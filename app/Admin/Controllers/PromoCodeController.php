<?php

namespace App\Admin\Controllers;

use App\Models\PromoCode;
use App\Models\Status;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PromoCodeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Promo Codes';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PromoCode());

        $grid->column('id', __('Id'));
        $grid->column('promo_code', __('Promo codes'));
        $grid->column('promo_name', __('Promo names'));
        $grid->column('promo_description', __('Promo description'));
        $grid->column('discount_type', __('Discount type'))->display(function($type){
            if ($type == 1) {
                return "<span class='label label-warning'>Fixed</span>";
            }if ($type == 2) {
                return "<span class='label label-info'>Percentage</span>";
            } 
        });
        $grid->column('amount', __('Amount'));
        $grid->column('min_amount', __('Min amount'));
        $grid->column('status', __('Status'))->display(function($status){
            $status_name = Status::where('id',$status)->value('status_name');
            if ($status == 1) {
                return "<span class='label label-success'>$status_name</span>";
            } else {
                return "<span class='label label-danger'>$status_name</span>";
            }
        });
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

         $grid->filter(function ($filter) {
             $statuses = Status::where('slug','general')->pluck('status_name', 'id');
        
            $filter->like('promo_code', 'Promo code');
            $filter->like('promo_name', 'Promo Name');
            $filter->like('discount_type', 'Discount Type')->select([1 => 'Fixed', 2 => 'Percentage']);
            $filter->equal('status', 'Status')->select($statuses);
            
                 
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
        $show = new Show(PromoCode::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('promo_code', __('Promo codes'));
        $show->field('promo_name', __('Promo names'));
        $show->field('promo_description', __('Promo description'));
        $show->field('discount_type', __('Discount type'));
        $show->field('amount', __('Amount'));
        $show->field('min_amount', __('Min amount'));
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
        $form = new Form(new PromoCode());

        $form->text('promo_code', __('Promo Code'))->required();
        $form->text('promo_name', __('Promo Name'))->required();
        $form->text('promo_description', __('Promo Description'))->required();
        $form->select('discount_type', __('Discount Type'))->options([1 => 'Fixed', 2 => 'Percentage'])->required();
        $form->decimal('amount', __('Amount'))->required();
        $form->decimal('min_amount', __('Min Amount'))->required();
        $form->select('status', __('Status'))->options(Status::where('slug','general')->pluck('status_name','id'))->rules(function ($form) {
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
