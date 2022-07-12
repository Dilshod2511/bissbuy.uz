<?php

namespace App\Admin\Controllers;

use App\Models\ShippingSetting;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ShippingSettingController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ShippingSetting';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ShippingSetting());

        $grid->column('id', __('Id'));
        $grid->column('free_delivery_margin', __('Free Delivery Margin'));
        $grid->column('delivery_charge', __('Delivery Charge'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

         $grid->filter(function ($filter) {
        
            $filter->like('free_delivery_margin', 'Free Delivery Margin');
            $filter->like('delivery_charge', 'Delivery Charge');
            
                 
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
        $show = new Show(ShippingSetting::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('free_delivery_margin', __('Free delivery margin'));
        $show->field('delivery_charge', __('Delivery charge'));
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
        $form = new Form(new ShippingSetting());

        $form->decimal('free_delivery_margin', __('Free Delivery Margin'))->required();
        $form->decimal('delivery_charge', __('Delivery Charge'))->required();

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
