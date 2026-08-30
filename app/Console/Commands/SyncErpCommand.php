<?php

namespace App\Console\Commands;

use App\Adapters\XptoAdapter;
use App\Adapters\XyzAdapter;
use App\Services\VestiSyncService;
use Illuminate\Console\Command;


class SyncErpCommand extends Command
{
    /**
     * Execute the console command.
     */
    
    protected $signature = 'erp:sync {origin : O nome do ERP (xpto, xyz)}';
    protected $description = 'Sincroniza os produtos de um ERP específico com a Vesti';

    public function handle(VestiSyncService $syncService)
    {
        $origin = $this->argument('origin');

        try {
            $adapter = \App\Adapters\ErpAdapterFactory::make($origin);
            
            $this->info("Lendo arquivos do ERP: " . strtoupper($origin) . "...");

            $isSuccess = $syncService->sync($adapter);

            if ($isSuccess) {
                $this->info("Sincronização finalizada com sucesso!");
                return self::SUCCESS;
            } else {
                $this->error("A sincronização finalizou com erros.");
                $this->line("Verifique o arquivo de log em storage/logs/laravel.log para mais detalhes.");
                return self::FAILURE;
            }
            
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
