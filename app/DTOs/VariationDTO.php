<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class VariationDTO
{
    public function __construct(
        public string $sku,
        public string $color,
        public string $size,
        public int $stock,
        public string $unit_type,
        public int $ordering,
    ) {}
}