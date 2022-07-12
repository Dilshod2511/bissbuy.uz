<?php

namespace App\Admin\Controllers;

use App\Models\CustomerAddress;
use App\Models\Customer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CustomerAddressController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'CustomerAddress';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CustomerAddress());

        $grid->column('id', __('Id'));
        $grid->column('customer_id', __('Customer'))->display(function($customer){
            $customer_name = Customer::where('id',$customer)->value('first_name');
            return $customer_name;
        });
        $grid->column('customer_address', __('Customer address'));
        $grid->column('google_address', __('Google address'));
        $grid->column('lat', __('Lat'));
        $grid->column('lng', __('Lng'));
        $grid->column('post_code', __('Post code'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

         $grid->filter(function ($filter) {
            $customer = Customer::pluck('first_name', 'id');
        
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
        $show = new Show(CustomerAddress::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('customer_id', __('Customer id'));
        $show->field('customer_address', __('Customer address'));
        $show->field('google_address', __('Google address'));
        $show->field('lat', __('Lat'));
        $show->field('lng', __('Lng'));
        $show->field('post_code', __('Post code'));
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
        $form = new Form(new CustomerAddress());
        $customer = Customer::pluck('first_name', 'id');

        $form->select('customer_id', __('Customer id'))->options($customer)->required();
        $form->textarea('customer_address', __('Customer address'));
        $form->textarea('google_address', __('Google address'));
        $form->text('lat', __('Lat'));
        $form->text('lng', __('Lng'));
        $form->text('post_code', __('Post code'));

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
