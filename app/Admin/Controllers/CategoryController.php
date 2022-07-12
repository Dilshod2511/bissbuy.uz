<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\Status;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Actions\Vendor\ChangeStatus;

class CategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Category';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Category());

        $grid->column('id', __('Id'));
        $grid->column('category_name', __('Category Name'));
        $grid->column('category_image', __('Category Image'))->image();
        $grid->column('parent_id', __('Parent Id'))->display(function($parent_id){
            if ($parent_id == "") {
                return "Null";
            }else{
                return "$parent_id";
            }
        });
      

        $grid->column('tax', __('Tax'))->display(function($tax){
            if($tax !== null)
             return $tax . ' %';
        });

      
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

         $grid->filter(function ($filter) {
            $statuses = Status::where('slug','general')->pluck('status_name', 'id');
        
            $filter->equal('status', 'Status')->select($statuses);
                 
        });
        
        $grid->column('status')->action(ChangeStatus::class);
        
        $grid->quickSearch('category_name');
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
        $show = new Show(Category::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category_name', __('Category name'));
        $show->field('category_image', __('Category image'));
        $show->field('parent_id', __('Parent id'));
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
        $form = new Form(new Category());
        $parent_id = Category::where('parent_id')->pluck('category_name','id');

        $form->text('category_name', __('Category Name'))->required();
        $form->image('category_image', __('Category Image'))
        ->move('category_images')
        ->uniqueName()
        ->required();
        $form->select('parent_id', __('Parent Id'))->options($parent_id);
        $form->select('status', __('Status'))->options(Status::where('slug','general')->pluck('status_name','id'))->required();

        $form->text('tax', __('Tax'));

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
