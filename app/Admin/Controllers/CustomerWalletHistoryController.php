<?php

namespace App\Admin\Controllers;

use App\Models\CustomerWalletHistory;
use App\Models\Customer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CustomerWalletHistoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Customer Wallet History';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CustomerWalletHistory());

        $grid->column('id', __('Id'));
        $grid->column('customer_id', __('Customer'))->display(function($customer){
            $customer_name = Customer::where('id',$customer)->value('first_name');
            return $customer_name;
        });
        $grid->column('type', __('Type'))->display(function($type){
            if ($type == 1) {
                return "<span class='label label-warning'>Debit</span>";
            }if ($type == 2) {
                return "<span class='label label-info'>Credit</span>";
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
            $customer = Customer::pluck('first_name', 'id');
        
            $filter->equal('customer_id', 'Customer')->select($customer);
            $filter->like('type', 'Type')->select([1 => 'Debit', 2 => 'Credit']);
                 
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
        $show = new Show(CustomerWalletHistory::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('customer_id', __('Customer id'));
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
        $form = new Form(new CustomerWalletHistory());
        $customer = Customer::pluck('first_name', 'id');

        $form->select('customer_id', __('Customer'))->options($customer)->required();
        $form->select('type', __('Type'))->options([1 => 'Debit', 2 => 'Credit'])->required();
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
