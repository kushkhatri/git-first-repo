<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCsvImportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'admin', 'slug' => 'admin']);
        $this->admin = User::factory()->create([
            'role_id' => $role->id,
            'email' => 'admin@test.com',
        ]);
    }

    public function test_admin_can_view_import_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.products.import'))
            ->assertOk()
            ->assertSee('Import products from CSV');
    }

    public function test_admin_can_import_csv_file(): void
    {
        Storage::fake('local');

        $csv = "name,sku,categories,regular_price,sale_price,stock,published,in_stock\n";
        $csv .= "Test Ruby Gem,SKU-RUBY-1,Ruby,500,450,1,1,1\n";

        $file = UploadedFile::fake()->createWithContent('products.csv', $csv);

        $response = $this->actingAs($this->admin)->post(route('admin.products.import.store'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', ['sku' => 'SKU-RUBY-1']);
        $this->assertDatabaseHas('categories', ['slug' => 'ruby']);
    }

    public function test_guest_cannot_import(): void
    {
        $file = UploadedFile::fake()->create('products.csv', 100);

        $this->post(route('admin.products.import.store'), ['csv_file' => $file])
            ->assertRedirect(route('login'));
    }
}
