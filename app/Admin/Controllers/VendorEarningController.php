<?php

namespace App\Admin\Controllers;

use App\Models\VendorEarning;
use App\Models\Vendor;
use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class VendorEarningController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Vendor Earnings';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new VendorEarning());

        $grid->column('id', __('Id'));
        $grid->column('order_id', __('Order'));
        $grid->column('vendor_id', __('Vendor'))->display(function($vendor){
            $vendor_name = Vendor::where('id',$vendor)->value('vendor_name');
            return $vendor_name;
        });
        $grid->column('total_amount', __('Total Amount'));
        $grid->column('vendor_earnings', __('Vendor Earnings'));
        $grid->column('admin_comissions', __('Admin Comissions'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

         $grid->filter(function ($filter) {
            $vendor = Vendor::pluck('vendor_name', 'id');
        
            $filter->equal('order_id', 'Order');
            $filter->equal('vendor_id', 'Vendor')->select($vendor);
            
                 
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
        $show = new Show(VendorEarning::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_id', __('Order id'));
        $show->field('vendor_id', __('Vendor id'));
        $show->field('total_amount', __('Total amount'));
        $show->field('vendor_earnings', __('Vendor earnings'));
        $show->field('admin_comissions', __('Admin comissions'));
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
        $form = new Form(new VendorEarning());
        $order = Order::pluck('id');
        $vendor = Vendor::pluck('vendor_name','id');

        $form->select('order_id', __('Order Id'))->options($order)->required();
        $form->select('vendor_id', __('Vendor Id'))->options($vendor)->required();
        $form->decimal('total_amount', __('Total Amount'))->required();
        $form->decimal('vendor_earnings', __('Vendor Earnings'))->required();
        $form->decimal('admin_comissions', __('Admin Comissions'))->required();

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
