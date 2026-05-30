<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\ProductImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

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

        if (! is_readable($path)) {
            $this->error("File not readable: {$path}");
            $this->line('Export your sheet: File → Download → CSV, then save as database/data/products.csv');

            return self::FAILURE;
        }

        $peek = file_get_contents($path, false, null, 0, 200);
        if ($peek && (str_contains($peek, '<!DOCTYPE') || str_contains($peek, '<html'))) {
            $this->error('Invalid CSV: file looks like HTML. Upload the real .csv from Google Sheets.');

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            Product::query()->forceDelete();
            $this->warn('Existing products removed.');
        }

        $handle = fopen($path, 'r');
        $headers = array_map(fn ($h) => Str::slug(trim($h), '_'), fgetcsv($handle) ?: []);

        if (empty($headers)) {
            $this->error('CSV has no header row.');

            return self::FAILURE;
        }

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row)) === 0) {
                continue;
            }

            $data = array_combine($headers, array_pad($row, count($headers), null));
            if ($data === false) {
                continue;
            }

            if ($this->importer->importRow($data)) {
                $count++;
            }
        }

        fclose($handle);
        $this->info("Imported {$count} products from {$path}.");

        return self::SUCCESS;
    }
}
