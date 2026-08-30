<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\VestiSyncService;
use App\Contracts\ErpAdapterInterface;
use App\DTOs\ProductDTO;
use App\DTOs\VariationDTO;
use Illuminate\Support\Facades\Http;
use Mockery;

class VestiSyncServiceTest extends TestCase
{
    public function test_should_sync_products_to_vesti_api_correctly(): void
    {
        Http::fake([
            '*' => Http::response(['status' => 'success'], 201),
        ]);

        $fakeProduct = new ProductDTO(
            reference: '99999',
            name: 'TESTE UNITÁRIO',
            description: 'Produto de teste',
            price: 120.50,
            price_promotional: 100.00,
            composition: '100% Algodão',
            brand: 'Marca Teste',
            variations: [
                new VariationDTO(
                    sku: '99999_M_AZUL',
                    color: 'AZUL',
                    size: 'M',
                    stock: 50,
                    unit_type: 'UN',
                    ordering: 1
                )
            ]
        );

        $mockAdapter = Mockery::mock(ErpAdapterInterface::class);
        
        $mockAdapter->shouldReceive('getProducts')
                    ->once()
                    ->andReturn([$fakeProduct]);

        $service = new VestiSyncService();
        $service->sync($mockAdapter);

        Http::assertSent(function ($request) {
            $data = $request->data();
            
            if (!isset($data['products']) || empty($data['products'])) {
                return false;
            }

            $productPayload = $data['products'][0];

            return $productPayload['code'] === '99999'
                && $productPayload['name'] === 'TESTE UNITÁRIO'
                && $productPayload['brand'] === 'Marca Teste'
                && $productPayload['variations'][0]['sku'] === '99999_M_AZUL'
                && $productPayload['variations'][0]['size'] === 'M';
        });
    }
}