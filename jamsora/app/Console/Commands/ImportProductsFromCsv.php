<?php

namespace App\Console\Commands;

use App\Services\ProductImportService;
use Illuminate\Console\Command;

class ImportProductsFromCsv extends Command
{
    protected $signature = 'products:import {file=database/data/products.csv : Path to CSV file} {--fresh : Truncate existing products before import}';

    protected $description = 'Import products from a CSV file (Google Sheets export)';

    public function __construct(private readonly ProductImportService $importer)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $path = $this->argument('file');

        try {
            $result = $this->importer->importFromPath($path, $this->option('fresh'));
        } catch (\InvalidArgumentException|\RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('Existing products were removed before import.');
        }

        $this->info("Imported {$result['imported']} products from {$path}.");

        return self::SUCCESS;
    }
}
