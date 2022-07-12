<?php

namespace App\Admin\Controllers;

use App\Models\OrderPaymentDetail;
use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OrderPaymentDetailController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Order Payment Details';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OrderPaymentDetail());

        $grid->column('id', __('Id'));
        $grid->column('order_id', __('Order Id'));
        $grid->column('title', __('Title'));
        $grid->column('amount', __('Amount'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        $grid->filter(function ($filter) {

            $filter->equal('order_id', 'Order Id');   
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
        $show = new Show(OrderPaymentDetail::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_id', __('Order id'));
        $show->field('title', __('Title'));
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
        $form = new Form(new OrderPaymentDetail());

        $form->number('order_id', __('Order id'));
        $form->text('title', __('Title'));
        $form->decimal('amount', __('Amount'));

        return $form;
    }
}
