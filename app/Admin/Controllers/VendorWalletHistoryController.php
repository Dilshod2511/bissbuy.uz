<?php

namespace App\Admin\Controllers;

use App\Models\VendorWalletHistory;
use App\Models\Vendor;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class VendorWalletHistoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Vendor Wallet Histories';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new VendorWalletHistory());

        $grid->column('id', __('Id'));
        $grid->column('vendor_id', __('Vendor'))->display(function($vendor){
            $vendor_name = Vendor::where('id',$vendor)->value('vendor_name');
            return $vendor_name;
        });
        $grid->column('type', __('Type'))->display(function($type){
            if ($type == 1) {
                return "<span class='label label-warning'>Fixed</span>";
            }if ($type == 2) {
                return "<span class='label label-info'>Percentage</span>";
            } 
        });
        $grid->column('message', __('Message'));
        $grid->column('amount', __('Amount'));
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
            $filter->like('type', 'Type')->select([1 => 'Fixed', 2 => 'Percentage']);
            
                 
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
        $show = new Show(VendorWalletHistory::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('vendor_id', __('Vendor id'));
        $show->field('type', __('Type'));
        $show->field('message', __('Message'));
        $show->field('amount', __('Amount'));
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
        $form = new Form(new VendorWalletHistory());
        $vendor = Vendor::pluck('vendor_name','id');

        $form->select('vendor_id', __('Vendor'))->options($vendor)->required();
        $form->select('type', __('Type'))->options([1 => 'Fixed', 2 => 'Percentage'])->required();
        $form->text('message', __('Message'))->required();
        $form->decimal('amount', __('Amount'))->required();

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
