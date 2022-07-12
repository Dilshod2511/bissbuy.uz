<?php

namespace App\Admin\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\DeliveryPartner;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\VendorTax;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index(Content $content)
    {

        return Admin::content(function (Content $content) {

            $content->header('Dashboard');
            $data = array();
            $current_year = date("Y");
            
            
            if(Admin::user()->isRole('administrator')){
                
           $vendors = Vendor::get();
        
            foreach($vendors as $vendor){
                $rows[] = [
                   'vendor_name' => $vendor->vendor_name,
                   'total_orders_sum' => Order::where('vendor_id', $vendor->id)->sum('total'),
                   'total_orders_sum_4_status' => Order::where('vendor_id', $vendor->id)->where('status', 4)->sum('total'),
                   'total_tax' => Order::where('vendor_id', $vendor->id)->where('status', 4)->sum('tax'),
                   'vendor_id' => $vendor->id,
                   'total_tax_paid' => VendorTax::where('vendor_id', $vendor->id)->sum('amount'),
                   'debt' => $vendor->debt
                ];
            }

            //echo $table->render();

                $data['vendors'] = $vendors;
                $data['table'] = $rows;
                $content->body(view('admin.statistic', $data));
                
            }
        });

    }


    public function show(Request $request, int $id)
    {

        if($request->input('date_from') != null  && $request->input('date_to') != null )
        {
            session(['date_from' => $request->input('date_from')]);
            session(['date_to' => $request->input('date_to')]);
        }

        session(['vendor_id' => $id]);

        return Admin::content(function (Content $content) {
            $id = session('vendor_id');
            $date_from = session('date_from');
            $date_to = session('date_to');
            $result = null;

            if($date_from != null && $date_to != null){
                $orders = Order::whereBetween('created_at', [$date_from, $date_to])->get();
                $result = True;
            }else{
                $orders = Order::where('vendor_id', $id)->get();
            }

            

            $vendor_name = Vendor::find($id)->vendor_name;

            $content->header( $vendor_name);
            $data = array();
            $current_year = date("Y");
            
            
            
            $data['orders'] = $orders;
            $data['vendor_name'] =  $vendor_name;
            $data['result'] = $result;
            session()->forget(['vendor_id', 'date_from', 'date_to']);

            if(Admin::user()->isRole('administrator')){
                $content->body(view('admin.statistic_details', $data));
            }
        });
    }


    public function edit(int $id)
    {

        session(['vendor_id' => $id]);
        return Admin::content(function (Content $content) {
            $id = session('vendor_id');


            $content->header('Погашение задолженности');
            $data = array();
            $current_year = date("Y");
            
        
            $vendor = Vendor::find($id);
        
   
                $rows = [
                   'vendor_name' => $vendor->vendor_name,
                   'total_orders_sum' => Order::where('vendor_id', $vendor->id)->sum('total'),
                   'total_orders_sum_4_status' => Order::where('vendor_id', $vendor->id)->where('status', 4)->sum('total'),
                   'total_tax' => Order::where('vendor_id', $vendor->id)->where('status', 4)->sum('tax'),
                   'vendor_id' => $vendor->id,
                   'total_tax_paid' => VendorTax::where('vendor_id', $vendor->id)->sum('amount'),
                   'debt' => $vendor->debt
                ];
  
                $data['vendor'] = $vendor;
                $data['table'] = $rows;
        

            session()->forget(['vendor_id']);

            if(Admin::user()->isRole('administrator')){
                $content->body(view('admin.statistic_edit', $data));
            }
        });
    }


    public function store(Request $request)
    {
        $vendor =  Vendor::find($request->vendor_id);
        $debt_before =$vendor->debt;
        $debt_after =$vendor->debt - $request->amount;

        $vendor->debt = $debt_after;
        $vendor->save();


        VendorTax::create([
            'vendor_id' => $request->vendor_id,
            'amount' => $request->amount,
            'debt_before' => $debt_before,
            'debt_after' => $debt_after
        ]);

        return redirect()->back()->withErrors(['msg' => 'Транзакция прошел успешно']);
    }
}
