<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="_token" content="{{ csrf_token() }}">
    <title>Laravel Multiple Image Uploads</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dropzone.min.css') }}">
</head>

<body>

    <div class="container my-5">
          <form action="" name="product" id="product" method="post">
            <div class="card w-100 shadow-lg">
                <h2 class="card-header text-center py-4">Multiple image upload</h2>
                <div class="row g-3 card-body px-5 py-4">
                    <h3 class="text-center">Products</h3>

                    @if (Session::has('success'))
                        <div class="bg-success text-white">{{ Session::get('success') }}</div>
                    @endif
                    @if (Session::has('error'))
                        <div class="bg-success text-white">{{ Session::get('error') }}</div>
                    @endif
                    <div>
                        <a href="{{ route('create.product') }}" class="btn btn-primary">Create</a>
                    </div>
                    <table class="table table-striped table-hover">
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Edit</th>
                        </tr>
                        @foreach ($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->price }}</td>
                            <td><a href="{{ route('edit.product', $product->id) }}" class="btn btn-warning">Edit</a></td>
                        </tr>
                        @endforeach
                    </table>
                    {{ $products->links() }}
                </div>
            </div>
            <div class="row mt-5" id="image-wrapper"></div>
          </form>
    </div>

    <script src="{{ asset('assets/js/jquery-3.6.4.min.js') }}"></script>
    <script src="{{ asset('assets/js/dropzone.min.js') }}"></script>

</body>

</html>
