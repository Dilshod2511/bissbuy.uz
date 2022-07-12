<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Vendor;
use App\Models\Customer;
use App\Models\PromoCode;
use App\Models\DeliveryPartner;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;

class OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Order';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order());
        
        if(Admin::user()->isRole('vendor')){
            $grid->model()->where('vendor_id', Vendor::where('admin_user_id',Admin::user()->id)->value('id'));
        }
        $grid->model()->orderBy('id','desc');

        $grid->column('id', __('Id'));
        $grid->column('customer_id', __('Customer'))->display(function($customer_id){
            return Customer::where('id',$customer_id)->value('first_name');
        });
        $grid->column('referred_id', __('Referred By'))->display(function($customer_id){
            if($customer_id){
            return Customer::where('id',$customer_id)->value('first_name');
            }else{
                return '---';
            }
        });
        //$grid->column('customer_address_id', __('Customer Address'));
        $grid->column('vendor_id', __('Vendor'))->display(function($vendor_id){
            return Vendor::where('id',$vendor_id)->value('vendor_name');
        });
        $grid->column('total', __('Total'));
       
        //$grid->column('shipping_price', __('Shipping Price'));
        //$grid->column('shipping_method', __('Shipping Method'));
        $grid->column('promo_id', __('Promo'))->display(function($promo){
            if($promo){
            return PromoCode::where('id',$promo)->value('promo_code');
            }else{
                return '---';
            }
        });
        $grid->column('discount', __('Discount'));
        $grid->column('sub_total', __('Sub Total'));
        $grid->column('tax', __('Tax'));
        $grid->column('paid_tax', __('Paid Tax'));
        $grid->column('status', __('Status'))->display(function($status){
            $label_name = OrderStatus::where('id',$status)->value('status');
            if ($status == 4) {
                return "<span class='label label-success'>$label_name</span>";
            } else {
                return "<span class='label label-warning'>$label_name</span>";
            }
        });
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

         $grid->filter(function ($filter) {
            $vendor = Vendor::pluck('vendor_name', 'id');
            $customer = Customer::pluck('first_name', 'id');
        
            $filter->equal('vendor_id', 'Vendor')->select($vendor);
            $filter->equal('customer_id', 'Customer')->select($customer);
            
                 
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
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('customer_id', __('Customer id'));
        $show->field('customer_address_id', __('Customer address id'));
        $show->field('vendor_id', __('Vendor id'));
        $show->field('total', __('Total'));
        $show->field('shipping_price', __('Shipping price'));
        $show->field('shipping_method', __('Shipping method'));
        $show->field('promo_id', __('Promo id'));
        $show->field('discount', __('Discount'));
        $show->field('sub_total', __('Sub total'));
        $show->field('tax', __('Tax'));
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
       $form = new Form(new Order);
        $statuses = OrderStatus::where('id','<',5)->pluck('status', 'id');
        $delivery_boys = DeliveryPartner::where('status',1)->pluck('delivery_boy_name', 'id');
        $vendors = Vendor::pluck('store_name', 'id');
        
        if(Admin::user()->isRole('vendor')){
            $form->hidden('vendor_id')->value(Vendor::where('admin_user_id',Admin::user()->id)->value('id'));
        }else{
            $form->select('vendor_id', __('Vendor Id'))->options($vendors)->rules(function ($form) {
                return 'required';
            });
        }
        $form->text('id', __('Order Id'))->readonly();
        $form->select('delivered_by', __('Delivered by'))->options($delivery_boys);
        $form->select('status', __('Status'))->options($statuses)->default(1)->rules(function ($form) {
            return 'required';
        });
        $form->saving(function (Form $form) {
           if($form->delivered_by > 0 && $form->status ==1){
                $error = new MessageBag([
                    'title'   => 'Warning',
                    'message' => 'Please change order status...',
                ]);

                return back()->with(compact('error'));
           }
        });

        $form->text('paid_tax', __('Paid Tax'));
        /*$form->saved(function (Form $form) {
            
            // Fcm Message
            $fcm_message = FcmNotification::where('id',$form->model()->status)->first();
            $description = $this->str_replace("{order_id}",'#'.$form->model()->order_id,$fcm_message->customer_description);
            $token = Customer::where('id',$form->model()->customer_id)->value('fcm_token');
            $this->update_history($form->model()->id);
        });*/
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
