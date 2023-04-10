<?php

namespace Stepanenko3\NovaMediaField\Traits;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait MediaHasConversionsTrait
{
    public function conversionOnIndex(string $conversion): self
    {
        return $this->withMeta([
            'conversionOnIndex' => $conversion,
        ]);
    }

    public function conversionOnDetail(string $conversion): self
    {
        return $this->withMeta([
            'conversionOnDetail' => $conversion,
        ]);
    }

    public function conversionOnForm(string $conversion): self
    {
        return $this->withMeta([
            'conversionOnForm' => $conversion,
        ]);
    }

    public function conversionOnPreview(string $conversion): self
    {
        return $this->withMeta([
            'conversionOnPreview' => $conversion,
        ]);
    }

    public function getConversionUrls(Media $media): array
    {
        return [
            'original' => $media->getFullUrl(),
            'index' => $media->getFullUrl($this->meta['conversionOnIndex'] ?? ''),
            'detail' => $media->getFullUrl($this->meta['conversionOnDetail'] ?? ''),
            'form' => $media->getFullUrl($this->meta['conversionOnForm'] ?? ''),
            'preview' => $media->getFullUrl($this->meta['conversionOnPreview'] ?? ''),
        ];
    }

    public function getTemporaryConversionUrls(Media $media): array
    {
        return [
            'original' => $media->getTemporaryUrl($this->secureUntil),
            'index' => $media->getTemporaryUrl($this->secureUntil, $this->meta['conversionOnIndexView'] ?? ''),
            'detai;' => $media->getTemporaryUrl($this->secureUntil, $this->meta['conversionOnDetailView'] ?? ''),
            'form' => $media->getTemporaryUrl($this->secureUntil, $this->meta['conversionOnForm'] ?? ''),
            'preview' => $media->getTemporaryUrl($this->secureUntil, $this->meta['conversionOnPreview'] ?? ''),
        ];
    }
}
