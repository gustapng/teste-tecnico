<?php

namespace App\Services;

use App\Contracts\ErpAdapterInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VestiSyncService
{
    public function sync(ErpAdapterInterface $adapter): bool
    {
        $products = $adapter->getProducts();

        $baseUrl = config('services.vesti.url');
        $companyId = config('services.vesti.company_id');
        $apiToken = config('services.vesti.token');

        if (empty($baseUrl)) {
            $baseUrl = 'https://integracao-hml.meuvesti.com/api';
        }
        if (empty($companyId)) {
            $companyId = 'ID_NAO_INFORMADO';
        }

        $apiUrl = "{$baseUrl}/v1/products/company/{$companyId}";
        $chunks = array_chunk($products, 100);
        
        $allSuccessful = true;

        foreach ($chunks as $chunk) {
            $payloadProducts = [];

            foreach ($chunk as $product) {
                $payloadProducts[] = [
                    'integration_id' => $product->reference,
                    'code' => $product->reference,
                    'name' => $product->name,
                    'active' => true,
                    'description' => $product->description ?? '',
                    'composition' => $product->composition ?? '',
                    'brand' => $product->brand ?? '',
                    'price' => $product->price ?? 0,
                    'promotion' => !empty($product->price_promotional),
                    'price_promotional' => $product->price_promotional ?? 0,
                    'variations' => array_map(function ($variation) {
                        return [
                            'sku' => $variation->sku,
                            'size' => $variation->size,
                            'color' => $variation->color,
                            'quantity' => $variation->stock,
                            'unit_type' => $variation->unit_type,
                            'order' => $variation->ordering,
                        ];
                    }, $product->variations)
                ];
            }

            $payload = ['products' => $payloadProducts];

            try {
                $response = Http::withHeaders([
                    'apikey' => $apiToken,
                    'Content-Type' => 'application/json'
                ])
                ->withoutVerifying()
                ->post($apiUrl, $payload);
                
                if ($response->successful()) {
                    Log::info("Lote de " . count($chunk) . " produtos cadastrado com sucesso na Vesti.");
                } else {
                    Log::error("Erro na Vesti: {$response->body()}");
                    $allSuccessful = false;
                }
            } catch (\Exception $e) {
                Log::error("Falha ao comunicar com API Vesti: " . $e->getMessage());
                $allSuccessful = false;
            }
        }
        return $allSuccessful; 
    }
}