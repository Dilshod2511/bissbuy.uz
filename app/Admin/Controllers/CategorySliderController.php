<?php

namespace App\Admin\Controllers;

use App\Models\CategorySlider;
use App\Models\Category;
use App\Models\Status;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CategorySliderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Category Slider';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CategorySlider());

        $grid->column('id', __('Id'));
        $grid->column('category_id', __('Category'))->display(function($category){
            $category_name = Category::where('id',$category)->value('category_name');
            return $category_name;
        });
        $grid->column('category_slider_image', __('Category Slider Image'))->image();
        $grid->column('status', __('Status'))->display(function($status){
            $status_name = Status::where('id',$status)->value('status_name');
            if ($status == 1) {
                return "<span class='label label-success'>$status_name</span>";
            }if ($status == 2) {
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
            $category = Category::pluck('category_name', 'id');
            $statuses = Status::where('slug','general')->pluck('status_name', 'id');
        
            $filter->equal('category_id', 'Category')->select($category);
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
        $show = new Show(CategorySlider::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category_id', __('Category id'));
        $show->field('category_slider_image', __('Category slider image'));
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
        $form = new Form(new CategorySlider());
        $category = Category::pluck('category_name', 'id');


        $form->select('category_id', __('Category'))->options($category)->required();
        $form->image('category_slider_image', __('Category Slider Image'))->move('category_banner_images')->uniqueName()->required();
        $form->select('status', __('Status'))->options(Status::where('slug','general')->pluck('status_name','id'))->required();

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
