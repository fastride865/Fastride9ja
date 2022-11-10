<?php

namespace App\Traits;

use App\Models\Merchant;
use Auth;
use Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

trait ImageTrait
{

//    public function uploadImage($image, $dir = 'images', $merchant_id = null, $image_type = 'single', $is_banner = false, $banner = [])
//    {
//        $name = "";
//        if ($image_type == 'multiple') {
//            $file = $image; // its name of image
//        } else {
//            if (request()->hasFile($image)) {
//                $file = request()->file($image); // its name of image's field
//            }
//        }
//        $upload_path = \Config::get('custom.' . $dir);
//        $id = $merchant_id ? $merchant_id : get_merchant_id();
//        $merchant = Merchant::find($id);
//        $alias = $merchant->alias_name . $upload_path['path'];
//        if($is_banner){
//            $name = time() . "_" . uniqid() . "_" . $dir . '.' . $banner->extension;
//            $filePath = $alias . $name;
//            \Illuminate\Support\Facades\Storage::disk('s3')->put($filePath, $banner->image->__toString());
//        }else{
//            $name = time() . "_" . uniqid() . "_" . $dir . '.' . $file->getClientOriginalExtension();
//            $filePath = $alias . $name;
//            \Illuminate\Support\Facades\Storage::disk('s3')->put($filePath, file_get_contents($file));
//        }
//        return $name;
//    }


// s3 image upload
    public function uploadImage($image, $dir = 'images', $merchant_id = null, $image_type = 'single',$additional_req = [])
    {
        $name = "";
//        p($image);
        if($image_type =='multiple')
        {
            $file = $image; // its name of image
        }
        else{
            if (request()->hasFile($image)) {
                $file = request()->file($image); // its name of image's field
            }
        }
//
//        $extension = $file->getClientOriginalExtension();
//        $upload_path = \Config::get('custom.' . $dir);
//        $id = $merchant_id ? $merchant_id : get_merchant_id();
//        $merchant = Merchant::find($id);
//        $alias = $merchant->alias_name. $upload_path['path'];
//        $name = time() . "_" . uniqid() ."_". $dir.'.' . $extension;
//        $filePath = $alias . $name;
//
//        \Illuminate\Support\Facades\Storage::disk('s3')->put($filePath, $file);
////        p($name);

        if(!empty($file))
        {
            $extension = $file->getClientOriginalExtension();
            if(isset($additional_req['compress']) && $additional_req['compress'] == true)
            {
                $size = \Config::get('custom.image_size');
                $size = $size[$additional_req['custom_key']];
                $width = $size['width'];
                $height = $size['height'];
                $uploaded_image = Image::make($file->getRealPath())->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $compressed_image = (object)array(
                    'image' => $uploaded_image->stream(),
                );
                $s3_upload_image = $compressed_image->image->__toString();

            }
            else
            {
                $s3_upload_image = $file;
                $s3_upload_image = file_get_contents($s3_upload_image);
            }
            $upload_path = \Config::get('custom.' . $dir);
            $id = $merchant_id ? $merchant_id : get_merchant_id();
            $merchant = Merchant::find($id);
            $alias = $merchant->alias_name. $upload_path['path'];
            $name = time() . "_" . uniqid() ."_". $dir.'.' . $extension;
            $filePath = $alias . $name;

            \Illuminate\Support\Facades\Storage::disk('s3')->put($filePath, $s3_upload_image);
//            p($name);
             return $name;
        }
        return NULL;
    }

    public function uploadBase64Image($image, $dir = 'images', $merchant_id = null, $image_type = 'single')
    {
        $name = "";
        if ($image_type == 'multiple') {
            $file = $image; // its name of image
        } else {
            if (request()->$image) {
                $file = request()->$image; // its name of image's field
            }
        }

        list($format, $file) = explode(',', $file);
        $temp = explode('/', $format);
        list($ext,) = explode(';', $temp[1]);
        $file = base64_decode($file);

        $upload_path = \Config::get('custom.' . $dir);
        $id = $merchant_id ? $merchant_id : get_merchant_id();
        $merchant = Merchant::Find($id);
        $alias = $merchant->alias_name . $upload_path['path'];
        $name = time() . "_" . uniqid() . "_" . $dir . '.' . $ext;
        $filePath = $alias . $name;
        \Illuminate\Support\Facades\Storage::disk('s3')->put($filePath, $file);

        return $name;
    }


    // upload product import cover image
    public function uploadProductImportImage($image, $dir, $merchant_id, $extension ,$additional_req = [])
    {
        $file = $image; // its name of image
        if(!empty($file))
        {
//            $extension = $file->getClientOriginalExtension();
//            if(isset($additional_req['compress']) && $additional_req['compress'] == true)
//            {
//                $size = \Config::get('custom.image_size');
//                $size = $size[$additional_req['custom_key']];
//                $width = $size['width'];
//                $height = $size['height'];
//                $uploaded_image = Image::make($file->getRealPath())->resize($width, $height, function ($constraint) {
//                    $constraint->aspectRatio();
//                });
//                $compressed_image = (object)array(
//                    'image' => $uploaded_image->stream(),
//                );
//                $s3_upload_image = $compressed_image->image->__toString();
//
//            }
//            else
//            {
                $s3_upload_image = $file;
                $s3_upload_image = file_get_contents($s3_upload_image);
//            }
            $upload_path = \Config::get('custom.' . $dir);
            $id = $merchant_id ? $merchant_id : get_merchant_id();
            $merchant = Merchant::find($id);
            $alias = $merchant->alias_name. $upload_path['path'];
            $name = time() . "_" . uniqid() ."_". $dir.'.' . $extension;
            $filePath = $alias . $name;

            \Illuminate\Support\Facades\Storage::disk('s3')->put($filePath, $s3_upload_image);
            return $name;
        }
        return NULL;
    }


// google storage s3
//    public function uploadImage($image, $dir = 'images', $merchant_id = null, $image_type = 'single',$additional_req = [])
//    {
//        $name = "";
//        if($image_type =='multiple')
//        {
//            $file = $image; // its name of image
//        }
//        else{
//            if (request()->hasFile($image)) {
//                $file = request()->file($image); // its name of image's field
//            }
//        }
//        if(!empty($file))
//        {
//            $extension = $file->getClientOriginalExtension();
//            if(isset($additional_req['compress']) && $additional_req['compress'] == true)
//            {
//                $size = \Config::get('custom.image_size');
//                $size = $size[$additional_req['custom_key']];
//                $width = $size['width'];
//                $height = $size['height'];
//                $uploaded_image = Image::make($file->getRealPath())->resize($width, $height, function ($constraint) {
//                    $constraint->aspectRatio();
//                });
//                $compressed_image = (object)array(
//                    'image' => $uploaded_image->stream(),
//                );
//                $s3_upload_image = $compressed_image->image->__toString();
//
//            }
//            else
//            {
//                $s3_upload_image = $file;
//                $s3_upload_image = file_get_contents($s3_upload_image);
//            }
//            $upload_path = \Config::get('custom.' . $dir);
//            $id = $merchant_id ? $merchant_id : get_merchant_id();
//            $merchant = Merchant::find($id);
//            $alias = $merchant->alias_name. $upload_path['path'];
//            $name = time() . "_" . uniqid() ."_". $dir.'.' . $extension;
//            $filePath = $alias . $name;
//            $file_up = File::get($file);
//            Storage::disk('gcs')->put($filePath, $file_up);
//            return $name;
//        }
//        return NULL;
//    }
//
//    public function uploadBase64Image($image, $dir = 'images', $merchant_id = null, $image_type = 'single')
//    {
//        $name = "";
//        if ($image_type == 'multiple') {
//            $file = $image; // its name of image
//        } else {
//            if (request()->$image) {
//                $file = request()->$image; // its name of image's field
//            }
//        }
//
//        list($format, $file) = explode(',', $file);
//        $temp = explode('/', $format);
//        list($ext,) = explode(';', $temp[1]);
//        $file = base64_decode($file);
//
//        $upload_path = \Config::get('custom.' . $dir);
//        $id = $merchant_id ? $merchant_id : get_merchant_id();
//        $merchant = Merchant::Find($id);
//        $alias = $merchant->alias_name . $upload_path['path'];
//        $name = time() . "_" . uniqid() . "_" . $dir . '.' . $ext;
//        $filePath = $alias . $name;

//        Storage::disk('gcs')->put($filePath, $file);
//        return $name;
//    }

}