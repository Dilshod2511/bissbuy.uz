<?php

namespace App\Admin\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\DeliveryPartner;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\DeliveryEarning;
use App\Models\Product;
use App\Models\VendorTax;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;

class DeliveryStatisticController extends Controller
{
    public function index(Content $content)
    {

        return Admin::content(function (Content $content) {
            $content->header('Dashboard');
            $data = array();
            $current_year = date("Y");
            
            if(Admin::user()->isRole('administrator')){
                
           $partners = DeliveryPartner::get();
        
            foreach($partners as $partner){
                $rows[] = [
                   'id' => $partner->id,
                   'name' => $partner->delivery_boy_name,
                   'total_orders_sum' => Order::where('delivered_by', $partner->id)->whereIn('status', [3,4,6])->count('id'),
                   'total_orders_sum_4_status' => Order::where('delivered_by', $partner->id)->whereIn('status', [4,6])->count('id'),
                   'total_fee' => Order::where('delivered_by', $partner->id)->whereIn('status', [4,6])->sum('delivery_amount'),
                   'earnings' => $partner->earnings
                ];
            }
                $data['vendors'] = $partners;
                $data['table'] = $rows;
                $content->body(view('admin.delivery_statistic', $data));
                
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

        session(['partner_id' => $id]);

        return Admin::content(function (Content $content) {
            $id = session('partner_id');
            $date_from = session('date_from');
            $date_to = session('date_to');
            $result = null;

            if($date_from != null && $date_to != null){
                $orders = Order::where('delivered_by', $id)->whereBetween('created_at', [$date_from, $date_to])->get();
                $result = True;
            }else{
                $orders = Order::where('delivered_by', $id)->whereIn('status',[3,4,6])->get();
            }

            

            $name = DeliveryPartner::find($id)->delivery_boy_name;

            $content->header( $name);
            $data = array();
            $current_year = date("Y");
            
            

            $data['orders'] = $orders;
            $data['name'] =  $name;
            $data['result'] = $result;
            session()->forget(['partner_id', 'date_from', 'date_to']);

            if(Admin::user()->isRole('administrator')){
                $content->body(view('admin.delivery_statistic_details', $data));
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
            
        
            $partner = DeliveryPartner::find($id);
        

                $rows = [
                   'id' => $partner->id,
                   'name' => $partner->delivery_boy_name,
                   'total_orders_sum' => Order::where('delivered_by', $partner->id)->whereIn('status', [3,4,6])->count('id'),
                   'total_orders_sum_4_status' => Order::where('delivered_by', $partner->id)->whereIn('status', [4,6])->count('id'),
                   'total_fee' => Order::where('delivered_by', $partner->id)->whereIn('status', [4,6])->sum('delivery_amount'),
                   'earnings' => $partner->earnings
                ];
      
  
                $data['vendor'] = $partner;
                $data['table'] = $rows;
        

            session()->forget(['vendor_id']);

            if(Admin::user()->isRole('administrator')){
                $content->body(view('admin.delivery_statistic_edit', $data));
            }
        });
    }


    public function store(Request $request)
    {
        $delivery =  DeliveryPartner::find($request->id);
        $balance_before =$delivery->earnings;
        $balance_after = $delivery->earnings + $request->amount;

        $delivery->earnings = $balance_after;
        $delivery->save();


        DeliveryEarning::create([
            'delivery_id' => $request->id,
            'amount' => $request->amount,
            'balance_before' => $balance_before,
            'balance_after' => $balance_after
        ]);

        return redirect()->back()->withErrors(['msg' => 'Транзакция прошел успешно']);
    }
}
