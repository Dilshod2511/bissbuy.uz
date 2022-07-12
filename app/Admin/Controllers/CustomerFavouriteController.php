<?php

namespace App\Admin\Controllers;

use App\Models\CustomerFavourite;
use App\Models\Customer;
use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CustomerFavouriteController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Customer Favourites';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CustomerFavourite());

        $grid->column('id', __('Id'));
        $grid->column('product_id', __('Product'))->display(function($product){
            $product_name = Product::where('id',$product)->value('product_name');
            return $product_name;
        });
        $grid->column('customer_id', __('Customer'))->display(function($customer){
            $first_name = Customer::where('id',$customer)->value('first_name');
            return $first_name;
        });
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

         $grid->filter(function ($filter) {
            $product = Product::pluck('product_name', 'id');
            $customer = Customer::pluck('first_name', 'id');
        
            $filter->equal('product_id', 'Product')->select($product);
            $filter->equal('customer_id', 'Customer')->select($customer);
            
                 
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
        $show = new Show(CustomerFavourite::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('product_id', __('Product id'));
        $show->field('customer_id', __('Customer id'));
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
        $form = new Form(new CustomerFavourite());
        $product = Product::pluck('product_name', 'id');
        $customer = Customer::pluck('first_name', 'id');

        $form->select('product_id', __('Product'))->options($product)->required();
        $form->select('customer_id', __('Customer'))->options($customer)->required();

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
