@include('templates.header')
@include('templates.product_modal')
      <div class="container">
        <!-- HERO SECTION-->
        <section class="py-5 bg-light">
          <div class="container">
            <div class="row px-4 px-lg-5 py-lg-4 align-items-center">
              <div class="col-lg-6">
                <h1 class="h2 text-uppercase mb-0">Cart</h1>
              </div>
              <div class="col-lg-6 text-lg-right">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb justify-content-lg-end mb-0 px-0">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cart</li>
                  </ol>
                </nav>
              </div>
            </div>
          </div>
        </section>
@if(count(Session::get('cart', [])) > 0)
        <section class="py-5">
          <h2 class="h5 text-uppercase mb-4">Shopping cart</h2>
          <div class="row">
            <div class="col-lg-8 mb-4 mb-lg-0">
              <!-- CART TABLE-->
              <div class="table-responsive mb-4">
                <table class="table">
                  <thead class="bg-light">
                    <tr>
                      <th class="border-0" scope="col"> <strong class="text-small text-uppercase">Product</strong></th>
                      <th class="border-0" scope="col"> <strong class="text-small text-uppercase">Price</strong></th>
                      <th class="border-0" scope="col"> <strong class="text-small text-uppercase">Quantity</strong></th>
                      <th class="border-0" scope="col"> <strong class="text-small text-uppercase">Total</strong></th>
                      <th class="border-0" scope="col"> </th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach(Session::get('cart', []) as $value)
                    <tr>
                      <th class="pl-0 border-light" scope="row">
                        <div class="media align-items-center"><a class="reset-anchor d-block animsition-link" href="detail.html"><img src="{{ env('IMG_URL').$value['image'] }}" alt="..." width="70"/></a>
                          <div class="media-body ml-3"><strong class="h6"><a class="reset-anchor animsition-link" href="detail.html">{{ $value['product_name'] }} ( {{ $value['category_name'] }} )</a></strong></div>
                        </div>
                      </th>
                      <td class="align-middle border-light">
                        <p class="mb-0 small">{{ $currency }} {{ $value['price'] }}</p>
                      </td>
                      <td class="align-middle border-light">
                        <div class="border d-flex align-items-center justify-content-between px-3"><span class="small text-uppercase text-gray headings-font-family" id="quantity_{{ $value['product_id'] }}">{{ $value['qty'] }}</span>
                          <div class="quantity">
                        <button type="button" class="btn p-0  btn-number" data-type="minus" data-field="quant[{{ $value['product_id'] }}]"><i class="fas fa-minus-square"></i>
                        </button>
                      <input class="form-control border-0 shadow-0 p-0" type="text" name="quant[{{ $value['product_id'] }}]" id="quant_{{ $value['product_id'] }}" value="{{ $value['qty'] }}" min="0" max="100" onchange="add_item_to_cart({{$value['category_id'] }},{{$value['product_id'] }},{{$value['price'] }} );">
                      <button type="button "  class="btn p-0  btn-number" data-type="plus" data-field="quant[{{ $value['product_id'] }}]"><i class="fas fa-plus-square"></i>
                     </button>
                    </div>
                        </div>
                      </td>
                      <td class="align-middle border-light">
                        <p class="mb-0 small" id="total_item_price_{{ $value['product_id'] }}">{{ $currency }} {{ $value['total_price'] }}</p>
                      </td>
                      <td class="align-middle border-light"><a onclick="remove_from_cart({{ $value['product_id'] }},{{ $value['category_id'] }})" class="reset-anchor" href="#"><i class="fas fa-trash-alt small text-muted"></i></a></td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <!-- CART NAV-->
              <div class="bg-light px-4 py-3">
                <div class="row align-items-center text-center">
                  <div class="col-md-6 mb-3 mb-md-0 text-md-left"><a class="btn btn-link p-0 text-dark btn-sm" href="/shop"><i class="fas fa-long-arrow-alt-left mr-2"> </i>Continue shopping</a></div>
                  <div class="col-md-6 text-md-right"><a class="btn btn-outline-dark btn-sm" href="/checkout">Procceed to checkout<i class="fas fa-long-arrow-alt-right ml-2"></i></a></div>
                </div>
              </div>
            </div>
            <!-- ORDER TOTAL-->
            <div class="col-lg-4">
              <div class="card border-0 rounded-0 p-lg-4 bg-light">
                <div class="card-body">
                  <h5 class="text-uppercase mb-4">Cart total</h5>
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex align-items-center justify-content-between"><strong class="text-uppercase small font-weight-bold">Subtotal</strong><span class="text-muted small" id="sub_total_text">{{ $currency }} {{ $sub_total }}</span></li>
                    <li class="d-flex align-items-center justify-content-between mt-3"><strong class="text-uppercase small font-weight-bold">Delivery Cost</strong><span class="text-muted small" id="delivery_cost_text">{{ $currency }} {{ $delivery_cost }}</span></li>
                    <li class="d-flex align-items-center justify-content-between mt-3"><strong class="text-uppercase small font-weight-bold">Discount</strong><span class="text-muted small" id="discount_text">{{ $currency }} {{ $promo_amount }}</span></li>
                    <li class="border-bottom my-2"></li>
                    <li class="d-flex align-items-center justify-content-between mb-4"><strong class="text-uppercase small font-weight-bold">Total</strong><span id="total_text">{{ $currency }} {{ $total }}</span></li>
                    <li>
                      <form action="#">
                        <div class="form-group mb-0">
                          <input class="form-control" name="promo_code" id="promo_code" type="text" value="{{ $promo }}" placeholder="Select coupon">
                          <p><center><a id="remove_promo_text" @if($promo != '') style="color:red;" @else style="color:red;display:none;" @endif  onclick="remove_promo();"><i class="fa fa-minus-circle"></i> Remove promo code</a></center></p>
                          <input type="button" id="apply_promo" onClick="apply_promo_get()" class="btn btn-dark btn-sm btn-block" value="Apply Code">
                          <p><center><a data-toggle="modal" data-target="#coupon_model">View coupon codes</a></center></p>
                          <!-- <button class="btn btn-dark btn-sm btn-block" onclick="apply_promo();"  type="submit"> <i class="fas fa-gift mr-2"></i>Apply coupon</button> -->
                        </div>
                      </form>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </section>
        <input type="hidden" name="total" id="total" value="{{ $total }}" />
        <input type="hidden" name="promo_amount" id="promo_amount" value="{{ $promo_amount }}" />
        <input type="hidden" name="sub_total" id="sub_total" value="{{ $sub_total }}" />
        <input type="hidden" name="promo_id" id="promo_id" value="{{ $promo_id }}" />
        <input type="hidden" name="delivery_cost" id="delivery_cost" value="{{ $delivery_cost }}" />
        @else
        <!-- CART PRODUCT START -->
        <section class="py-5">
          <center>
            <img src="{{ asset('web/img/cart_empty.png') }}" alt="..." width="70"/></a>
              <h4 class="title ele-title text-uppercase mb-4">Your cart is empty</h4>
          </center>
        </section>
        @endif

      </div>

<!-- Modal -->
<div class="modal fade" id="coupon_model" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        @foreach($promo_codes as $value)
        <div class="row">
            <div class="col-md-4">
                <center><p style="color:green;margin-top:2px;border:1px solid green;" >{{ $value->promo_code }}</p></center>
            </div>
            <div class="col-md-4 "></div>
            <div class="col-md-4 ">
                <center><button type="button" class="btn btn-default" data-dismiss="modal" onclick="choose_promo('{{$value->promo_code}}');">SELECT</button></center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h6 style="color:black;" >{{ $value->promo_name }}</h6>
            </div>
            <div class="col-md-12 ">
                <p style="font-size: 14px;">{{ $value->description }}</p>
            </div>
        </div>
        <hr>
        @endforeach
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  </div>
</div>

</div>
</div>

@include('templates.footer')
<script type="text/javascript">

    function apply_promo_get(){
        var promo_code = $("#promo_code").val();
        $.ajax({
            url: '/apply_promo',
            type: 'POST',
            data: {_token: "{{ csrf_token() }}", promo_code:promo_code },
            success: function (data) { 
                var obj = JSON.parse(data);
                if(obj.error == 0){
                    $("#sub_total_text").text(obj.currency+" "+obj.sub_total);
                    $("#total_text").text(obj.currency+" "+obj.total);
                    $("#discount_text").text(obj.currency+" "+obj.promo_amount);
                    $("#delivery_cost_text").text(obj.currency+" "+obj.delivery_cost);
                    $("#total").val(obj.total);
                    $("#promo_amount").val(obj.promo_amount);
                    $("#sub_total").val(obj.sub_total);
                    $("#promo_id").val(obj.promo_id);
                    document.getElementById('remove_promo_text').style.display='block';
                }else{
                    alert('Sorry something went wrong !')
                }
                //alert(JSON.stringify(data));
                //location.reload();
            }
        }); 
    }


    function remove_promo(){
        var promo_code = $("#promo_code").val();
        $.ajax({
            url: '/remove_promo',
            type: 'POST',
            data: {_token: "{{ csrf_token() }}", promo_code:promo_code },
            success: function (data) { 
               var obj = JSON.parse(data);
               if(obj.error == 0){
                $("#sub_total_text").text(obj.currency+obj.sub_total);
                $("#total_text").text(obj.currency+obj.total);
                $("#discount_text").text(obj.currency+obj.promo_amount);
                $("#delivery_cost_text").text(obj.currency+obj.delivery_cost);
                $("#total").val(obj.total);
                $("#promo_amount").val(obj.promo_amount);
                $("#sub_total").val(obj.sub_total);
                $("#promo_id").val(obj.promo_id);
                $("#promo_code").val('');
                document.getElementById('remove_promo_text').style.display='none';
            }else{
                alert('Sorry something went wrong !')
            }
                //location.reload();
            }
        }); 
    }
 
  function remove_from_cart(product_id,category_id){
  $.ajax({
    url: '/remove_from_cart',
    type: 'POST',
    data: {_token: "{{ csrf_token() }}",product_id:product_id, category_id:category_id},
    success: function (data) { 
        alert("Item removed from cart");
        window.location.href = "/cart";
    }
  }); 
}


function add_item_to_cart(category_id,product_id,price){
    var qty = $("#quant_"+product_id).val();
    $.ajax({
        url: '/add_item_to_cart',
        type: 'POST',
        data: {_token: "{{ csrf_token() }}", qty:qty, product_id:product_id, category_id:category_id, price:price},
        success: function (data) { 
           var obj = JSON.parse(data);
            $("#total_item_price_"+product_id).text(obj.currency+" "+obj.total_item_price);
            $("#quantity_"+product_id).text(obj.qty);
            $("#sub_total_text").text(obj.currency+" "+obj.sub_total);
            $("#total_text").text(obj.currency+" "+obj.total);
            $("#discount_text").text(obj.currency+"0");

        }
    });
}


function choose_promo(promo_name){
    $("#promo_code").val(promo_name);
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
