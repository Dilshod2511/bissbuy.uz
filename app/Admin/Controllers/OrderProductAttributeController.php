<?php

namespace App\Admin\Controllers;

use App\Models\OrderProductAttribute;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OrderProductAttributeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Order Product Attributes';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OrderProductAttribute());

        $grid->column('id', __('Id'));
        $grid->column('order_id', __('Order id'));
        $grid->column('product_id', __('Product id'));
        $grid->column('product_option_id', __('Product option id'));
        $grid->column('product_option_value', __('Product option value'));
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        $grid->filter(function ($filter) {

            $filter->equal('order_id', 'Order Id');
            $filter->equal('product_id', 'Product Id'); 
              
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
        $show = new Show(OrderProductAttribute::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_id', __('Order id'));
        $show->field('product_id', __('Product id'));
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
        $form = new Form(new OrderProductAttribute());

        $form->number('order_id', __('Order Id'));
        $form->number('product_id', __('Product'));
        $form->number('product_option_id', __('Product option'));
        $form->number('product_option_value', __('Product Option Value'));

        return $form;
    }
}
