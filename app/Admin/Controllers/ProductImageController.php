<?php

namespace App\Admin\Controllers;

use App\Models\ProductImage;
use App\Models\Product;
use App\Models\Status;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductImageController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Product Images';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProductImage());

        $grid->column('id', __('Id'));
        $grid->column('product_id', __('Product'))->display(function($product){
            $product_name = Product::where('id',$product)->value('product_name');
            return $product_name;
        });
        $grid->column('product_image', __('Product Image'))->image();
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
        $show = new Show(ProductImage::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('product_id', __('Product id'));
        $show->field('product_image', __('Product image'));
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
        $form = new Form(new ProductImage());
        $product = Product::pluck('product_name', 'id');

        $form->select('product_id', __('Product'))->options($product)->required();
        $form->image('product_image', __('Product Image'))->move('products')->required()->uniqueName();
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
