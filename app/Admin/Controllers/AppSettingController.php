<?php

namespace App\Admin\Controllers;

use App\Models\AppSetting;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AppSettingController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App Setting';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AppSetting());

        $grid->column('id', __('Id'));
        $grid->column('application_name', __('Application name'));
        $grid->column('logo', __('Logo'))->image();
        $grid->column('contact_number', __('Contact number'));
        $grid->column('email', __('Email'));
        $grid->column('default_currency', __('Default currency'));
        $grid->column('booking_radius', __('Booking Radius'));
        $grid->column('referral_commission', __('Referral Comission'));
        $grid->disableExport();
        //$grid->disableCreation();
        $grid->disableFilter();
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
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
        $show = new Show(AppSetting::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('application_name', __('Application name'));
        $show->field('logo', __('Logo'));
        $show->field('contact_number', __('Contact number'));
        $show->field('email', __('Email'));
        $show->field('address', __('Address'));
        $show->field('delivery_charge', __('Delivery charge'));
        $show->field('free_delivery_amount', __('Free delivery amount'));
        $show->field('default_currency', __('Default currency'));
        $show->field('currency_short_code', __('Currency short code'));
        $show->field('razorpay_key', __('Razorpay key'));
        $show->field('booking_radius', __('Booking radius'));
        $show->field('vendor_radius', __('Vendor radius'));
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
        $form = new Form(new AppSetting());

        $form->text('application_name', __('Application name'))->rules(function ($form) {
            return 'required|max:100';
        });
        $form->image('logo', __('Logo'))->rules('required')->rules('required')->uniqueName();
        $form->text('contact_number', __('Contact number'))->rules(function ($form) {
            if (!$id = $form->model()->id) {
                return 'numeric|digits_between:1,15|required||unique:app_settings,contact_number';
            } else {
                return 'numeric|digits_between:1,15|required||unique:app_settings,contact_number,'.$form->model()->id;
            }
        });
        $form->email('email', __('Email'))->rules(function ($form) {
            if (!$id = $form->model()->id) {
                return 'required|max:100|unique:app_settings,email';
            } else {
                return 'required|max:100|unique:app_settings,email,'.$form->model()->id;
            }
        });
        $form->textarea('address', __('Address'))->rules(function ($form) {
            return 'required';
        });
        $form->decimal('delivery_charge', __('Delivery Charge'))->rules(function ($form) {
            return 'required';
        });
        $form->decimal('free_delivery_amount', __('Eligible free delivery amount'))->rules(function ($form) {
            return 'required';
        });
        $form->text('default_currency', __('Currency symbol'))->rules(function ($form) {
            return 'required';
        });
        $form->text('currency_short_code', __('Currenct short code'))->rules(function ($form) {
            return 'required';
        });
        $form->text('referral_commission', __('Referral Commission'))->rules(function ($form) {
            return 'required';
        });
        $form->text('booking_radius', __('Booking Radius'))->rules(function ($form) {
            return 'required';
        });
        $form->text('vendor_radius', __('Vendor Radius'))->rules(function ($form) {
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
