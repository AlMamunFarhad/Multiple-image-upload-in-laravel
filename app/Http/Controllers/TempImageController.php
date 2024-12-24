<?php

namespace App\Http\Controllers;

use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TempImageController extends Controller
{
    
    public function store(Request $request)
    {
        if(!empty($request->image)){
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();

            $temp_image = new TempImage();
            $temp_image->name = 'NULL';
            $temp_image->save();
            
            $image_name = $temp_image->id .'.'. $ext;

            $temp_image->name = $image_name;
            $temp_image->save();

            $image->move(public_path('uploads/temp/'), $image_name);

            // $source_path = public_path('uploads/temp/' . $image_name);
           
                 // Create a small size image
                 $source_path = public_path('uploads/temp/' . $image_name);
                 $dest_path = public_path('uploads/temp/thumb/'. $image_name);
                 $manager = new ImageManager(Driver::class);
                 $image = $manager->read($source_path);
     
                 // crop the best fitting 1:1 ratio (200x200) and resize to 200x200 pixel
                 $image->cover(320, 300);
                 $image->save($dest_path);
                //  $image->toPng()->save(public_path('uploads/temp/thumb/' . $image_name));

            return response()->json([
               'status' => true,
               'image_id' => $temp_image->id,
               'name' => $image_name,
               'imagePath' => asset('uploads/temp/thumb/'.$image_name),
            ]);
            
        }
    }
}
