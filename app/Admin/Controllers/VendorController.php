<?php

namespace App\Admin\Controllers;

use App\Models\Vendor;
use App\Models\Status;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Admin;
use App\Admin\Actions\Vendor\ChangeStatus;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;

class VendorController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Vendor';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Vendor());

        $grid->column('id', __('Id'));
        $grid->column('vendor_name', __('Vendor Name'));
        $grid->column('vendor_email', __('Vendor Email'));
        $grid->column('phone_number', __('Phone Number'));
        //$grid->column('password', __('Password'));
        //$grid->column('vendor_rating', __('Vendor Rating'));
        $grid->column('user_name', __('User Name'));
        $grid->column('store_name', __('Store Name'));
        //$grid->column('created_at', __('Created at'))->hide();
        //$grid->column('updated_at', __('Updated at'))->hide();
        // $grid->column('status', __('Status'))->display(function($status){
        //     $status_name = Status::where('id',$status)->value('status_name');
        //     if ($status == 1) {
        //         return "<span class='label label-success'>$status_name</span>";
        //     }if ($status == 2) {
        //         return "<span class='label label-danger'>$status_name</span>";
        //     }
        // });

        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });




         $grid->filter(function ($filter) {
             $statuses = Status::where('slug','general')->pluck('status_name', 'id');

            $filter->like('vendor_name', 'Vendor Name');
            $filter->like('vendor_email', 'Vendor Email');
            $filter->like('store_name', 'Store Name');
            $filter->equal('status', 'Status')->select($statuses);
        });

        $grid->column('status')->action(ChangeStatus::class);


        $grid->quickSearch('vendor_name');
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
        $show = new Show(Vendor::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('vendor_name', __('Vendor name'));
        $show->field('vendor_email', __('Vendor email'));
        $show->field('password', __('Password'));
        $show->field('vendor_rating', __('Vendor rating'));
        $show->field('user_name', __('User name'));
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
        $form = new Form(new Vendor());


        $form->text('vendor_name', __('Vendor Name'))->required();
        $form->text('phone_number', __('Phone number'))->rules(function ($form) {
            if (!$id = $form->model()->id) {
                return 'required|unique:vendors,phone_number';
            } else {
                return 'required|unique:vendors,phone_number,'.$form->model()->id;
            }
        });
        $form->text('vendor_email', __('Vendor Email'));
        // ->rules(function ($form) {
        //     if (!$id = $form->model()->id) {
        //         return 'sometimes|max:100|unique:vendors,vendor_email';
        //     } else {
        //         return 'sometimes|max:100|unique:vendors,vendor_email,'.$form->model()->id;
        //     }
        // });
        $form->text('user_name', __('User Name'))->required();
        $form->password('password', __('Password'))->required();
        $form->image('profile_picture', __('Profile Picture'))->uniqueName()->required();
        $form->text('store_name', __('Store Name'))->required();
        $form->image('store_image', __('Store Image'))->uniqueName()->required();
        $form->textarea('description', __('Description'))->required();
        $form->select('status', __('Status'))->options(Status::where('slug','general')->pluck('status_name','id'))->required();



        $form->text('brand', __('Brand'));
        $form->text('model', __('Model'));
        $form->text('manufacturer_county', __('Manufacturer County'));
        $form->text('authenticity', __('Authenticity'));
        $form->text('overall_dimensions_without_packaging', __('Dimensions Without packaging'));
        $form->text('weight_without_packaging', __('Weight Without packaging'));
        $form->text('guarantee', __('Guarantee'));
        $form->text('product_availability_period', __('Product Availaility Period'));
        $form->text('delivery', __('Delivery'));
        $form->text('delivery_price', __('Delivery Price'));
        $form->text('delivery_term', __('Delivery Term'));












        $form->hidden('admin_user_id')->default(0);
        $form->saving(function ($form) {
            if($form->password && $form->model()->password != $form->password)
            {
                $form->password = $this->getEncryptedPassword($form->password);
                DB::table('admin_users')->where('id',$form->admin_user_id)->update([ 'password' => $form->password ]);

            }
            if($form->user_name && $form->model()->user_name != $form->user_name)
            {
                DB::table('admin_users')->where('id',$form->admin_user_id)->update([ 'username' => $form->user_name ]);

            }

            if($form->vendor_name && $form->model()->vendor_name != $form->vendor_name)
            {
                DB::table('admin_users')->where('id',$form->admin_user_id)->update([ 'name' => $form->store_name ]);

            }

            if(!$form->model()->id){
                $id = DB::table('admin_users')->insertGetId(
                        ['username' => $form->user_name, 'password' => $form->password, 'name' => $form->store_name, 'avatar' => $form->profile_picture]
                    );

                    DB::table('admin_role_users')->insert(
                        ['role_id' => 2, 'user_id' => $id ]
                    );
                $form->admin_user_id = $id;
            }
        });



        $form->latlong('latitude', 'longitude', 'Position')->default(['lat' => 41.2995, 'lng' => 69.2401])->required();

     



        $form->saved(function (Form $form) {
            $this->update_profile_image($form->admin_user_id,$form->model()->profile_picture);
            $this->update_status($form->model()->id,$form->model()->status);

            $warehouse_address = [
                'lat'=> $form->model()->latitude,
                'long'=> $form->model()->longitude
            ];
            $form->model()->warehouse_address = $warehouse_address;
            $form->model()->save();
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

    public function update_status($id,$status){
        // $factory = (new Factory())->withDatabaseUri(env('FIREBASE_DB'));
        // $database = $factory->createDatabase();
        // $newPost = $database
        // ->getReference('vendors/'.$id)
        // ->update([
        //     'status' => $status
        // ]);
    }

     function update_profile_image($id,$avatar){
        DB::table('admin_users')
            ->where('id', $id)
            ->update(['avatar' => $avatar]);
    }
}
