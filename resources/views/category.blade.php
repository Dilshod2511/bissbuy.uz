@include('templates.header')
@include('templates.product_modal')
      <!-- HERO SECTION-->
      <div class="container">
        <section class="py-5">
          <header>
            <h2 class="h5 text-uppercase text-center mb-4"></h2>
          </header>
          <div class="row">
            <!-- PRODUCT-->
            @foreach($top_liked as $value)
             @php
                {{ $image = env('IMG_URL').$value->cover_image; }}
            @endphp
            <div class="col-xl-3 col-lg-4 col-sm-6">
              <div class="product text-center">
                <div class="position-relative mb-3">
                  <div class="badge text-white badge-"></div><a class="d-block" href="detail.html"><img class="img-fluid w-100" src="{{$image}}" alt="..."></a>
                  <div class="product-overlay">
                    <ul class="mb-0 list-inline">
                      <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-outline-dark" href="#"><i class="far fa-heart"></i></a></li>
                      <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-dark" href="cart.html">Add to cart</a></li>
                      <li class="list-inline-item mr-0"><a class="btn btn-sm btn-outline-dark" href="#productView" data-toggle="modal"><i class="fas fa-expand"></i></a></li>
                    </ul>
                  </div>
                </div>
                <h6> <a class="reset-anchor" href="detail.html">{{ $value -> product_name}}</a></h6>
                <p class="small text-muted">{{$currency}} {{ $value -> product_price}}</p>
              </div>
            </div>
            @endforeach
          </div>
        </section>
      </div>
@include('templates.footer')
<style>

</style>