<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_saved_without_optional_image_or_description(): void
    {
        $response = $this->post(route('products.store'), [
            'name' => 'Test product',
            'price' => 12500,
            'stock' => 4,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Test product',
            'price' => 12500,
            'stock' => 4,
            'image' => null,
            'description' => null,
        ]);
    }

    public function test_uploaded_product_image_is_stored(): void
    {
        Storage::fake('public');

        $response = $this->post(route('products.store'), [
            'name' => 'Product with image',
            'price' => 25000,
            'stock' => 2,
            'image' => UploadedFile::fake()->image('product.jpg'),
        ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::where('name', 'Product with image')->firstOrFail();
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_product_can_be_edited_without_replacing_its_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/current.jpg', 'image contents');
        $product = Product::create([
            'name' => 'Original product',
            'description' => 'Original description',
            'price' => 1000,
            'stock' => 2,
            'image' => 'products/current.jpg',
        ]);

        $this->get(route('products.edit', $product))
            ->assertOk()
            ->assertSee('Original product');

        $response = $this->put(route('products.update', $product), [
            'name' => 'Updated product',
            'description' => 'Updated description',
            'price' => 2500,
            'stock' => 5,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated product',
            'description' => 'Updated description',
            'price' => 2500,
            'stock' => 5,
            'image' => 'products/current.jpg',
        ]);
        Storage::disk('public')->assertExists('products/current.jpg');
    }

    public function test_editing_product_can_replace_its_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/current.jpg', 'old image');
        $product = Product::create([
            'name' => 'Product with image',
            'price' => 1000,
            'stock' => 1,
            'image' => 'products/current.jpg',
        ]);

        $this->put(route('products.update', $product), [
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'image' => UploadedFile::fake()->image('replacement.jpg'),
        ])->assertRedirect(route('products.index'));

        $product->refresh();
        $this->assertNotSame('products/current.jpg', $product->image);
        Storage::disk('public')->assertExists($product->image);
        Storage::disk('public')->assertMissing('products/current.jpg');
    }

    public function test_product_can_be_deleted_with_its_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/to-delete.jpg', 'image contents');
        $product = Product::create([
            'name' => 'Product to delete',
            'price' => 1000,
            'stock' => 1,
            'image' => 'products/to-delete.jpg',
        ]);

        $this->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('status', 'Product deleted successfully.');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('products/to-delete.jpg');
    }

    public function test_product_list_uses_bootstrap_modal_for_delete_confirmation(): void
    {
        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('deleteProductModal')
            ->assertSee('data-bs-toggle="modal"', false)
            ->assertDontSee("confirm('Hapus produk ini?')", false);
    }
}
