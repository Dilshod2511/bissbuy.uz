<?php

Namespace App\Admin\Actions\Vendor;

Use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Vendor;
Use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ChangeStatus extends RowAction
{
    // After the page clicks on the chart in this column, send the request to the backend handle method to execute
    public function handle(Model $model)
    {
        $model->status = $model->status == 2 ? 1 : 2;
        $model->save();
        
        
        $success = "<button class=\"btn btn-sm btn-success\" title=\"Inactivate\"><span class=\"hidden-xs\">&nbsp;&nbsp;Active</span></button>";
        $danger = "<button class=\"btn btn-sm btn-danger\" title=\"Activate\"><span class=\"hidden-xs\">&nbsp;&nbsp;Inactive</span></button>";

         $html = $model->status == 1 ? $success :  $danger ;

         return $this->response()->html($html);
    }

    // This method displays different icons in this column based on the value of the `star` field.
    public function display($status)
    {
        $success = "<button class=\"btn btn-sm btn-success\" title=\"Inactivate\"><span class=\"hidden-xs\">&nbsp;&nbsp;Active</span></button>";
        $danger = "<button class=\"btn btn-sm btn-danger\" title=\"Activate\"><span class=\"hidden-xs\">&nbsp;&nbsp;Inactive</span></button>";
        return $status == 1 ? $success : $danger;
    }
}