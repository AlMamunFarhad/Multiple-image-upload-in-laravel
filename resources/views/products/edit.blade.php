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
    <style>
        .card {
            position: relative;
        }

        .card .danger {
            position: absolute;
            right: -15px;
            top: -10px;
            border-radius: 50%;
            height: 35px;
            width: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>

    <div class="container my-5">
        @if (Session::has('success'))
            <div class="bg-success text-white">{{ Session::get('success') }}</div>
        @endif
        @if (Session::has('error'))
            <div class="bg-success text-white">{{ Session::get('error') }}</div>
        @endif
        <form action="" name="product" id="product" method="post">
            <div class="card w-100 shadow-lg">
                <h2 class="card-header text-center py-4">Multiple image upload</h2>
                <div class="row g-3 card-body">
                    <h3 class="text-center">Update Product</h3>
                    <div class="col">
                        <input value="{{ $product->name }}" type="text" class="form-control" name="name"
                            id="name" placeholder="Name" aria-label="name">
                        <p></p>
                    </div>
                    <div class="col">
                        <input type="text" value="{{ $product->price }}" class="form-control" name="price"
                            id="price" placeholder="Price" aria-label="price">
                        <p></p>
                    </div>
                    <div class="form-contorl">
                        <div id="image" class="dropzone dz-clickable">
                            <div class="dz-message needsclick">
                                <br>Drop files here or click to upload. <br> <br>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-success d-flex" width="100" type="submit">Update</button>
                    </div>
                </div>
            </div>
            <div class="row mt-5" id="image-wrapper">
                @if ($product_image->isNotEmpty())
                    @foreach ($product_image as $image)
                        <div class="col-md-4 shadow-lg mb-4 rounded" id="product-image-row-{{ $image->id }}">
                            <div class="card">
                                <a href="" onclick="deleteImage({{ $image->id }});"
                                    class="btn btn-circle btn-danger danger">x</a>
                                <div>
                                    <img src="{{ asset('uploads/products/small/' . $image->name) }}" alt=""
                                        class="w-100 h-50">
                                </div>
                                <div class="card-body">
                                    <input type="text" name="caption[]" value="{{ $image->caption }}"
                                        class="form-control mb-3">
                                    <input type="hidden" name="image_id[]" value="{{ $image->id }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </form>
    </div>

    <script src="{{ asset('assets/js/jquery-3.6.4.min.js') }}"></script>
    <script src="{{ asset('assets/js/dropzone.min.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;
        let product_id = {{ $product->id }};
        const dropzone = $("#image").dropzone({
            uploadprogress: function(file, progress, bytesSent) {
                $("button[type=submit]").prop('disabled', true);
            },

            url: " {{ route('product.imagas.create') }}",
            params: {
                product_id: product_id
            },
            maxFiles: 10,
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(file, response) {
                $("button[type=submit]").prop('disabled', false);
                $("#image_id").val(response.image_id);
                let html = `<div class="col-md-4 shadow-lg mb-5" id="product-image-row-${response.image_id}">
                    <div class="card image-card">
                        <a href="#" onclick="deleteImage(${response.image_id});" class="btn btn-circle btn-danger danger">X</a>
                      <div>
                      <img src="${response.imagePath}" alt="" class="w-100">
                      </div>
                      <div class="card-body">
                      <input type="text" name="caption[]" value="" class="form-control mb-3">
                      <input type="hidden" name="image_id[]"  value="${response.image_id}" class="form-control mt-3">
                      </div>
                      </div>
                </div>`;

                $("#image-wrapper").append(html);
                $("button[type=submit]").prop('disabled', false);
                this.removeFile(file);
            }
        });

        $("#product").submit(function(e) {
            e.preventDefault();
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: "{{ route('update.product', $product->id) }}",
                data: $(this).serializeArray(),
                method: 'post',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response.status == true) {
                        window.location.href = "{{ route('products') }}";
                    } else {
                        let errors = response.errors
                        if (errors.name) {
                            $('#name').addClass('is-invalid').siblings("p").addClass('invalid-feedback')
                                .html(errors.name);

                        } else {
                            $('#name').removeClass('is-invalid').siblings("p").removeClass(
                                'invalid-feedback').html("");
                        }
                        if (errors.price) {
                            $('#price').addClass('is-invalid').siblings("p").addClass(
                                'invalid-feedback').html(errors.name);

                        } else {
                            $('#price').removeClass('is-invalid').siblings("p").removeClass(
                                'invalid-feedback').html("");
                        }
                    }
                }
            })
        });

        function deleteImage(id) {
            if (confirm('Are you sure you want to delete ?')) {
                
                let URL = "{{ route('product.imagas.delete', 'ID') }}";
                newURL = URL.replace('ID', id);
                $("#product-image-row-" + id).remove();

                $.ajax({
                    url: newURL,
                    data: {},
                    method: 'delete',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    success: function(response) {
                        window.location.href = "{{ route('edit.product', $product->id) }}";
                    }
                });
            }
        }
    </script>

</body>

</html>


</body>

</html>
