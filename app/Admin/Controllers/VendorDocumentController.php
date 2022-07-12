<?php

namespace App\Admin\Controllers;

use App\Models\VendorDocument;
use App\Models\Status;
use App\Models\Vendor;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class VendorDocumentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Vendor Document';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new VendorDocument);

        $grid->column('id', __('Id'));
        $grid->column('vendor_id', __('Vendor'))->display(function($vendor){
            $vendor_name = Vendor::where('id',$vendor)->value('store_name');
            return $vendor_name;
        });
        $grid->column('id_proof', __('Id proof'))->image();
        $grid->column('id_proof_status', __('Id proof status'))->display(function($status){
            $status_name = Status::where('id',$status)->value('status_name');
            if ($status == 3) {
                return "<span class='label label-warning'>$status_name</span>";
            }if ($status == 4) {
                return "<span class='label label-success'>$status_name</span>";
            } else {
                return "<span class='label label-danger'>$status_name</span>";
            }
        });
        $grid->column('certificate', __('Certificate'))->image();
        $grid->column('certificate_status', __('Certificate status'))->display(function($status){
            $status_name = Status::where('id',$status)->value('status_name');
            if ($status == 3) {
                return "<span class='label label-warning'>$status_name</span>";
            }if ($status == 4) {
                return "<span class='label label-success'>$status_name</span>";
            } else {
                return "<span class='label label-danger'>$status_name</span>";
            }
        });
        //$grid->column('created_at', __('Created at'));
       // $grid->column('updated_at', __('Updated at'));
        
        $grid->disableExport();
        //$grid->disableCreation();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        $grid->filter(function ($filter) {
             

            $filter->equal('id_proof_status', 'Id Proof Status');
            $filter->equal('cerficate_status', 'Certificate Status');
        
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
        $show = new Show(VendorDocument::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('vendor_id', __('Vendor id'));
        $show->field('id_proof', __('Id proof'));
        $show->field('id_proof_status', __('Id proof status'));
        $show->field('certificate', __('Certificate'));
        $show->field('certificate_status', __('Certificate status'));
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
        $form = new Form(new VendorDocument);

        $form->select('vendor_id', __('Vendor id'))->options(Vendor::pluck('store_name','id'))->rules(function ($form) {
            return 'required';
        });
        $form->image('id_proof', __('Id proof'))->uniqueName();
        $form->select('id_proof_status', __('Id proof status'))->options(Status::where('slug','document')->pluck('status_name','id'))->rules(function ($form) {
            return 'required';
        });
        $form->image('certificate', __('Certificate'))->uniqueName();
        $form->select('certificate_status', __('Certificate status'))->options(Status::where('slug','document')->pluck('status_name','id'))->rules(function ($form) {
            return 'required';
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
        $form->saved(function (Form $form) {
            if($form->model()->id_proof_status == 4 && $form->model()->certificate_status == 4){
                Vendor::where('id',$form->model()->vendor_id)->update([ 'document_approved_status' => 1 ]);
            }else{
                Vendor::where('id',$form->model()->vendor_id)->update([ 'document_approved_status' => 0 ]);
                //Vendor::where('id',$form->model()->vendor_id)->update([ 'document_update_status' => 0 ]);
            }
        });

        return $form;
    }
}
