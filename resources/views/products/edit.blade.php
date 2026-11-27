@extends('templates.layout')

@section('content')
  <div class="app-content-header">
    <div class="container-fluid">
      <h1 class="mb-0 fs-3">Edit Product</h1>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      @if ($errors->any())
        <div class="alert alert-danger" role="alert">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="card">
        <div class="card-body">
          <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label for="name" class="form-label">Product Name</label>
              <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
            </div>

            <div class="mb-3">
              <label for="price" class="form-label">Price</label>
              <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}" min="0" step="1" required>
            </div>

            <div class="mb-3">
              <label for="stock" class="form-label">Stock</label>
              <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0" step="1" required>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" name="description">{{ old('description', $product->description) }}</textarea>
            </div>

            @if ($product->image)
              <div class="mb-3">
                <p class="form-label">Current Image</p>
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 180px;">
              </div>
            @endif

            <div class="mb-3">
              <label for="image" class="form-label">Replace Image (optional)</label>
              <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>

            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
