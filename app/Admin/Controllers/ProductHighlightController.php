<?php

namespace App\Admin\Controllers;

use App\Models\ProductHighlight;
use App\Models\Product;
use App\Models\Status;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductHighlightController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Product Highlights';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProductHighlight());

        $grid->column('id', __('Id'));
        $grid->column('product_id', __('Product'))->display(function($product){
            $product_name = Product::where('id',$product)->value('product_name');
            return $product_name;
        });
        $grid->column('key', __('Key'));
        $grid->column('value', __('Value'));
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
            $product = Product::pluck('product_name', 'id');
            $statuses = Status::where('slug','general')->pluck('status_name', 'id');
        
            $filter->equal('product_id', 'Product')->select($product);
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
        $show = new Show(ProductHighlight::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('product_id', __('Product id'));
        $show->field('key', __('Key'));
        $show->field('value', __('Value'));
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
        $form = new Form(new ProductHighlight());
        $product = Product::pluck('product_name', 'id');

        $form->select('product_id', __('Product'))->options($product)->required();
        $form->text('key', __('Key'))->required();
        $form->text('value', __('Value'))->required();
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
