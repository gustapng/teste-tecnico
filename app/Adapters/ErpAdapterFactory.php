<?php

declare(strict_types=1);

namespace App\Adapters;

use App\Contracts\ErpAdapterInterface;
use InvalidArgumentException;

class ErpAdapterFactory
{
    public static function make(string $origin): ErpAdapterInterface
    {
        return match (strtolower($origin)) {
            'xpto' => new XptoAdapter(),
            'xyz' => new XyzAdapter(),
            default => throw new InvalidArgumentException("ERP '{$origin}' não suportado ou inexistente."),
        };
    }
}