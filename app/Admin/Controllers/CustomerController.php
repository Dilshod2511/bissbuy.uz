<?php

namespace App\Admin\Controllers;

use App\Models\Customer;
use App\Models\Status;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Hash;

class CustomerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Customer';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Customer());

        $grid->column('id', __('Id'));
        $grid->column('first_name', __('First Name'));
        $grid->column('last_name', __('Last Name'));
        $grid->column('phone_number', __('Phone Number'));
        // $grid->column('phone_with_code', __('Phone With Code'));
        $grid->column('email', __('Email'));
        $grid->column('password', __('Password'))->hide();
        $grid->column('wallet', __('Wallet'));
        $grid->column('fcm_token', __('Fcm token'))->hide();
        $grid->column('status', __('Status'))->display(function($status){
            $status_name = Status::where('id',$status)->value('status_name');
            if ($status == 1) {
                return "<span class='label label-success'>$status_name</span>";
            }if ($status == 2) {
                return "<span class='label label-danger'>$status_name</span>";
            } 
        });
        $grid->column('profile_picture', __('Profile picture'))->hide();
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

         $grid->filter(function ($filter) {
            $statuses = Status::where('slug','general')->pluck('status_name', 'id');
        
            $filter->equal('status', 'Status')->select($statuses);
            $filter->like('first_name', 'First Name');
            $filter->like('last_name', 'Last Name');
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
        $show = new Show(Customer::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('first_name', __('First name'));
        $show->field('last_name', __('Last name'));
        $show->field('phone_number', __('Phone number'));
        // $show->field('phone_with_code', __('Phone with code'));
        $show->field('email', __('Email'));
        $show->field('password', __('Password'));
        $show->field('wallet', __('Wallet'));
        $show->field('fcm_token', __('Fcm token'));
        $show->field('status', __('Status'));
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
        $form = new Form(new Customer());

        $form->text('first_name', __('First name'))->required();
        $form->text('last_name', __('Last name'))->required();
        $form->text('phone_number', __('Phone number'))->rules('required|numeric');
        // $form->text('phone_with_code', __('Phone with code'))->required();
        $form->text('email', __('Email'));
        $form->text('password', __('Password'));
        $form->decimal('wallet', __('Wallet'));
        $form->image('profile_picture', __('Profile picture'))->move('customers')->required()->uniqueName();
        $form->select('status', __('Status'))->options(Status::where('slug','general')->pluck('status_name','id'))->required();

        $form->saving(function ($form) {
            if($form->password && $form->model()->password != $form->password)
            {
                $form->password = Hash::make($form->password); //$this->getEncryptedPassword($form->password);
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
