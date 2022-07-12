@include('templates.header')
@include('templates.product_modal')
      <!-- HERO SECTION-->
      <div class="container">
        <section class="hero pb-3 bg-cover bg-center d-flex align-items-center" style="background: url({{ asset('web/img/banner.jpeg') }})">
          <div class="container py-5">
            <div class="row px-4 px-lg-5">
              <div class="col-lg-6">
                <!-- <p class="text-muted small text-uppercase mb-2">New Inspiration 2021</p> -->
                <h1 class="h2 text-uppercase mb-3">New Inspiration 2021</h1>
                <a class="btn btn-dark" href="/shop">Browse collections</a>
              </div>
            </div>
          </div>
        </section>
        <!-- CATEGORIES SECTION-->
        <section class="pt-5">
          <header class="text-center">
            <!-- <p class="small text-muted small text-uppercase mb-1">Carefully created collections</p> -->
            <h2 class="h5 text-uppercase mb-4">Browse our categories</h2>
          </header>
          <div class="row">
            @foreach($categories as $value)
             @php
                {{ $image = env('IMG_URL').$value->category_image; }}
            @endphp
           <!--  <div class="col-md-4 mb-4 mb-md-0">
              <a class="category-item" href="shop">
                <img class="img-fluid-custom" src="{{ $image }}" alt="">
                <strong class="category-item-title">{{$value -> category_name}}</strong>
              </a>
            </div> -->
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12 mb-4 mb-lg-5 text-center text-sm-start">
                        <picture class="d-block position-relative circle-1">
                            <img class="img-fluid-custom" src="{{ $image }}" alt="">
                        </picture>
                        <h5 class="mt-3">{{ $value->category_name }} </h5>
                       <!--  <p>
                           {{ $value->description }}
                        </p> -->
                        <a class="fw-bold" href="/shop/{{$value->id}}">View</a>
                    </div>
            @endforeach
          </div>
        </section>
        <!-- TRENDING PRODUCTS-->
        <section class="py-5">
          <header>
            <p class="small text-muted small text-uppercase mb-1">Made the hard way</p>
            <h2 class="h5 text-uppercase mb-4">Top liked products</h2>
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
        <!-- SERVICES-->
        <section class="py-5 bg-light">
          <div class="container">
            <div class="row text-center">
              <div class="col-lg-4 mb-3 mb-lg-0">
                <div class="d-inline-block">
                  <div class="media align-items-end">
                    <svg class="svg-icon svg-icon-big svg-icon-light">
                      <use xlink:href="#delivery-time-1"> </use>
                    </svg>
                    <div class="media-body text-left ml-3">
                      <h6 class="text-uppercase mb-1">Free shipping</h6>
                      <p class="text-small mb-0 text-muted">Free shipping worlwide</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 mb-3 mb-lg-0">
                <div class="d-inline-block">
                  <div class="media align-items-end">
                    <svg class="svg-icon svg-icon-big svg-icon-light">
                      <use xlink:href="#helpline-24h-1"> </use>
                    </svg>
                    <div class="media-body text-left ml-3">
                      <h6 class="text-uppercase mb-1">24 x 7 service</h6>
                      <p class="text-small mb-0 text-muted">Free shipping worlwide</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="d-inline-block">
                  <div class="media align-items-end">
                    <svg class="svg-icon svg-icon-big svg-icon-light">
                      <use xlink:href="#label-tag-1"> </use>
                    </svg>
                    <div class="media-body text-left ml-3">
                      <h6 class="text-uppercase mb-1">Festival offer</h6>
                      <p class="text-small mb-0 text-muted">Free shipping worlwide</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- NEWSLETTER-->
        <!-- <section class="py-5">
          <div class="container p-0">
            <div class="row">
              <div class="col-lg-6 mb-3 mb-lg-0">
                <h5 class="text-uppercase">Let's be friends!</h5>
                <p class="text-small text-muted mb-0">Nisi nisi tempor consequat laboris nisi.</p>
              </div>
              <div class="col-lg-6">
                <form action="#">
                  <div class="input-group flex-column flex-sm-row mb-3">
                    <input class="form-control form-control-lg py-3" type="email" placeholder="Enter your email address" aria-describedby="button-addon2">
                    <div class="input-group-append">
                      <button class="btn btn-dark btn-block" id="button-addon2" type="submit">Subscribe</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </section> -->
      </div>
@include('templates.footer')
<style>

</style>