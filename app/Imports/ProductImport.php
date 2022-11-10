<?php

namespace App\Imports;

//use App\Models\ImportUserFail;
use App\Models\BusinessSegment\LanguageProduct;
use App\Models\BusinessSegment\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Auth;
use DB;
use App\Traits\MerchantTrait;
use App\Traits\ImageTrait;
use Illuminate\Http\UploadedFile;

class ProductImport implements ToModel,WithStartRow,WithValidation

{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
     *
    */
    use ImageTrait,MerchantTrait;
    public function startRow(): int
    {
        return 2;
    }

    public function rules(): array
    {
        return [
//            '3' => Rule::unique('orders', 'po_number','do_number','line_item','material_code')
        ];
    }


    public function model(array $row)
    {
        try
        {
            $business_segment = get_business_segment(false);
            $string_file = $this->getStringFile(NULL,$business_segment->Merchant);
            $merchant_id = $business_segment->merchant_id;
            $business_segment_id = $business_segment->id;
            $segment_id = $business_segment->segment_id;
            $category_id = $row[0]; // category_id
            $sku = $row[1];
            $product_name = $row[2];
            $description = $row[3];
            $ingredient = $row[4];
            $cover_image = $row[5];
            $preparation_time = $row[6];
            $sequence = $row[7];
            $status = $row[8];
            $food_type = $row[9];
            $manage_inventory = $row[10];
            $locale = \App::getLocale();

            $arr_name = [
                'product_name'=>$product_name,
                'product_description'=>$description,
                'product_ingredients'=>$ingredient,
            ];

            $product_name = DB::table('language_products')->where(function ($query) use ($merchant_id,$locale,$product_name) {
                return $query->where([['language_products.merchant_id', '=', $merchant_id], ['language_products.locale', '=', $locale], ['language_products.name', '=', $product_name]]);
            })->join("products","language_products.product_id","=","products.id")
                ->where('products.merchant_id','=',$merchant_id)
                ->where('products.business_segment_id','=',$business_segment_id)
                ->where('products.delete',NULL)->first();

            if (!empty($product_name->id)) {
                throw new \Exception(trans("$string_file.product_name_already_exist"));
            }


            $product = new Product ();
            $product->business_segment_id = $business_segment_id;
            $product->merchant_id = $merchant_id;
            $product->segment_id = $segment_id;
            $product->sku_id = $sku;
            $product->product_preparation_time = $preparation_time;
            $product->sequence = $sequence;
            $product->status = $status;
            $product->category_id = $category_id;
            $product->food_type = $food_type;
//            $product->display_type = $request->display_type;

            $product->manage_inventory = $manage_inventory;

            if (!empty($cover_image)) {
                $additional_req = ['compress' => true, 'custom_key' => 'product'];
                $info = pathinfo($cover_image);
                $contents = file_get_contents($cover_image); // current
                $file = '/tmp/' . $info['basename'];// new file
                file_put_contents($file, $contents);
                $extension = $info['extension'];//$file->getClientOriginalExtension();
                $cover_image = new UploadedFile($file, $info['basename']);
                $product->product_cover_image = $this->uploadProductImportImage($cover_image, 'product_cover_image', $merchant_id, $extension, $additional_req);
            }

            $product->save();
            // save language data
            $this->saveLanguageData($arr_name,$product->merchant_id,$product,$locale);

        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            throw new \Exception($message);
        }
//        p('sdf');
        return $product;
    }


    // save
    public function saveLanguageData($arr_name,$merchant_id,$product,$locale)
    {

        //
        LanguageProduct::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => $locale, 'product_id' => $product->id
        ], [
            'business_segment_id' => $product->business_segment_id,
            'name' => $arr_name['product_name'],
            'description' => $arr_name['product_description'],
            'ingredients' => $arr_name['product_ingredients'],
        ]);
    }
}
