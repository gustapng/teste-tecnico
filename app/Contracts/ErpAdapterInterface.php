<?php

namespace App\Contracts;

interface ErpAdapterInterface
{
    /**
     * Deve retornar uma lista de produtos já convertidos para o nosso DTO padrão.
     *
     * @return \App\DTOs\ProductDTO[]
     */
    public function getProducts(): array;
}