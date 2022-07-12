<?php

namespace App\Admin\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\DeliveryPartner;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Facades\Admin;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {

            $content->header('Dashboard');
            $data = array();
            $current_year = date("Y");
            
            
            if(Admin::user()->isRole('vendor')){
                $vendor_id = Vendor::where('admin_user_id',Admin::user()->id)->value('id');
                $data['total_orders'] = Order::where('vendor_id', $vendor_id)->count();
                $data['pending_orders'] = Order::where('status','!=',8)->where('vendor_id', $vendor_id)->count();
                $data['completed_orders'] = Order::where('status','=',8)->where('vendor_id', $vendor_id)->count();

                $customers = Customer::select('id', 'created_at')
                    ->get()
                    ->groupBy(function ($val) {
                        return Carbon::parse($val->created_at)->format('M');
                    });
                $orders = Order::select('id', 'created_at')
                    ->get()
                    ->groupBy(function ($val) {
                        return Carbon::parse($val->created_at)->format('M');
                    });
                $month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                $temp = [];
                foreach ($customers as $c) {
                    $temp[Carbon::parse($c[0]->created_at)->format('M')] = count($c);
                }
                $growth = [];
                foreach ($month as $m) {
                    if (isset($temp[$m])) {
                        $growth[] = $temp[$m];
                    } else {
                        $growth[] = 0;
                    }
    
                }
                $temp_orders = [];
                foreach ($orders as $o) {
                    $temp_orders[Carbon::parse($o[0]->created_at)->format('M')] = count($o);
                }
                $growth_orders = [];
                foreach ($month as $m) {
                    if (isset($temp_orders[$m])) {
                        $growth_orders[] = $temp_orders[$m];
                    } else {
                        $growth_orders[] = 0;
                    }
    
                }
                $data['customers_chart'] = implode(",", $growth);
                $data['orders_chart'] = implode(",", $growth_orders);
                $content->body(view('admin.dashboard', $data));
                
            }elseif(Admin::user()->isRole('administrator')){
                $data['customers'] = Customer::where('status','!=',0)->count();
                $data['total_orders'] = Order::count();
                $data['completed_orders'] = Order::where('status','=',8)->count();
                $data['delivery_boys'] = DeliveryPartner::where('status','!=',0)->count();
                $data['pending_orders'] = Order::where('status','!=',8)->count();
            
            $customers = Customer::select('id', 'created_at')
            ->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->created_at)->format('M');
            });
            $orders = Order::select('id', 'created_at')
                ->get()
                ->groupBy(function ($val) {
                    return Carbon::parse($val->created_at)->format('M');
                });

           

            $month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
           
           
           
           
           
            $temp = [];
            foreach ($customers as $c) {
                $temp[Carbon::parse($c[0]->created_at)->format('M')] = count($c);
            }
            $growth = [];
            foreach ($month as $m) {
                if (isset($temp[$m])) {
                    $growth[] = $temp[$m];
                } else {
                    $growth[] = 0;
                }

            }





            $temp_orders = [];
            foreach ($orders as $o) {
                $temp_orders[Carbon::parse($o[0]->created_at)->format('M')] = count($o);
            }
            $growth_orders = [];
            foreach ($month as $m) {
                if (isset($temp_orders[$m])) {
                    $growth_orders[] = $temp_orders[$m];
                } else {
                    $growth_orders[] = 0;
                }

            }


            

            // $temp_curtomer_viewed_products = [];
            // foreach ($customer_viewed_products as $c) {
            //     $temp_curtomer_viewed_products[Carbon::parse($c[0]->created_at)->format('M')] = count($c);
            // }
            // $growth_cvp = [];
            // foreach ($month as $m) {
            //     if (isset($temp_curtomer_viewed_products[$m])) {
            //         $growth_cvp[] = $temp_curtomer_viewed_products[$m];
            //     } else {
            //         $growth_cvp[] = 0;
            //     }

            // }


           



          
        
            $data['customers_chart'] = implode(",", $growth);
            $data['orders_chart'] = implode(",", $growth_orders);
            


            $cvp_count =  \App\Models\Product::orderBy('total_view', 'desc')->take(10)->pluck('total_view')->toArray();
            $cvp_product_name = \App\Models\Product::orderBy('total_view', 'desc')->take(10)->pluck('product_name')->toArray();
            $data['cvp_chart'] = [ implode(",",$cvp_count), $cvp_product_name];

            $cfp_count =  \App\Models\Product::orderBy('total_like', 'desc')->take(10)->pluck('total_like')->toArray();
            $cfp_product_name = \App\Models\Product::orderBy('total_like', 'desc')->take(10)->pluck('product_name')->toArray();
            $data['cfp_chart'] = [ implode(",",$cfp_count), $cfp_product_name];

            $cshp_count =  \App\Models\Product::orderBy('total_sharing', 'desc')->take(10)->pluck('total_sharing')->toArray();
            $cshp_product_name = \App\Models\Product::orderBy('total_sharing', 'desc')->take(10)->pluck('product_name')->toArray();
            $data['cshp_chart'] = [ implode(",",$cshp_count), $cshp_product_name];

                $content->body(view('admin.dashboard', $data));
            }else{
                
                   return redirect('/admin/products');
            }
        });

    }
}
