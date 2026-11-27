@extends('templates.layout')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tabulator-tables@6.6.1/dist/css/tabulator_bootstrap5.min.css" crossorigin="anonymous" />
@endpush
 
@section('content')
        @if (session('status'))
          <div class="container-fluid pt-3">
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
          </div>
        @endif
        @if ($errors->any())
          <div class="container-fluid pt-3">
            <div class="alert alert-danger" role="alert">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif
<!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Starter Page</h1>
              </div>
              <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Starter Page</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-12">
                <!--begin::Card-->
                <div class="card">
                  <div class="card-header">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
                      <i class="bi bi-plus-circle me-1" aria-hidden="true"></i>
                      Create Product
                    </button>
                    <div class="card-tools">
                      <button
                        type="button"
                        class="btn btn-tool"
                        data-lte-toggle="card-collapse"
                        aria-label="Collapse card"
                      >
                        <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                        <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                      </button>
                      <button
                        type="button"
                        class="btn btn-tool"
                        data-lte-toggle="card-remove"
                        aria-label="Remove card"
                      >
                        <i class="bi bi-x-lg"></i>
                      </button>
                    </div>
                  </div>
                  <!-- /.card-header -->

                  <div class="card-body">
                <div class="d-flex gap-2 mb-3">
                  <button id="export-csv" type="button" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-filetype-csv me-1" aria-hidden="true"></i>
                    Export CSV
                  </button>
                  <button id="export-json" type="button" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-filetype-json me-1" aria-hidden="true"></i>
                    Export JSON
                  </button>
                  <button id="print-table" type="button" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-printer me-1" aria-hidden="true"></i>
                    Print
                  </button>
                </div>
                <div id="product-table" aria-label="Products"></div>
                <div
                  id="table-pagination"
                  class="d-flex flex-wrap gap-2 justify-content-end mt-2"
                ></div>
              </div>
                  <!-- /.card-body -->
                  <div class="card-footer">The footer of the card</div>
                  <!-- /.card-footer -->
                </div>
                <!--end::Card-->
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
@endsection

@section('modal')
    @include('products.modal')
    <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="deleteProductModalLabel">Konfirmasi Hapus</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            Apakah Anda yakin ingin menghapus produk ini?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <form id="deleteProductForm" method="POST" action="#">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger">Hapus</button>
            </form>
          </div>
        </div>
      </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tabulator-tables@6.6.1/dist/js/tabulator.min.js" crossorigin="anonymous"></script>
    <script>
      const statusBadge = (cell) => {
        const value = cell.getValue();
        const map = { 
          Active: 'success', 
          Invited: 'info', 
          Suspended: 'secondary' 
        };
        const color = map[value] || 'secondary';
        return `<span class="badge text-bg-${color}">${value}</span>`;
      };

      document.addEventListener('DOMContentLoaded', () => {
        
      const table = new Tabulator('#product-table', {
        ajaxURL: "{{ route('products.api') }}",
        layout: 'fitColumns',
        filterMode: 'local',
        pagination: 'local',
        paginationElement: document.getElementById('table-pagination'),
        paginationSize: 10,
        paginationSizeSelector: [10, 25, 50, 100],
        movebleColumns: true,
        columns: [{
          title: 'ID',
          field: 'id',
          width: 80,
          headerSort: 'true'
        },
        {
          title: 'Product Name',
          field: 'name',
        },
        {
          title: 'Price',
          field: 'price'
        },
        {
          title: 'Stock',
          field: 'stock',
          width: 120
        },
        {
          title: 'Aksi',
          field: 'id',
          formatter: function(cell) {
            var id = cell.getValue();
            var editUrl = "{{ url('products') }}/" + id + "/edit";
            var deleteUrl = "{{ url('products') }}/" + id;

            return `<a href="${editUrl}" class="btn btn-success">Edit</a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProductModal" data-delete-url="${deleteUrl}">Hapus</button>`;
          },
          headerSort: false,
          borderless: true,

          width: 200,
          hozAlign: 'center',
        },

      ],
      });

      document.getElementById('deleteProductModal').addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        document.getElementById('deleteProductForm').action = button.getAttribute('data-delete-url');
      });

      table.on('tableBuilt', () => {
        const grid = document.getElementById('product-table');
        grid.removeAttribute('aria-owns');
        grid.querySelector('.tabulator-tableholder').setAttribute('role', 'rowgroup');
        grid.querySelector('.tabulator-table').setAttribute('role', 'presentation');
      });

      table.on('renderComplete', () => {
        document.querySelectorAll('#table-pagination button').forEach((button) => {
          button.classList.add('btn', 'btn-sm', 'btn-outline-secondary');
        });
      });

      document.getElementById('table-filter').addEventListener('input', (e) => {
        const value = e.target.value;
        if (value) {
          table.setFilter(
            [{
              field: 'name',
              type: 'like',
              value: value
            }]);
        } else {
          table.clearFilter();
        }
      });

      document
      .getElementById('export-csv')
      .addEventListener('click', () => table.download('csv', 'users.csv'));
      document
      .getElementById('export-json')
      .addEventListener('click', () => table.download('json', 'users.json'));
      document
      .getElementById('print-table')
      .addEventListener('click', () => table.print(false, true));

      });
    </script>
@endpush