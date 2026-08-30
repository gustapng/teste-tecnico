<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class ProductDTO
{
    /**
     * @param VariationDTO[] $variations
     */
    public function __construct(
        public string $reference,
        public string $name,
        public ?string $description,
        public ?float $price,
        public ?float $price_promotional,
        public ?string $composition,
        public ?string $brand,
        public array $variations = []
    ) {}
}