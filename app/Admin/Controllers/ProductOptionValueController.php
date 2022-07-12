<?php

namespace App\Admin\Controllers;

use App\Models\ProductOptionValue;
use App\Models\ProductOption;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductOptionValueController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Product Option Values';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProductOptionValue());

        $grid->column('id', __('Id'));
        $grid->column('product_option_id', __('Product Option'))->display(function($product_option){
            $option_name = ProductOption::where('id',$product_option)->value('option_name');
            return $option_name;
        });
        $grid->column('product_option_value', __('Product Option Value'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

         $grid->filter(function ($filter) {
            $product_option = ProductOption::pluck('option_name', 'id');
        
            $filter->equal('product_id', 'Product')->select($product_option);
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
        $show = new Show(ProductOptionValue::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('product_option_id', __('Product option id'));
        $show->field('product_option_value', __('Product option value'));
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
        $form = new Form(new ProductOptionValue());
        $product_option = ProductOption::pluck('option_name', 'id');

        $form->select('product_option_id', __('Product Option'))->options($product_option)->required();
        $form->text('product_option_value', __('Product Option Value'))->required();

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
