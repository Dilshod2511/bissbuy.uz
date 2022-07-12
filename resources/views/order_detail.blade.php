@include('templates.header')
    <link rel="stylesheet" href="{{ asset('web/css/order_detail_style.css') }}">
      <div class="container">
        <!-- HERO SECTION-->
        <section class="py-2 bg-light">
          <div class="container">
            <div class="row px-4 px-lg-5 py-lg-4 align-items-center">
              <div class="col-lg-6">
                <h1 class="h2 text-uppercase mb-0">Order Details</h1>
              </div>
             <!--  <div class="col-lg-6 text-lg-right">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb justify-content-lg-end mb-0 px-0">
                    <li class="breadcrumb-item">OrderList</li>
                    <li class="breadcrumb-item active"> {{Auth::user()->first_name}}!</li>
                  </ol>
                </nav>
              </div> -->
            </div>
          </div>
        </section>
        <section class="">
          <div class="container p-0">
            <article class="card" style="padding:0px!important">
        <header class="card-header"> My Orders / Tracking </header>
        <div class="card-body">
            <h6>Order ID: {{$order_detail->id }}</h6>

        <div class="row justify-content-center">
            <div class="col-12">
                <ul id="progressbar" class="text-center">
                @foreach($order_status as $val)
               
                    <li class="@if($order_detail->status == $val['id']) active @endif step0"></li>
              @endforeach
                </ul>
            </div>
        </div>
        <div class="row justify-content-between top">
                @foreach($order_status as $val)
                 <?php
                  if($val['id'] == 1 ){
                   $image = env('APP_URL').'/web/img/order_pending.png'; 
                 }else if($val['id'] == 2 ){
                   $image = env('APP_URL').'/web/img/order_accepted.png'; 
                 }else if($val['id'] == 3 ){
                   $image = env('APP_URL').'/web/img/on_the_way.png'; 
                 }else if($val['id'] == 4 ){
                   $image = env('APP_URL').'/web/img/delivered.png'; 
                 }
                 ?>
            <div class="row d-flex icon-content"> <img class="icon" src="{{$image}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">Order<br>{{$val['status']}}</p>
                </div>
            </div>
            @endforeach
            <!-- <div class="row d-flex icon-content"> <img class="icon" src="https://i.imgur.com/GiWFtVu.png">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">Order<br>Designing</p>
                </div>
            </div>
            <div class="row d-flex icon-content"> <img class="icon" src="https://i.imgur.com/u1AzR7w.png">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">Order<br>Shipped</p>
                </div>
            </div>
            <div class="row d-flex icon-content"> <img class="icon" src="https://i.imgur.com/HdsziHP.png">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">Order<br>Arrived</p>
                </div>
            </div> -->
        </div>
        <hr>
                <div class=" row justify-content-between top">
                    <!-- <div class="col"> <strong>Estimated Delivery time:</strong> <br>29 nov 2019 </div> -->
                    <div class="col-6" style="padding-left: 0px;"> <strong>Delivery Address:</strong> <br> {{$order_detail->google_address}}</div>
                    <div class="col-3" style="padding-left: 0px;"> <strong>Status:</strong> <br>{{$order_detail->status_name}} </div>
                    </div>
                <hr>
                <div class=" row justify-content-between top">
             <h6 class="text-black text-uppercase"><b>Your Items</b></h6>
           </div>
              <hr>
                @foreach($items as $item)
                <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-2">
                  <p >{{ $item['qty'] }}X</p>
                </div>
                <div class="col-md-4">
                  <p >{{ $item['product_name'] }}</p>
                </div>
                 <div class="col-md-4">
                  <p>{{ $currency }} {{ $item['total_price'] }}</p>
                </div>
              </div>
              @endforeach
              <hr>
              <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                  <p>Subtotal</p>
                </div>
                <div class="col-md-4">
                  <p >{{ $currency }} {{ $order_detail->sub_total }}</p>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                  <p>Discount</p>
                </div>
                <div class="col-md-4">
                  <p>{{ $currency }} {{ $order_detail->discount }}</p>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                  <p>Tax</p>
                </div>
                <div class="col-md-4">
                  <p>{{ $currency }} {{ $order_detail->tax }}</p>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                  <p class="detail-content"><b>Total amount</b></p>
                </div>
                <div class="col-md-4">
                  <p class="detail-content"><b>{{ $currency }} {{ $order_detail->total }}</b></p>
                </div>
              </div>
           
            <hr>
            <div class="text-right">
            <a href="/order" class="btn btn-dark" data-abc="true"> <i class="fa fa-chevron-left"></i> Back to orders</a>
          </div>
        </div>
    </article>
          </div>
        </section>
      </div>

@include('templates.footer')
<style>
.active{
  color: #b68b23!important;
}
</style>