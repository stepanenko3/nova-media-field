<?php

namespace Stepanenko3\NovaMediaField\Traits;

use Laravel\Nova\Fields\Field;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait MediaCustomPropertiesTrait
{
    protected $customPropertiesFields = [];

    protected $customProperties = [];

    public function customPropertiesFields(array $customPropertiesFields): self
    {
        $this->customPropertiesFields = collect($customPropertiesFields);

        return $this->withMeta(compact('customPropertiesFields'));
    }

    public function customProperties(array $customProperties): self
    {
        $this->customProperties = $customProperties;

        return $this;
    }

    protected function customPropertiesValidationRules(string $prefix): array
    {
        if (empty($this->customPropertiesFields)) {
            return [];
        }

        return $this->customPropertiesFields
            ->mapWithKeys(fn (Field $field) => [
                $prefix . '.' . $field->attribute => $field->rules,
            ])
            ->toArray();
    }

    protected function updateFilesCustomProperties(array $payload, HasMedia $model, string $attribute, callable $next)
    {
        $modelMedia = $model->getMedia($attribute);

        foreach ($payload as $fileData) {
            if (empty($fileData['custom_properties'] ?? [])) {
                continue;
            }

            if ($media = $modelMedia->where('id', $fileData['id'])->first()) {
                $this->fillMediaCustomProperties(
                    $media,
                    $fileData['custom_properties'],
                );
            }
        }

        return $next($payload);
    }

    private function fillMediaCustomProperties(Media $media, array $customProperties): void
    {
        // prevent overriding the custom properties set by other processes like generating convesions
        $media->refresh();

        /** @var Field $field */
        foreach ($this->customPropertiesFields as $field) {
            if (isset($customProperties[$field->attribute])) {
                $media->setCustomProperty($field->attribute, $customProperties[$field->attribute]);
            } else {
                $media->forgetCustomProperty($field->attribute);
            }
        }

        $media->save();
    }
}
