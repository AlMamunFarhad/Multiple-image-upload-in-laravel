<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\TempImage;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
        ]);

        if ($validator->passes()) {

            $product = new Product();
            $product->name = $request->name;
            $product->price = $request->price;
            $product->save();

            if (!empty($request->image_id)) {
                $caption = $request->caption;
                foreach ($request->image_id as $key => $image_id) {

                    $temp_image = TempImage::find($image_id);
                    $extArray = explode('.', $temp_image->name);
                    $ext = last($extArray);

                    $product_image = new ProductImage();
                    $product_image->product_id = $product->id;
                    $product_image->name = 'NULL';
                    $product_image->caption = $caption[$key];
                    $product_image->save();

                    $new_image_name = $product_image->id . '.' . $ext;
                    $product_image->name = $new_image_name;
                    $product_image->save();

                    // First thumbnail
                    $source_path = public_path('uploads/temp/' . $temp_image->name);
                    $dest_path = public_path('uploads/products/small/' . $new_image_name);
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($source_path);
                    $image->resize(300, 200);
                    $image->save($dest_path);

                    // Second thumbnail
                    $source_path = public_path('uploads/temp/' . $temp_image->name);
                    $dest_path = public_path('uploads/products/large/' . $new_image_name);
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($source_path);
                    $image->resize(300, 200);
                    $image->save($dest_path);
                }

                $request->session()->flash('success','Product update successfully.');

                return response()->json([
                     'status' => true,
                     'message' => 'Product updated successfully.',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($id, Request $request)
    {
        $product = Product::find($id);

        if ($product == null) {
            return redirect()->route('products');
        }

        $product_image = ProductImage::where('product_id', $product->id)->get();
        $data['product'] = $product;
        $data['product_image'] = $product_image;

        return view('products.edit', $data);
    }

    public function update($product_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
        ]);

        $product = Product::find($product_id);
        if ($product == null) {
            return response()->json([
                'status' => false,
                'notFound' => true,
            ]);
        }

        if ($validator->passes()) {

            $product->name = $request->name;
            $product->price = $request->price;
            $product->save();

            if (!empty($request->image_id)) {
                $caption = $request->caption;
                foreach ($request->image_id as $key => $image_id) {

                    $product_image =  ProductImage::find($image_id);
                    $product_image->caption = $caption[$key];
                    $product_image->save();

                }
            }

            $request->session()->flash('success','Product update successfully.');

            return response()->json([
                 'status' => true,
                 'message' => 'Product updated successfully.',
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }


}
