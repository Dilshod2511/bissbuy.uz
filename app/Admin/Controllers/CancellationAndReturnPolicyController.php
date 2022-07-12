<?php

namespace App\Admin\Controllers;

use App\Models\CancellationAndReturnPolicy;
use App\Models\Category;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CancellationAndReturnPolicyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Cancellation And Return Policy';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CancellationAndReturnPolicy());

        $grid->column('id', __('Id'));
        $grid->column('category_id', __('Category'))->display(function($category){
            $category_name = Category::where('id',$category)->value('category_name');
            return $category_name;
        });
        $grid->column('policy', __('Policy'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

         $grid->filter(function ($filter) {
            $category = Category::pluck('category_name', 'id');
        
            $filter->equal('category_id', 'Category')->select($category);
                 
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
        $show = new Show(CancellationAndReturnPolicy::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category_id', __('Category id'));
        $show->field('policy', __('Policy'));
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
        $form = new Form(new CancellationAndReturnPolicy());
        $category = Category::pluck('category_name', 'id');

        $form->select('category_id', __('Category'))->options($category)->required();
        $form->text('policy', __('Policy'))->required();

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
