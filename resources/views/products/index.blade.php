<!DOCTYPE html>
<html>
<head>
    <title>Product Page</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2>Product Form</h2>
                <form id="productForm">
                @csrf
                    <div class="form-group">
                        <label for="name">Product Name:</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity in stock:</label>
                        <input type="number" class="form-control" id="quantity" name="quantity">
                    </div>
                    <div class="form-group">
                        <label for="price">Price per item:</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <br>
                <h2>Products List</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Date Time Submitted</th>
                            <th>Total Value</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        @foreach($products as $product)
                        <tr data-id="{{ $product->id }}">
                            <td class="name">{{ $product->name }}</td>
                            <td class="quantity">{{ $product->quantity }}</td>
                            <td class="price">{{ $product->price }}</td>
                            <td>{{ $product->created_at }}</td>
                            <td class="total">{{ $product->quantity * $product->price }}</td>
                            <td>
                                <button class="btn btn-info edit-button">Edit</button>
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="4"><strong>Total</strong></td>
                            <td id="totalSum"></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function updateTotalSum() {
            let totalSum = 0;
            $('#productTableBody tr').each(function() {
                const total = parseFloat($(this).find('.total').text());
                if (!isNaN(total)) {
                    totalSum += total;
                }
            });
            $('#totalSum').text(totalSum);
        }

        updateTotalSum();

        $('#productForm').on('submit', function(e) {
            e.preventDefault();
            const id = $(this).attr('data-id');
            const method = id ? 'PUT' : 'POST';
            const url = id ? `/products/${id}` : '/products';
            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: function(product) {
                    if (method === 'POST') {
                        const newRow = `<tr data-id="${product.id}">
                            <td class="name">${product.name}</td>
                            <td class="quantity">${product.quantity}</td>
                            <td class="price">${product.price}</td>
                            <td>${product.created_at}</td>
                            <td class="total">${product.quantity * product.price}</td>
                            <td>
                                <button class="btn btn-info edit-button">Edit</button>
                            </td>
                        </tr>`;
                        $('#productTableBody').prepend(newRow);
                    } else {
                        const row = $(`#productTableBody tr[data-id="${product.id}"]`);
                        row.find('.name').text(product.name);
                        row.find('.quantity').text(product.quantity);
                        row.find('.price').text(product.price);
                        row.find('.total').text(product.quantity * product.price);
                    }
                    updateTotalSum();
                    $('#productForm')[0].reset();
                    $('#productForm').removeAttr('data-id');
                }
            });
        });

        $('#productTableBody').on('click', '.edit-button', function() {
            const row = $(this).closest('tr');
            const id = row.data('id');
            const name = row.find('.name').text();
            const quantity = row.find('.quantity').text();
            const price = row.find('.price').text();
            
            $('#name').val(name);
            $('#quantity').val(quantity);
            $('#price').val(price);
            $('#productForm').attr('data-id', id);
        });
    });
</script>
</body>
</html>