<?php

namespace App\Http\Controllers;

use App\Models\TempImage;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductImageController extends Controller
{
    public function store(Request $request)
    {
        if(!empty($request->image)){
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();

            $product_image = new ProductImage();
            $product_image->name = 'NULL';
            $product_image->product_id = $request->product_id;
            $product_image->save();
            
            $image_name = $product_image->id .'.'. $ext;

            $product_image->name = $image_name;
            $product_image->save();
           
                    // First thumbnail
                    $source_path = $image->getPathName();
                    $dest_path = public_path('uploads/products/small/' . $image_name);
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($source_path);
                    $image->resize(300, 200);
                    $image->save($dest_path);

                    // Second thumbnail
                    $dest_path = public_path('uploads/products/large/' . $image_name);
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($source_path);
                    $image->resize(300, 200);
                    $image->save($dest_path);

            return response()->json([
               'status' => true,
               'image_id' => $product_image->id,
               'name' => $image_name,
               'imagePath' => asset('uploads/products/small/'.$image_name),
            ]);
            
        }
    }

    public function destroy($image_id, Request $request)
    {
        $image = ProductImage::find($image_id);
        if(empty($image)){
           $request->session()->flash('error','Image not found.');

           return response()->json([
                  'status' => false,
           ]);
        }

        File::delete(public_path('uploads/products/large/'.$image->name));
        File::delete(public_path('uploads/products/small/'.  $image->name));

        $image->delete();
        $request->session()->flash('success', 'Image deleted successfully.');
        return response()->json([
            'status' => true,
        ]);
    }
}
