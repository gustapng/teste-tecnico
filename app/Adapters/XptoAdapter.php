<?php

declare(strict_types=1);

namespace App\Adapters;

use App\DTOs\ProductDTO;
use App\DTOs\VariationDTO;

class XptoAdapter extends AbstractAdapter
{
    public function getProducts(): array
    {
        $produtosRaw = $this->loadJson(base_path('erpXpto/produtos-erp-xpto.json'));
        $variacoesRaw = $this->loadJson(base_path('erpXpto/variacoes-erp-xpto.json'));

        $variationsDto = [];
        foreach ($variacoesRaw as $var) {
            $variationsDto[] = new VariationDTO(
                sku: (string) ($var['sku'] ?? ''),
                color: (string) ($var['color'] ?? ''),
                size: (string) ($var['size'] ?? ''),
                stock: (int) ($var['quantity'] ?? 0),
                unit_type: (string) ($var['unit_measurement'] ?? 'UN'),
                ordering: (int) ($var['ordering'] ?? 1)
            );
        }

        $variationsGrouped = $this->groupVariationsByProduct($variationsDto);

        $productsDto = [];
        foreach ($produtosRaw as $prod) {
            $code = (string) $prod['code'];

            $productsDto[] = new ProductDTO(
                reference: $code,
                name: $prod['name'],
                description: $prod['description'],
                price: $this->parsePrice($prod['price'] ?? null),
                price_promotional: $this->parsePrice($prod['price_promotional'] ?? null),
                composition: $prod['composition'] ?? null,
                brand: $prod['brand'] ?? null,
                variations: $variationsGrouped[$code] ?? []
            );
        }

        return $productsDto;
    }
}