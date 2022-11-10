<?php

namespace App\Imports;

//use App\Models\ImportUserFail;
use App\Models\BusinessSegment\LanguageProduct;
use App\Models\BusinessSegment\Product;
use App\Models\BusinessSegment\ProductVariant;
use App\Models\BusinessSegment\ProductInventory;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
//use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Auth;
use DB;
use App\Traits\MerchantTrait;
use App\Traits\ImageTrait;
use Illuminate\Http\UploadedFile;
use App\Models\BusinessSegment\LanguageProductVariant;
use App\Models\BusinessSegment\ProductInventoryLog;

class ProductVariantImport implements ToModel,WithStartRow,WithValidation

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
//            p($row);
            $business_segment = get_business_segment(false);
            $string_file = $this->getStringFile(NULL,$business_segment->Merchant);
            $merchant_id = $business_segment->merchant_id;
            $business_segment_id = $business_segment->id;
            $segment_id = $business_segment->segment_id;
            $product_id = $row[0]; // category_id
            $product_sku = $row[1];
            $product_name = $row[2];
            $variant_sku = $row[3];
            $variant_title = $row[4];
            $price = $row[5];
            $weight_unit_id = $row[6];
            $weight = $row[7];
            $is_title_show = $row[8];
            $variant_status = $row[9];
            $inventory_stock = $row[10];
            $locale = \App::getLocale();

            $product_sku_diff = Product::whereHas('ProductVariant',function($q)use($variant_sku){
                return $q->where([['sku_id','=',$variant_sku]]);
            })
                ->where([['merchant_id','=',$merchant_id],['business_segment_id','=',$business_segment_id],['segment_id','=',$segment_id],['id','=',$product_id]])->count();
            if($product_sku_diff > 0){
                return array('error' => 'The sku id has already been taken.');
            }

            $product_variant = new ProductVariant ();
            $product_variant->product_id = $product_id;
            $product_variant->sku_id = $variant_sku;
            $product_variant->product_title = $variant_title;
            $product_variant->product_price = $price;
            $product_variant->weight_unit_id = $weight_unit_id;
            $product_variant->weight = $weight;
            $product_variant->status = $variant_status;
            $product_variant->is_title_show = $is_title_show;
            $product_variant->save();

            // PRODUCT INVENTORY
            $product_inventory = new ProductInventory();
            $product_inventory->product_variant_id = $product_variant->id;
            $product_inventory->merchant_id = $merchant_id;
            $product_inventory->segment_id = $segment_id;
            $product_inventory->business_segment_id = $business_segment_id;

            // PRODUCT INVENTORY LOG
            $product_inventory->current_stock = $inventory_stock;
            $product_inventory->save();
            $product_inventory_log = new ProductInventoryLog();
            $product_inventory_log->product_inventory_id = $product_inventory->id;
            $product_inventory_log->last_current_stock = 0;
            $product_inventory_log->last_product_cost = 0;
            $product_inventory_log->last_product_selling_price = 0;
            $product_inventory_log->new_stock = $inventory_stock;
            $product_inventory_log->current_stock = $inventory_stock;
            $product_inventory_log->save();

            // sync language of category
            if($is_title_show == 1)
            {
                $this->saveVariantData($variant_title,$merchant_id,$product_variant,$business_segment_id,$locale);
            }
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            throw new \Exception($message);
        }
        return $product_variant;
    }


    // save
    public function saveVariantData($variant_title,$merchant_id,$product_variant,$business_segment_id,$locale)
    {
        LanguageProductVariant::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => $locale, 'product_variant_id' => $product_variant->id
        ], [
            'business_segment_id' => $business_segment_id,
            'name' => $variant_title,
        ]);
    }
}
