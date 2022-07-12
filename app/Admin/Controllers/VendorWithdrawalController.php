<?php

namespace App\Admin\Controllers;

use App\Models\VendorWithdrawal;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class VendorWithdrawalController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'VendorWithdrawal';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new VendorWithdrawal());

        $grid->column('id', __('Id'));
        $grid->column('vendor_id', __('Vendor id'));
        $grid->column('amount', __('Amount'));
        $grid->column('reference_proof', __('Reference proof'));
        $grid->column('reference_no', __('Reference no'));
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
        $show = new Show(VendorWithdrawal::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('vendor_id', __('Vendor id'));
        $show->field('amount', __('Amount'));
        $show->field('reference_proof', __('Reference proof'));
        $show->field('reference_no', __('Reference no'));
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
        $form = new Form(new VendorWithdrawal());

        $form->number('vendor_id', __('Vendor id'));
        $form->decimal('amount', __('Amount'));
        $form->text('reference_proof', __('Reference proof'));
        $form->textarea('reference_no', __('Reference no'));
        $form->number('status', __('Status'))->default(1);

        return $form;
    }
}
