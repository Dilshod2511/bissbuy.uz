@include('templates.header')
@include('templates.product_modal')
 <section class="py-5">
        <div class="container">
          <div class="row mb-5">
            <div class="col-lg-6">
              <!-- PRODUCT SLIDER-->
              @php
                  {{ $image = env('IMG_URL').$data->cover_image; }}
                  @endphp
              <div class="row m-sm-0">
                <!-- <div class="col-sm-2 p-sm-0 order-2 order-sm-1 mt-2 mt-sm-0">
                  <div class="owl-thumbs d-flex flex-row flex-sm-column" data-slider-id="1">
                    <div class="owl-thumb-item flex-fill mb-2 mr-2 mr-sm-0"><img class="w-100" src="{{$image}}" alt="..."></div>
                    <div class="owl-thumb-item flex-fill mb-2 mr-2 mr-sm-0"><img class="w-100" src="{{$image}}" alt="..."></div>
                    <div class="owl-thumb-item flex-fill mb-2 mr-2 mr-sm-0"><img class="w-100" src="{{$image}}" alt="..."></div>
                    <div class="owl-thumb-item flex-fill mb-2"><img class="w-100" src="{{$image}}" alt="..."></div>
                  </div>
                </div> -->
                
                <div class="col-sm-10 order-1 order-sm-2">
                  <a class="d-block" href="{{$image}}" data-lightbox="product" title="{{$data->product_name}}"><img class="img-fluid" src="{{$image}}" alt="..."></a>
                  <!-- <div class="owl-carousel product-slider" data-slider-id="1"><a class="d-block" href="{{$image}}" data-lightbox="product" title="Product item 2"><img class="img-fluid" src="{{$image}}" alt="..."></a><a class="d-block" href="{{$image}}" data-lightbox="product" title="Product item 3"><img class="img-fluid" src="{{$image}}" alt="..."></a><a class="d-block" href="{{$image}}" data-lightbox="product" title="Product item 4"><img class="img-fluid" src="{{$image}}" alt="..."></a></div> -->
                </div>
              </div>
            </div>
            @php
                $cart_data = Session::get('cart', []);
            @endphp
            <!-- PRODUCT DETAILS-->
            <div class="col-lg-6">
              <ul class="list-inline mb-2">
                @if($data->rating == 1)
                <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                @elseif($data->rating == 2)
                 <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                @elseif($data->rating == 3)
                 <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                @elseif($data->rating == 4)
                 <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                @elseif($data->rating == 5)
                 <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="fas fa-star small text-warning"></i></li>
                @else
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                <li class="list-inline-item m-0"><i class="far fa-star small text-warning"></i></li>
                @endif
              </ul>
              <?php
                  if(@$cart_data[$data->category_id.'-'.$data->id]){
                      $qty_count = $cart_data[$data->category_id.'-'.$data->id]['qty'];
                  }else{
                      $qty_count = 0;
                  }
              ?>
              <h1>{{$data->product_name}}</h1>
              <p class="text-muted lead">{{$currency}} {{$data->product_price}}</p>
              <p class="text-small mb-4">{{$data->short_description}}</p>
                  @if($data->current_stock > 0)
              <div class="row align-items-stretch mb-4">
                <div class="col-sm-5 pr-sm-0">
                  <div class="border d-flex align-items-center justify-content-between py-1 px-3 bg-white border-white"><span class="small text-uppercase text-gray mr-4 no-select">Quantity</span>
                    <div class="quantity">
                      <!-- <button class="dec-btn p-0"><i class="fas fa-caret-left"></i></button> -->
                        <button type="button" class="btn btn-number p-0" data-type="minus" data-field="quant[{{ $data->id }}]"><i class="fas fa-minus-square"></i>
                        </button>
                      <input class="form-control border-0 shadow-0 p-0" type="text" name="quant[{{ $data->id }}]" id="quant_{{ $data->id }}" value="{{ $qty_count }}" min="0" max="100" onchange="add_to_cart({{$data->category_id}}, {{$data->id}},{{$data->product_price}} );">
                      <!-- <button class="inc-btn p-0"><i class="fas fa-caret-right"></i></button> -->
                      <button type="button "  class="btn p-0 btn-number" data-type="plus" data-field="quant[{{ $data->id }}]"><i class="fas fa-plus-square"></i>
                     </button>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3 pl-sm-0">
                  <a class="btn btn-dark btn-sm btn-block h-100 d-flex align-items-center justify-content-center px-0" href="/cart">View Cart</a>
                </div>

              </div>
              @else
              <div class="row align-items-stretch mb-4">
                <div class="col-sm-5 pr-sm-0">
                  <a class="btn btn-dark btn-sm btn-block h-100 d-flex align-items-center justify-content-center px-0" href="#">OUT OF STOCK</a>
                </div>
                </div>
                @endif
              @if($data->is_like == 0)
              <a class="btn btn-link text-dark p-0 mb-4" href="#"><i class="far fa-heart mr-2"></i>Add to wish list</a>
              @elseif($data->is_like == 1)
              <a class="btn btn-link text-dark p-0 mb-4" href="#"><i class="fas fa-heart  mr-2 text-danger" aria-hidden="true"></i>Added in Wish list</a>
              @endif
              <br>
              <a class="btn btn-link text-dark p-0 mb-4 mr-4" href="#"><i class="far fa-eye  mr-1 " aria-hidden="true"></i>{{$data->total_view}}</a>
               <a class="btn btn-link text-dark p-0 mb-4 mr-4" href="#"><i class="far fa-heart  mr-1" aria-hidden="true"></i>{{$data->total_like}}</a>
                <a class="btn btn-link text-dark p-0 mb-4 mr-4" href="#"><i class="far fa-share-square  mr-1" aria-hidden="true"></i>{{$data->total_sharing}}</a>
                <a class="btn btn-link text-dark p-0 mb-4 mr-4" href="#"><i class="far fa-comment  mr-1 " aria-hidden="true"></i>{{$data->total_comment}}</a>
                <br>
              <ul class="list-unstyled small d-inline-block">
                <li class="px-3 py-2 mb-1 bg-white"><strong class="text-uppercase">BRAND:</strong><span class="ml-2 text-muted">{{$data->brand_name}}</span></li>
                <li class="px-3 py-2 mb-1 bg-white text-muted"><strong class="text-uppercase text-dark">Category:</strong><a class="reset-anchor ml-2" href="#">{{$data->category_name}}</a></li>
                <li class="px-3 py-2 mb-1 bg-white text-muted"><strong class="text-uppercase text-dark">Vendor:</strong><a class="reset-anchor ml-2" href="#">{{$data->vendor_name}}</a></li>
              </ul>
            </div>
          </div>
          <!-- DETAILS TABS-->
        <!--   <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="true">Description</a></li>
            <li class="nav-item"><a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">Reviews</a></li>
          </ul>
          <div class="tab-content mb-5" id="myTabContent">
            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
              <div class="p-4 p-lg-5 bg-white">
                <h6 class="text-uppercase">Product description </h6>
                <p class="text-muted text-small mb-0">L{{$data->short_description}}</p>
              </div>
            </div>
            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
              <div class="p-4 p-lg-5 bg-white">
                <div class="row">
                  <div class="col-lg-8">
                    <div class="media mb-3"><img class="rounded-circle" src="img/customer-1.png" alt="" width="50">
                      <div class="media-body ml-3">
                        <h6 class="mb-0 text-uppercase">Jason Doe</h6>
                        <p class="small text-muted mb-0 text-uppercase">20 May 2020</p>
                        <ul class="list-inline mb-1 text-xs">
                          <li class="list-inline-item m-0"><i class="fas fa-star text-warning"></i></li>
                          <li class="list-inline-item m-0"><i class="fas fa-star text-warning"></i></li>
                          <li class="list-inline-item m-0"><i class="fas fa-star text-warning"></i></li>
                          <li class="list-inline-item m-0"><i class="fas fa-star text-warning"></i></li>
                          <li class="list-inline-item m-0"><i class="fas fa-star-half-alt text-warning"></i></li>
                        </ul>
                        <p class="text-small mb-0 text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                      </div>
                    </div>
                    <div class="media"><img class="rounded-circle" src="img/customer-2.png" alt="" width="50">
                      <div class="media-body ml-3">
                        <h6 class="mb-0 text-uppercase">Jason Doe</h6>
                        <p class="small text-muted mb-0 text-uppercase">20 May 2020</p>
                        <ul class="list-inline mb-1 text-xs">
                          <li class="list-inline-item m-0"><i class="fas fa-star text-warning"></i></li>
                          <li class="list-inline-item m-0"><i class="fas fa-star text-warning"></i></li>
                          <li class="list-inline-item m-0"><i class="fas fa-star text-warning"></i></li>
                          <li class="list-inline-item m-0"><i class="fas fa-star text-warning"></i></li>
                          <li class="list-inline-item m-0"><i class="fas fa-star-half-alt text-warning"></i></li>
                        </ul>
                        <p class="text-small mb-0 text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
          <!-- RELATED PRODUCTS-->
          <!-- <h2 class="h5 text-uppercase mb-4">Related products</h2>
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="product text-center skel-loader">
                <div class="d-block mb-3 position-relative"><a class="d-block" href="detail.html"><img class="img-fluid w-100" src="img/product-1.jpg" alt="..."></a>
                  <div class="product-overlay">
                    <ul class="mb-0 list-inline">
                      <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-outline-dark" href="#"><i class="far fa-heart"></i></a></li>
                      <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-dark" href="#">Add to cart</a></li>
                      <li class="list-inline-item mr-0"><a class="btn btn-sm btn-outline-dark" href="#productView" data-toggle="modal"><i class="fas fa-expand"></i></a></li>
                    </ul>
                  </div>
                </div>
                <h6> <a class="reset-anchor" href="detail.html">Kui Ye Chen’s AirPods</a></h6>
                <p class="small text-muted">$250</p>
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="product text-center skel-loader">
                <div class="d-block mb-3 position-relative"><a class="d-block" href="detail.html"><img class="img-fluid w-100" src="img/product-2.jpg" alt="..."></a>
                  <div class="product-overlay">
                    <ul class="mb-0 list-inline">
                      <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-outline-dark" href="#"><i class="far fa-heart"></i></a></li>
                      <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-dark" href="#">Add to cart</a></li>
                      <li class="list-inline-item mr-0"><a class="btn btn-sm btn-outline-dark" href="#productView" data-toggle="modal"><i class="fas fa-expand"></i></a></li>
                    </ul>
                  </div>
                </div>
                <h6> <a class="reset-anchor" href="detail.html">Air Jordan 12 gym red</a></h6>
                <p class="small text-muted">$300</p>
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="product text-center skel-loader">
                <div class="d-block mb-3 position-relative"><a class="d-block" href="detail.html"><img class="img-fluid w-100" src="img/product-3.jpg" alt="..."></a>
                  <div class="product-overlay">
                    <ul class="mb-0 list-inline">
                      <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-outline-dark" href="#"><i class="far fa-heart"></i></a></li>
                      <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-dark" href="#">Add to cart</a></li>
                      <li class="list-inline-item mr-0"><a class="btn btn-sm btn-outline-dark" href="#productView" data-toggle="modal"><i class="fas fa-expand"></i></a></li>
                    </ul>
                  </div>
                </div>
                <h6> <a class="reset-anchor" href="detail.html">Cyan cotton t-shirt</a></h6>
                <p class="small text-muted">$25</p>
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="product text-center skel-loader">
                <div class="d-block mb-3 position-relative"><a class="d-block" href="detail.html"><img class="img-fluid w-100" src="img/product-4.jpg" alt="..."></a>
                  <div class="product-overlay">
                    <ul class="mb-0 list-inline">
                      <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-outline-dark" href="#"><i class="far fa-heart"></i></a></li>
                      <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-dark" href="#">Add to cart</a></li>
                      <li class="list-inline-item mr-0"><a class="btn btn-sm btn-outline-dark" href="#productView" data-toggle="modal"><i class="fas fa-expand"></i></a></li>
                     ul>
                  </div>
                </div>
                <h6> <a class="reset-anchor" href="detail.html">Timex Unisex Originals</a></h6>
                <p class="small text-muted">$351</p>
              </div>
            </div>
          </div> -->
        </div>
      </section>
    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

@include('templates.footer')
<script type="text/javascript">
function add_to_cart(category_id,product_id,price){
  // alert(category_name); return false;
    var qty = $("#quant_"+product_id).val();
    $.ajax({
        url: '/add_to_cart',
        type: 'POST',
        data: {_token: $("#csrf-token").val(), qty:qty, product_id:product_id, category_id:category_id, price:price},
        dataType: 'JSON',
        success: function (data) { 
            $("#cart_count").text("("+data+")");
        }
    }); 
}
</script>

<script>

$('.btn-number').click(function(e){
    e.preventDefault();
    
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            
            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            } 
            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            }

        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            }

        }
    } else {
        input.val(0);
    }
});
$('.input-number').focusin(function(){
   $(this).data('oldValue', $(this).val());
});
$('.input-number').change(function() {
    
    minValue =  parseInt($(this).attr('min'));
    maxValue =  parseInt($(this).attr('max'));
    valueCurrent = parseInt($(this).val());
    
    name = $(this).attr('name');
    if(valueCurrent >= minValue) {
        $(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the minimum value was reached');
        $(this).val($(this).data('oldValue'));
    }
    if(valueCurrent <= maxValue) {
        $(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the maximum value was reached');
        $(this).val($(this).data('oldValue'));
    }
    
    
});
$(".input-number").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

function goToCart(){
    window.location.href="/cart";
}
</script>

<style>
  .red{
    color:red;
  }

</style>