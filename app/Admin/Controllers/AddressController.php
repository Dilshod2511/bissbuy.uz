<?php

namespace App\Admin\Controllers;

use App\Models\Address;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AddressController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Address';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Address());

        $grid->column('id', __('Id'));
        $grid->column('customer_id', __('Customer id'));
        $grid->column('customer_address', __('Customer address'));
        $grid->column('google_address', __('Google address'));
        $grid->column('lat', __('Lat'));
        $grid->column('lng', __('Lng'));
        $grid->column('static_map', __('Static map'));
        $grid->column('post_code', __('Post code'));
        $grid->column('status', __('Status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Address::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('customer_id', __('Customer id'));
        $show->field('customer_address', __('Customer address'));
        $show->field('google_address', __('Google address'));
        $show->field('lat', __('Lat'));
        $show->field('lng', __('Lng'));
        $show->field('static_map', __('Static map'));
        $show->field('post_code', __('Post code'));
        $show->field('status', __('Status'));
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
        $form = new Form(new Address());

        $form->number('customer_id', __('Customer id'));
        $form->textarea('customer_address', __('Customer address'));
        $form->text('google_address', __('Google address'));
        $form->text('lat', __('Lat'));
        $form->text('lng', __('Lng'));
        $form->text('static_map', __('Static map'));
        $form->text('post_code', __('Post code'));
        $form->number('status', __('Status'))->default(1);

        return $form;
    }
}
