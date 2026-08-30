<?php

declare(strict_types=1);

namespace App\Adapters;

use App\Contracts\ErpAdapterInterface;
use Illuminate\Support\Facades\File;

abstract class AbstractAdapter implements ErpAdapterInterface
{
    protected function loadJson(string $path): array
    {
        if (!File::exists($path)) {
            return [];
        }

        $content = File::get($path);
        
        return json_decode($content, true) ?? [];
    }

    protected function parsePrice(mixed $price): ?float
    {
        if (empty($price)) {
            return null;
        }

        if (is_numeric($price)) {
            return (float) $price;
        }

        $price = str_replace(['R$', ' ', '.'], '', $price);
        $price = str_replace(',', '.', $price);

        return (float) $price;
    }

    protected function groupVariationsByProduct(array $variationsDto): array
    {
        $grouped = [];

        foreach ($variationsDto as $variation) {
            $productCode = explode('_', $variation->sku)[0];
            $grouped[$productCode][] = $variation;
        }

        return $grouped;
    }
}