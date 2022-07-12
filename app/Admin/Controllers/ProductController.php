<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\Vendor;
use App\Models\Brand;
use App\Models\Status;
use App\Models\Category;
use App\Models\ProductOption;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
Use Encore\Admin\Admin;
use App\Admin\Actions\Product\DuplicateProduct;
use App\Admin\Actions\Vendor\ChangeStatus;
use App\Models\ProductImage;

class ProductController extends AdminController
{
    /**
     * 
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Product';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        
        $grid = new Grid(new Product());




       $perPage = $grid->perPage;
        $current_page = request()->has('page') ? intval(request()->page) : 1; 
        $grid->rows(function (Grid\Row $row) use($current_page, $perPage) {
            $row->column('number',  ($current_page-1) * $perPage + $row->number + 1 );
         });
         $grid->column('number', '№');
        



        
        
        
        
        $grid->column('category_id', __('Category'))->display(function($category){
            $category_name = Category::where('id',$category)->value('category_name');
            return $category_name;
        });
        
        
        $grid->column('subcategory_id', __('Subcategory'))->display(function($category){
            $category_name = Category::where('id',$category)->value('category_name');
            return $category_name;
        });
        
        
        $grid->column('brand_id', __('Brand'))->display(function($brand){
            $brand_name = Brand::where('id',$brand)->value('brand_name');
            return $brand_name;
        });
        $grid->column('vendor_id', __('Vendor'))->display(function($vendor){
            $vendor_name = Vendor::where('id',$vendor)->value('vendor_name');
            return $vendor_name;
        });
        $grid->column('product_name', __('Product Name'));
        // $grid->column('cover_image', __('Cover Image'))->hide();
        $grid->column('short_description', __('Short Description'))->hide();
        $grid->column('product_price', __('Product Price'));
        //$grid->column('offer_price', __('Offer Price'));
        //$grid->column('rating', __('Rating'));
        // $grid->column('current_stock', __('Current Stock'));
        // $grid->column('min_qty', __('Min qty'));
        // $grid->column('status', __('Status'))->display(function($status){
        //     $status_name = Status::where('id',$status)->value('status_name');
        //     if ($status == 1) {
        //         return "<span class='label label-success'>$status_name</span>";
        //     }if ($status == 2) {
        //         return "<span class='label label-danger'>$status_name</span>";
        //     } 
        // });
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();



        $grid->column('cover_image')->image();


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




        $grid->quickSearch('product_name');
        $grid->column('Дублировать запись')->action(DuplicateProduct::class);
 $grid->column('status')->action(ChangeStatus::class);


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
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category_id', __('Category id'));
        $show->field('brand_id', __('Brand id'));
        $show->field('vendor_id', __('Vendor id'));
        $show->field('product_name', __('Product name'));
        // $show->field('cover_image', __('Cover image'));
        $show->field('short_description', __('Short description'));
        $show->field('product_price', __('Product price'));
        $show->field('offer_price', __('Offer price'));
        $show->field('rating', __('Rating'));
        $show->field('current_stack', __('Current stack'));
        $show->field('min_qty', __('Min qty'));
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
        $form = new Form(new Product());
        $category = Category::whereNull('parent_id')->pluck('category_name', 'id');
        $brand = Brand::pluck('brand_name', 'id');
        $vendor = Vendor::pluck('vendor_name', 'id');

       // $form->select('category_id', __('Category'))->options($category)->required();
        //$form->select('subcategory_id', __('SubCategory'))->options($category)->required();
        


        //$form->select('category_id', __('Category'))->options($category)->load('subcategory_id', '/api/subcats')->required(); 
        //$form->select('subcategory_id', 'Subcategory'); //user
          
          
        $form->select('category_id', __('Category'))->options($category)->load('subcategory_id', '/api/subcats')->required(); 
        
        $form
        ->select('subcategory_id', 'Subcategory')
        ->options(function ($id) {
            
            if(Category::where('id',$id)->exists()){
                return Category::find($id)->pluck('category_name', 'id');
            }
           
            //return DataPartner::options($id);
        }); //user 
        
        
        
        
        
        
        $form->select('brand_id', __('Brand'))->options($brand)->required();
        $form->select('vendor_id', __('Vendor'))->options($vendor)->required();
        $form->text('product_name', __('Product Name'))->required();
        // $form->image('cover_image', __('Cover Image'))->move('products')->uniqueName()->required();
        $form->ckeditor('short_description', __('Short Description'))->required();
        $form->text('key_words', __('Key Words'))->required()->rules('max:150');;
        $form->decimal('product_price', __('Product Price'))->required();
        //$form->decimal('offer_price', __('Offer Price'))->required();
        // $form->number('current_stock', __('Current Stock'))->required();
        // $form->number('min_qty', __('Min Qty'))->required();
        $form->select('status', __('Status'))->options(Status::where('slug','general')->pluck('status_name','id'))->required();
        
        $form->tools(function (Form\Tools $tools) {
           $tools->disableDelete(); 
           $tools->disableView();
       });
       
       
       $form->footer(function ($footer) {
           $footer->disableViewCheck();
           //$footer->disableEditingCheck();
           //$footer->disableCreatingCheck();
       });

       // Subtable fields
       
        $form->multipleFile('gallery', 'Photos')->sortable()->removable();
        
        // $form->hasMany('images', 'Фото', function (Form\NestedForm $form) {
        //     $form->image('product_image')->rules('mimes:jpeg,jpg,bmp,png|max:1000');
        // });


    

    
       
        $form->hasMany('attributes' , 'Атрибуты', function (Form\NestedForm $form) {
            $options = ProductOption::pluck('option_name', 'id');



            $form->select('option_id', __('Option'))->options($options)->load('option_value_id', '/api/option_vals')->attribute(['id' => 'option_select'])->required();  //categpry
            $form->multipleSelect('option_value_id', 'Value')->attribute(['id' => 'select_option_id']); //user
                        
            // $form->text('option_value_id', 'Value');
        });
        
        
        $form->submitted(function (Form $form) {
            $form->ignore('photos');
        });


        $form->saved(function (Form $form) {
            ProductImage::where('product_id', $form->model()->id)->delete();
            if (is_array($form->model()->gallery)) {
            foreach ($form->model()->gallery as $gallery) {
                if($gallery != null){
                  ProductImage::create([
                    'product_id' => $form->model()->id,
                    'product_image' => $gallery,
                    'status' => 1
                  ]);
                }
            }
            }
        });


       
        return $form;
    }
}
