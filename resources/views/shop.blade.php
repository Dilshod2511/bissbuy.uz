@include('templates.header')
@include('templates.product_modal')
     
      <div class="container">
        <!-- HERO SECTION-->
        <section class="py-5 bg-light">
          <div class="container">
            <div class="row px-4 px-lg-5 py-lg-4 align-items-center">
              <div class="col-lg-6">
                <h1 class="h2 text-uppercase mb-0">Shop</h1>
              </div>
              <div class="col-lg-6 text-lg-right">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb justify-content-lg-end mb-0 px-0">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Shop</li>
                  </ol>
                </nav>
              </div>
            </div>
          </div>
        </section>
        <section class="py-5">
          <div class="container p-0">
            <div class="row">
              <!-- SHOP SIDEBAR-->
              <div class="col-lg-3 order-2 order-lg-1">
                <h5 class="text-uppercase mb-4">Categories</h5>
                      @foreach($categories as $key => $category)
                <div class="py-2 px-4 bg-dark text-white mb-3"><strong class="small text-uppercase font-weight-bold">{{$category->category_name}}</strong></div>
                <ul class="list-unstyled small text-muted pl-lg-4 font-weight-normal">
                      @foreach($category['sub_categories'] as $key => $sub_category)
                  <li class="mb-2 @if( Request::segment(3)  == $sub_category['id']) active @elseif($sub_category['id']  == $category_id) active @endif"><a class="reset-anchor" href="/shop/product_by_category/{{$sub_category['id']}}">{{$sub_category['category_name']}}</a></li>
                  @endforeach
                  </ul>
                  @endforeach
              </div>
              <!-- SHOP LISTING-->
              <div class="col-lg-9 order-1 order-lg-2 mb-5 mb-lg-0">
                <div class="row mb-3 align-items-center">
                  <div class="col-lg-6 mb-2 mb-lg-0">
                    <!-- <p class="text-small text-muted mb-0">Showing 1–12 of 53 results</p> -->
                  </div>
                </div>
                @if(count($products) > 0)
                <div class="row">
                  <!-- PRODUCT-->
                  @foreach($products as $value)
                   @php
                      {{ $image = env('IMG_URL').$value->cover_image; }}
                  @endphp
                  <div class="col-lg-4 col-sm-6">
                    <div class="product text-center">
                      <div class="mb-3 position-relative">
                        <div class="badge text-white badge-"></div><a class="d-block" href="detail/{{$value->id }}"><img class="img-fluid w-100" src="{{$image}}" alt="..."></a>
                        <div class="product-overlay">
                          <ul class="mb-0 list-inline">
                            <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-outline-dark" href="#"><i class="far fa-heart"></i></a></li>
                            <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-dark" href="/cart">Add to cart</a></li>
                            <li class="list-inline-item mr-0"><a class="btn btn-sm btn-outline-dark" href="#productView-{{$value->id}}" data-toggle="modal"><i class="fas fa-expand"></i></a></li>
                          </ul>
                        </div>
                      </div>
                      <h6> <a class="reset-anchor" href="detail{{$value->id }}">{{$value->product_name}}</a></h6>
                      <p class="small text-muted">{{$currency}} {{$value->product_price}}</p>
                    </div>
                  </div>
                   @endforeach
                </div>
                @else
                 <div class="py-5">
          <center>
            <img src="{{ asset('web/img/no_product.png') }}" alt="..." width="250"/></a>
              <!-- <h4 class="title ele-title text-uppercase mb-4">No Products</h4> -->
          </center>
        </div>
                @endif

                <!-- PAGINATION-->
                <!-- <nav aria-label="Page navigation example">
                  <ul class="pagination justify-content-center justify-content-lg-end">
                    <li class="page-item"><a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">»</span></a></li>
                  </ul>
                </nav> -->
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