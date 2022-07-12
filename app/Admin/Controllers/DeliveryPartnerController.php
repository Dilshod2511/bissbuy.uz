<?php

namespace App\Admin\Controllers;

use App\Models\DeliveryPartner;
use App\Models\Status;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DeliveryPartnerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Delivery Partner';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DeliveryPartner());

        $grid->column('id', __('Id'));
        $grid->column('delivery_email', __('Email'));
        //$grid->column('password', __('Password'));
        $grid->column('phone_number', __('Phone Number'));
        $grid->column('delivery_boy_name', __('Name'));
        $grid->column('profile_picture', __('Profile Picture'))->hide();
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
            $statuses = Status::where('slug','general')->pluck('status_name', 'id');
        
            $filter->equal('status', 'Status')->select($statuses);
            $filter->like('delivery_boy__name', 'Name');
            $filter->like('delivery_email', 'Email');
            $filter->like('phone_number', 'Phone Number');
                 
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
        $show = new Show(DeliveryPartner::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('delivery_email', __('Delivery email'));
        $show->field('password', __('Password'));
        $show->field('phone_number', __('Phone number'));
        $show->field('delivery_boy__name', __('Delivery name'));
        $show->field('fcm_token', __('Fcm token'));
        $show->field('profile_picture', __('Profile picture'));
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
        $form = new Form(new DeliveryPartner());

        $form->text('delivery_boy_name', __('Name'))->required();
        $form->text('delivery_email', __('Email'))->required();
        $form->text('phone_number', __('Phone number'))->rules(function ($form) {
            if (!$id = $form->model()->id) {
                return 'numeric|digits_between:9,20|required|unique:delivery_partners,phone_number|unique:customers,phone_number';
            } else {
                return 'numeric|digits_between:9,20|required|unique:customers,phone_number|unique:delivery_partners,phone_number,'.$form->model()->id;
            }
        });
        $form->password('password', __('Password'))->required();
        $form->image('profile_picture', __('Profile picture'))->move('partners')->required()->uniqueName();
        $form->select('status', __('Status'))->options(Status::where('slug','general')->pluck('status_name','id'))->required();

        $form->saving(function ($form) {
            if($form->password && $form->model()->password != $form->password)
            {
                $form->password = $this->getEncryptedPassword($form->password);
            }
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
    public function getEncryptedPassword($input, $rounds = 12) {
        $salt = "";
        $saltchars = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
        for ($i = 0; $i < 22; $i++) {
            $salt .= $saltchars[array_rand($saltchars)];
        }
        return crypt($input, sprintf('$2y$%2d$', $rounds) . $salt);
    }
}
