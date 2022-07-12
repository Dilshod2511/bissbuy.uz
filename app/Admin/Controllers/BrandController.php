<?php

namespace App\Admin\Controllers;

use App\Models\Brand;
use App\Models\Status;
use App\Models\Category;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Actions\Vendor\ChangeStatus;


class BrandController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Brand';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Brand());

        $grid->column('id', __('Id'));
        $grid->column('category_id', __('Category'))->display(function($category){
            $category_name = Category::where('id',$category)->value('category_name');
            return $category_name;
        });
        $grid->column('brand_name', __('Brand Name'));
        $grid->column('brand_description', __('Brand Description'))->hide();
        $grid->column('brand_image', __('Brand Image'))->image();
      
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });


        $grid->column('status')->action(ChangeStatus::class);
 
 
         $grid->filter(function ($filter) {
            $statuses = Status::where('slug','general')->pluck('status_name', 'id');
        
            $filter->equal('status', 'Status')->select($statuses);
                 
        });
 $grid->quickSearch('brand_name');
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
        $show = new Show(Brand::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category_id', __('Category id'));
        $show->field('brand_name', __('Brand name'));
        $show->field('brand_description', __('Brand description'));
        $show->field('brand_image', __('Brand image'));
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
        $form = new Form(new Brand());
        $category = Category::pluck('category_name', 'id');

        $form->select('category_id', __('Category id'))->options($category)->required();
        $form->text('brand_name', __('Brand name'))->required();
        $form->textarea('brand_description', __('Brand description'))->required();
        $form->image('brand_image', __('Brand image'))->move('brand_images')->required()->uniqueName();
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
