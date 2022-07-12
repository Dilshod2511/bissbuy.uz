<?php

Namespace App\Admin\Actions\Product;

Use App\Models\Product;
Use App\Models\ProductImage;
Use Encore\Admin\Actions\RowAction;

class DuplicateProduct extends RowAction
{
    // After the page clicks on the chart in this column, send the request to the backend handle method to execute
    public function handle(Product $model)
    {
         $replica = $model->replicate();
        $replica->save();

        foreach($model->images as $image)
        {
            if(ProductImage::where('id', $image->id)->exists()){
                $replicate_image = ProductImage::find($image->id)->replicate();
                $replicate_image->product_id = $replica->id;
                $replicate_image->save();
            }        
        }

        return $this->response()->success('copied')->refresh();
    }

    // This method displays different icons in this column based on the value of the `star` field.
    public function display($star)
    {
        return $star ? "<i class=\"fa fa-clone\"></i>" : "<i class=\"fa fa-clone\"></i>";
    }
}