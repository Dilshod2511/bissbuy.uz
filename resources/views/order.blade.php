@include('templates.header')
      <div class="container">
        <!-- HERO SECTION-->
        <section class="py-5 bg-light">
          <div class="container">
            <div class="row px-4 px-lg-5 py-lg-4 align-items-center">
              <div class="col-lg-6">
                <h1 class="h2 text-uppercase mb-0">Orders</h1>
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
        <section class="py-2">
          <div class="container p-0">
            <div class="row">
              <!-- SHOP LISTING-->
              <div class="col-lg-12 order-1 order-lg-2 mb-5 mb-lg-0">
               @foreach($orders_list as $value)
                <article class="card" style="margin-top:40px!important; margin-bottom:40px!important">
                    <header class="card-header"> Order ID: {{$value->id}}</header>
                    <div class="card-body">
                            <div class="row">
                                <!-- <div class="col-4"> <strong>Estimated Delivery time:</strong> <br>29 nov 2019 </div> -->
                                <div class="col-6"> <strong>Delivery Address:</strong> <br> {{$value->google_address}}</div>
                                <div class="col-4"> <strong>Status:</strong> <br> {{$value->status_name}} </div>
                                <div class="col-2">
                                <a href="order_detail/{{$value->id}}" class="btn btn-dark" data-abc="true"> <i class="fa fa-chevron-left"></i> View Detail</a>
                                </div>
                                </div>
                    </div>
                </article>
                @endforeach
              <!--   <div class="row">
                  <div class="col-lg-3 col-sm-6">
                    <div class="product text-center">
                      <div class="mb-3 position-relative">
                        <div class="badge text-white badge-"></div><a class="d-block" href=""><img class="img-fluid w-100" src="" alt="..."></a>
                      </div>
                      <h6> <a class="reset-anchor" href="">My Profile</a></h6>
                    </div>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="product text-center">
                      <div class="mb-3 position-relative">
                        <div class="badge text-white badge-"></div><a class="d-block" href=""><img class="img-fluid w-100" src="" alt="..."></a>
                      </div>
                      <h6> <a class="reset-anchor" href="">My Profile</a></h6>
                    </div>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="product text-center">
                      <div class="mb-3 position-relative">
                        <div class="badge text-white badge-"></div><a class="d-block" href=""><img class="img-fluid w-100" src="" alt="..."></a>
                      </div>
                      <h6> <a class="reset-anchor" href="">My Profile</a></h6>
                    </div>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="product text-center">
                      <div class="mb-3 position-relative">
                        <div class="badge text-white badge-"></div><a class="d-block" href=""><img class="img-fluid w-100" src="" alt="..."></a>
                      </div>
                      <h6> <a class="reset-anchor" href="">My Profile</a></h6>
                    </div>
                  </div>
                </div>
                -->
              </div>
            </div>
          </div>
        </section>
      </div>

@include('templates.footer')
<style>
.active{
  color: #b68b23!important;
}
</style>