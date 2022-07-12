<?php

namespace App\Admin\Controllers;

use App\Models\OrderProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OrderProductController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Order Products';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OrderProduct());

        $grid->column('id', __('Id'));
        $grid->column('order_id', __('Order Id'));
        $grid->column('product_id', __('Product Id'));
        $grid->column('referred_id', __('Referred'))->display(function($customer_id){
            if($customer_id !=0){
                return Customer::where('id',$customer_id)->value('first_name');
            }else{
                return "null";
            }
        });
        $grid->column('product_name', __('Product Name'));
        $grid->column('product_price', __('Product Price'));
        $grid->column('qty', __('Qty'));
        $grid->column('total_price', __('Total Price'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        $grid->filter(function ($filter) {

            $filter->equal('order_id', 'Order Id');
            $filter->equal('product_id', 'Product Id'); 
            $filter->like('product_name', 'Product Name');  
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
        $show = new Show(OrderProduct::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_id', __('Order id'));
        $show->field('product_id', __('Product id'));
        $show->field('product_name', __('Product name'));
        $show->field('product_price', __('Product price'));
        $show->field('qty', __('Qty'));
        $show->field('total_price', __('Total price'));
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
        $form = new Form(new OrderProduct());

        $form->number('order_id', __('Order id'));
        $form->number('product_id', __('Product id'));
        $form->text('product_name', __('Product name'));
        $form->decimal('product_price', __('Product price'));
        $form->number('qty', __('Qty'));
        $form->decimal('total_price', __('Total price'));

        return $form;
    }
}
