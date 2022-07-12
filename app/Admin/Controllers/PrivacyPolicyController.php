<?php

namespace App\Admin\Controllers;

use App\Models\PrivacyPolicy;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PrivacyPolicyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Privacy Policy';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PrivacyPolicy());

        $grid->column('id', __('Id'));
        $grid->column('type', __('Type'));
        $grid->column('title', __('Title'));
        $grid->column('discription', __('Discription'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

         $grid->filter(function ($filter) {
        
            $filter->equal('type', 'Type')->select([1 => 'Customer', 2 => 'Delivery Partner']);
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
        $show = new Show(PrivacyPolicy::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('type', __('Type'));
        $show->field('title', __('Title'));
        $show->field('discription', __('Discription'));
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
        $form = new Form(new PrivacyPolicy());

        $form->select('type', __('Type'))->options([1 => 'Customer', 2 => 'Delivery Partner'])->required();
        $form->text('title', __('Title'))->required();
        $form->textarea('discription', __('Discription'))->required();

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
