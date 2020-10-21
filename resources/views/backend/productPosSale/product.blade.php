<div class="modal" id="modal-produk" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"> &times; </span> </button>
                <h3 class="modal-title">Cart Product</h3>
            </div>

            <div class="modal-body">
                <table class="table table-striped tabel-produk">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Purchase Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $data)
                            <tr>
                            <th>{{ $data->product_code }}</th>
                            <th>{{ $data->name }}</th>
                            <th>Rp. {{ number_format(200) }}</th>
                            <th><a onclick="selectItem({{ $data->product_code }})" class="btn btn-primary"><i class="fa fa-check-circle"></i> Select</a></th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
