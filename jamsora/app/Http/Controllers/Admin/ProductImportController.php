<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductCsvImportRequest;
use App\Services\ProductImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class ProductImportController extends Controller
{
    public function __construct(private readonly ProductImportService $importer) {}

    public function create(): View
    {
        $maxMb = round((int) config('jamsora.import.max_upload_kb', 102400) / 1024, 1);

        return view('admin.products.import', compact('maxMb'));
    }

    public function store(ProductCsvImportRequest $request): RedirectResponse
    {
        $this->raiseUploadLimits();

        $file = $request->file('csv_file');
        $directory = storage_path('app/imports');
        File::ensureDirectoryExists($directory);

        $storedName = now()->format('Y-m-d_His').'_'.preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $path = $file->move($directory, $storedName)->getPathname();

        try {
            $result = $this->importer->importFromPath($path, $request->boolean('fresh'));
        } catch (\InvalidArgumentException|\RuntimeException $e) {
            File::delete($path);

            return back()->withInput()->with('error', $e->getMessage());
        }

        $archivePath = database_path('data/products.csv');
        File::ensureDirectoryExists(dirname($archivePath));
        File::copy($path, $archivePath);

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Imported {$result['imported']} products from your CSV (categories assigned from the Categories column).");
    }

    private function raiseUploadLimits(): void
    {
        @ini_set('memory_limit', '512M');
        @ini_set('max_execution_time', '600');
        @ini_set('post_max_size', '128M');
        @ini_set('upload_max_filesize', '128M');
    }
}
