<?php

namespace App\Admin\Controllers;

use App\Models\ProductAttribute;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductAttributeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Product Attributes';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProductAttribute());

        $grid->column('id', __('Id'));
        $grid->column('product_id', __('Product'))->display(function($product){
            $product_name = Product::where('id',$product)->value('product_name');
            return $product_name;
        });
        $grid->column('option_id', __('Option'))->display(function($option){
            $option_name = ProductOption::where('id',$option)->value('option_name');
            return $option_name;
        });
        //$grid->column('option_value_id', __('Option Value Id'));
        //$grid->column('option_value_price', __('Option Value Price'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

         $grid->filter(function ($filter) {
            $product = Product::pluck('product_name', 'id');
        
            $filter->equal('product_id', 'Product')->select($product);
                      
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ProductAttribute::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('product_id', __('Product id'));
        $show->field('option_id', __('Option id'));
        $show->field('option_value_id', __('Option value id'));
        $show->field('option_value_price', __('Option value price'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ProductAttribute());
        $product = Product::pluck('product_name', 'id');
        $product_option = ProductOption::pluck('option_name', 'id');
        

        $form->select('product_id', __('Product'))->options($product)->required();
        //$form->select('option_id', __('Option Id'))->options($product_option)->required();
        //$form->multipleSelect('option_value_id', __('Option Value Id'))->options(ProductOptionValue::all()->pluck('product_option_value', 'id'))->required();
        
        $form->select('option_id', __('Option'))->load('option_value_id', '/admin/get_option_value', 'product_option_value','product_option_value')->options($product_option)->rules(function ($form) {
            return 'required';
        });
        $form->multipleSelect('option_value_id', 'Option Value Id')->options(function ($id) {
               $option_value = ProductOptionValue::where('product_option_id',$this->option_id)->pluck('product_option_value','product_option_value');
               return $option_value;
            })->rules(function ($form) {
                return 'required';
            });
        $form->tools(function (Form\Tools $tools) {
           $tools->disableDelete(); 
           $tools->disableView();
       });
       $form->footer(function ($footer) {
           $footer->disableViewCheck();
           $footer->disableEditingCheck();
           $footer->disableCreatingCheck();
       });

        return $form;
    }
}
