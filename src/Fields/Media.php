<?php

namespace Stepanenko3\NovaMediaField\Fields;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Stepanenko3\NovaMediaField\Traits\MediaCustomPropertiesTrait;
use Stepanenko3\NovaMediaField\Traits\MediaHasConversionsTrait;

class Media extends Field
{
    use MediaCustomPropertiesTrait;
    use MediaHasConversionsTrait;

    public $component = 'nova-media-field';

    public $meta = [
        'type' => 'media',
        'countOfImagesDisplayedOnIndex' => 2,
    ];

    protected $serializeMediaCallback;

    protected $collectionMediaRules = [];

    protected $singleMediaRules = [];

    protected Carbon $secureUntil;

    public function resolve(
        $resource,
        $attribute = null,
    ): void {
        $collection = $attribute ?? $this->attribute;

        $this->value = $resource
            ->getMedia($collection)
            ->map(fn ($media) => $this->serializeMedia(
                media: $media,
            ))
            ->values();

        if ($collection) {
            $this->checkCollectionIsMultiple(
                resource: $resource,
                collectionName: $collection,
            );
        }
    }

    public function temporary(
        Carbon $until,
    ): self {
        $this->secureUntil = $until;

        return $this;
    }

    public function rules(
        $rules,
    ): self {
        $this->collectionMediaRules = ($rules instanceof Rule || is_string($rules))
            ? func_get_args()
            : $rules;

        return $this;
    }

    public function singleMediaRules(
        $rules,
    ): self {
        $this->singleMediaRules = ($rules instanceof Rule || is_string($rules))
            ? func_get_args()
            : $rules;

        return $this;
    }

    public function serializeMediaUsing(
        callable $serializeMediaUsing,
    ): self {
        $this->serializeMediaCallback = $serializeMediaUsing;

        return $this;
    }

    public function getGeneratedConversionsUrls(
        mixed $media,
    ): array {
        return collect($media->generated_conversions)
            ->filter()
            ->keys()
            ->mapWithKeys(fn (string $conversion) => [
                $conversion => $media->getFullUrl(
                    conversionName: $conversion,
                ),
            ])
            ->toArray();
    }

    public function getMediaUrls(
        mixed $media,
    ): array {
        if (isset($this->secureUntil) && $this->secureUntil instanceof Carbon) {
            return $this->getTemporaryConversionUrls(
                media: $media,
            );
        }

        return $this->getConversionUrls(
            media: $media,
        );
    }

    public function serializeMedia(
        mixed $media,
    ): array {
        if ($this->serializeMediaCallback) {
            return call_user_func($this->serializeMediaCallback, $media);
        }

        return array_merge(
            $media->only([
                'id',
                'collection_name',
                'name',
                'mime_type',
                'file_name',
                'disk',
                'conversions_disk',
                'custom_properties',
                'size',
                'order_column',
                'created_at',
                'updated_at',
            ]),
            [
                'generated_conversions' => $this->getGeneratedConversionsUrls($media),
                'conversion' => $this->getMediaUrls($media),
            ],
        );
    }

    public function fileManager(
        bool $show = true,
    ): self {
        $this->withMeta([
            'fileManager' => $show,
        ]);

        return $this;
    }

    public function countOfImagesDisplayedOnIndex(
        int $count,
    ): self {
        $this->withMeta([
            'countOfImagesDisplayedOnIndex' => $count,
        ]);

        return $this;
    }

    protected function checkCollectionIsMultiple(
        mixed $resource,
        string $collectionName,
    ): void {
        $resource->registerMediaCollections();

        $isSingle = collect($resource->mediaCollections)
            ->where(
                key: 'name',
                operator: '=',
                value: $collectionName,
            )
            ->first()
            ->singleFile ?? false;

        $this->withMeta([
            'multiple' => !$isSingle,
        ]);
    }

    protected function fillAttributeFromRequest(
        NovaRequest $request,
        mixed $requestAttribute,
        mixed $model,
        mixed $attribute,
    ): void {
        $request->validate([
            $requestAttribute => $this->collectionMediaRules,
            $requestAttribute . '.*.id' => 'required|string',
            $requestAttribute . '.*.file' => $this->singleMediaRules,
            $requestAttribute . '.*.delete' => 'nullable',
            $requestAttribute . '.*.order' => 'nullable|numeric',
            $requestAttribute . '.*.custom_properties' => 'nullable|array',

            ...$this->customPropertiesValidationRules(
                prefix: $requestAttribute . '.*.custom_properties',
            ),
        ]);

        $requestData = Arr::where(
            array: Arr::get(
                $request->all($requestAttribute),
                $requestAttribute,
            ) ?: [],
            callback: fn ($fileData) => isset($fileData['id']),
        );

        $pipes = [
            fn ($payload, $next) => $this->deleteFiles(
                payload: $payload,
                model: $model,
                attribute: $attribute,
                next: $next,
            ),
            fn ($payload, $next) => $this->updateFilesCustomProperties(
                payload: $payload,
                model: $model,
                attribute: $attribute,
                next: $next,
            ),
            fn ($payload, $next) => $this->uploadFiles(
                payload: $payload,
                model: $model,
                attribute: $attribute,
                next: $next,
            ),
        ];

        if (!$request->isCreateOrAttachRequest()) {
            $pipes[] = fn ($payload, $next) => $this->reorderFiles(
                payload: $payload,
                model: $model,
                attribute: $attribute,
                next: $next,
            );
        }

        app(Pipeline::class)
            ->send($requestData)
            ->through($pipes)
            ->then(function ($request): void {
                //
            });
    }

    protected function deleteFiles(
        array $payload,
        mixed $model,
        string $attribute,
        callable $next,
    ) {
        $ids = collect($payload)
            ->filter(fn ($fileData) => $fileData['delete'] ?? false)
            ->pluck('id')
            ->toArray();

        $model
            ->media()
            ->whereIn('id', $ids)
            ->delete();

        $payload = Arr::where(
            array: $payload,
            callback: fn ($fileData) => !in_array($fileData['id'], $ids),
        );

        return $next($payload);
    }

    protected function uploadFiles(
        array $payload,
        mixed $model,
        string $attribute,
        callable $next,
    ) {
        $payload = collect($payload)
            ->mapWithKeys(function ($fileData) use ($model, $attribute) {
                if (!array_key_exists('file', $fileData)) {
                    return [
                        $fileData['id'] => $fileData,
                    ];
                }

                $media = $model
                    ->addMedia($fileData['file'])
                    ->withCustomProperties($fileData['custom_properties'] ?? [])
                    ->toMediaCollection($attribute);

                $fileData['id'] = $media->getKey();

                return [
                    $media->getKey() => $fileData,
                ];
            })
            ->toArray();

        return $next($payload);
    }

    protected function reorderFiles(
        array $payload,
        mixed $model,
        string $attribute,
        callable $next,
    ) {
        $sort = collect($payload)
            ->mapWithKeys(fn ($fileData) => [
                $fileData['order'] => $fileData['id'],
            ])
            ->sortKeys()
            ->values()
            ->toArray();

        SpatieMedia::setNewOrder(
            ids: $sort,
        );

        return $next($payload);
    }
}
