
@include('templates.header')
@include('templates.product_modal')
      <div class="container">
        <!-- HERO SECTION-->
        <section class="py-5 bg-light">
          <div class="container">
            <div class="row px-4 px-lg-5 py-lg-4 align-items-center">
              <div class="col-lg-6">
                <h1 class="h2 text-uppercase mb-0">Checkout</h1>
              </div>
              <div class="col-lg-6 text-lg-right">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb justify-content-lg-end mb-0 px-0">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="cart.html">Cart</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                  </ol>
                </nav>
              </div>
            </div>
          </div>
        </section>
        <section class="py-5">
          <!-- BILLING ADDRESS-->
          <h2 class="h5 text-uppercase mb-4">Billing details</h2>
          <div class="row">
            <div class="col-lg-8">
                <div class="row">
                  <div class="col-lg-6 form-group">
                    <label class="text-small text-uppercase" for="firstName">First name</label>
                    <input class="form-control form-control-lg" id="firstName" type="text" placeholder="Enter your first name" value="{{$data->first_name}}">
                  </div>
                  <div class="col-lg-6 form-group">
                    <label class="text-small text-uppercase" for="lastName">Last name</label>
                    <input class="form-control form-control-lg" id="lastName" type="text" placeholder="Enter your last name" value="{{$data->last_name}}">
                  </div>
                  <div class="col-lg-6 form-group">
                    <label class="text-small text-uppercase" for="email">Email address</label>
                    <input class="form-control form-control-lg" id="email" type="email" placeholder="e.g. Jason@example.com" value="{{$data->email}}">
                  </div>
                  <div class="col-lg-6 form-group">
                    <label class="text-small text-uppercase" for="phone">Phone number</label>
                    <input class="form-control form-control-lg" id="phone" type="tel" placeholder="e.g. +02 245354745" value="{{$data->phone_number}}">
                  </div>
                    
                  <div class="col-lg-8 form-group">
                    <label class="text-small text-uppercase" for="billing_address"></label>Billing Address
                    <input class="form-control form-control-lg" id="billing_address" name="billing_address" type="text" placeholder="House number and street name" value="">
                  </div>
                  <div class="col-lg-4 form-group">
                    <div style="height:30px;"></div>
                    <a style="color:white;" id="pickup_address_btn" class="btn btn-dark" data-toggle="modal"  data-target="#address_model">Select Pickup Address</a>
                    <input type="hidden" name="billing_address_id" id="billing_address_id" value="" />
                  </div>
                  <div class="col-lg-12 form-group">
                    <button class="btn btn-dark" onclick="place_order();" type="submit">Place order</button>
                  </div>
                </div>
            </div>
          @if(count(Session::get('cart', [])) > 0)

            <!-- ORDER SUMMARY-->
            <div class="col-lg-4">
              <div class="card border-0 rounded-0 p-lg-4 bg-light">
                <div class="card-body">
                  <h5 class="text-uppercase mb-4">Your order</h5>
                  <ul class="list-unstyled mb-0">
                    @foreach(Session::get('cart', []) as $value)
                    <li class="d-flex align-items-center justify-content-between"><strong class="small font-weight-bold">{{ $value['product_name'] }}</strong><span class="text-muted small">{{$currency}} {{ $value['total_price'] }}</span></li>
                    <li class="border-bottom my-2"></li>
                    @endforeach
                    <li class="d-flex align-items-center justify-content-between mb-2"><strong class="text-uppercase small font-weight-bold">SubTotal</strong><span>{{$currency}} {{$sub_total}}</span></li>
                    <li class="d-flex align-items-center justify-content-between mb-2"><strong class="text-uppercase small font-weight-bold">Discount</strong><span>{{$currency}} {{$promo_amount}}</span></li>
                    <li class="d-flex align-items-center justify-content-between mb-2"><strong class="text-uppercase small font-weight-bold">Delivery cost</strong><span>{{$currency}} {{$delivery_cost}}</span></li>
                    <li class="border-bottom my-2"></li>
                    <li class="d-flex align-items-center justify-content-between mb-2"><strong class="text-uppercase small font-weight-bold">Total</strong><span>{{$currency}} {{$total}}</span></li>
                  </ul>
                </div>
              </div>
            </div>
            @endif
          </div>
        </section>
        <input type="hidden" name="total" id="total" value="{{ $total }}" />
        <input type="hidden" name="promo_amount" id="promo_amount" value="{{ $promo_amount }}" />
        <input type="hidden" name="sub_total" id="sub_total" value="{{ $sub_total }}" />
        <input type="hidden" name="promo_id" id="promo_id" value="{{ $promo_id }}" />
        <input type="hidden" name="delivery_cost" id="delivery_cost" value="{{ $delivery_cost }}" />
      </div>

<!-- Modal -->
<div class="modal fade" id="address_model" role="dialog" style="overflow: scroll;overflow-y: scroll;overflow-x:hidden"> 
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="btn btn-dark" data-dismiss="modal" data-toggle="modal" data-target="#new_address_model">Add Address</button>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" id="address_list">
            @foreach($addresses as $value)
            <div class="row">
                <div class="col-md-8 ">
                    <!--<p>Door no : {{ $value->door_no }}</p>-->
                    <h6>{{ $value->google_address }}</h6>
                </div>
                <div class="col-md-2">
                    <center><button type="button" class="btn btn-dark" data-dismiss="modal" onclick="choose_address('{{$value->id}}','{{$value->google_address}}');">Select</button></center>
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

<!-- Modal -->

<!-- Modal -->
<div class="modal fade" id="new_address_model" role="dialog" style="overflow: scroll;overflow-y: scroll;overflow-x:hidden">
    <div class="modal-dialog modal-lg">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
              <div class="search-container" style="margin:20px;">
                  <div class="form-group">
                    <input type="text" placeholder="Search.." id="search" name="search" >
                    <input type="hidden" id="lat" >
                    <input type="hidden" id="address_id" >
                    <input type="hidden"id="lng" >
                    <input type="hidden"id="scrolled_address" >
                    <button type="button" class="btn btn-dark"  onclick="get_lat_lng();">Search</button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
          <right>
            <textarea type="text" style="margin:20px;" placeholder="Enter Exact Address" id="customer_address" name="customer_address"></textarea>
        </right>
    </div>
    <div class="col-md-6">
      <p><b>Note:Please select your location in map</b></p>
      <p><b>If not need exact address, please enter none</b></p>
  </div>
                 <!--<div class="col-md-4">
                  <textarea type="text" style="margin:20px;" placeholder="Enter Full Address" id="customer_address" name="customer_address"></textarea> 
              </div>-->

              <div class="col-md-12">
                <div class="parent">
                  <div id="googleMap" style="width:100%;height:400px;">
                  </div>
                  <div class="child">
                      <img src="{{ asset('web/img/pin.png') }}" style="width:120px;height:80px;" alt="img">
                  </div>
              </div>
              
          </div><!--end col-->
      </div><!--end row-->
  </div>
  <div class="modal-footer">
      <button type="button" onclick="address_submit();" class="btn btn-dark" >Submit</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  </div>
</div>

</div>

</div>

@include('templates.footer')
<script type="text/javascript">
  function myMap(lat = 0, lng = 0) {
        if(lat == 0 && lng == 0){
            var mapProp= {
              center:new google.maps.LatLng(13.013397, 77.577337),
              zoom:16,
          };
          document.getElementById("lat").value = 13.013397;
          document.getElementById("lng").value = 77.577337;
      }else{
        var mapProp= {
          center:new google.maps.LatLng(lat, lng),
          zoom:16,
      };
      document.getElementById("lat").value = lat;
      document.getElementById("lng").value = lng;
  }


  var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
  var geocoder = new google.maps.Geocoder();

  google.maps.event.addListener(map, 'center_changed', function () {
      var location = map.getCenter();
      document.getElementById("lat").value = location.lat();
      document.getElementById("lng").value = location.lng();
      geocoder.geocode({
        'latLng': new google.maps.LatLng(location.lat(), location.lng())
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[0]) {
            document.getElementById("scrolled_address").value = results[0].formatted_address;
            document.getElementById("search").value = results[0].formatted_address;
        }
    }
});
  });
}


function get_lat_lng(){
  var geocoder = new google.maps.Geocoder();
  var address = $("#search").val();

  geocoder.geocode( { 'address': address}, function(results, status) {

    if (status == google.maps.GeocoderStatus.OK) {
      var latitude = results[0].geometry.location.lat();
      var longitude = results[0].geometry.location.lng();
      myMap(latitude,longitude);
  } 
}); 
}

function address_submit(){
  var customer_id = '{{ Auth::id() }}';
  var address_id = $("#address_id").val();
  var latitude = $("#lat").val();
  var longitude = $("#lng").val();
  if($("#scrolled_address").val() == ''){
    var address = $("#search").val();
}else{
    var address = $("#scrolled_address").val();
}
if($("#customer_address").val() != ''){
     var customer_address = $("#customer_address").val();
}else{
  alert("Please enter exact address");
  return false;
}
    if(address_id == ''){
      $.ajax({
          url: '/save_address',
          type: 'POST',
          data: {
              _token: "{{ csrf_token() }}", 
              customer_id:customer_id,
              lat:latitude ,
              lng:longitude ,
              google_address:address ,
              customer_address:customer_address ,
          },
          success: function (data) { 
            $('#new_address_model').modal('hide');
            $('#lat').val('');
            $('#lng').val('');
            $('#scrolled_address').val('');
            $('#search').val('');
            $('#address_id').val('');
            var obj = JSON.parse(data);
            $("#address_list").append('<div class="row"><div class="col-md-8 "><h6>'+obj.google_address+'</h6></div><div class="col-md-2"><center><button type="button" class="btn btn-default" data-dismiss="modal" onclick="choose_address('+"'"+obj.id+"'"+','+"'"+obj.google_address+"'"+');">Select</button></center></div></div><hr>');
            $('#address_model').modal('show');
            //window.location = "/profile/address";
        }
    });
  }else{
      $.ajax({
          url: '/edit_address',
          type: 'POST',
          data: {
              _token: "{{ csrf_token() }}", 
              customer_id:customer_id ,
              lat:latitude ,
              lng:longitude ,
              google_address:address ,
              customer_address:customer_address ,
          },
          success: function (data) { 
            $('#add_address_model').modal('hide');
            window.location = "/profile";
        }
    });
  }
}

function choose_address(id,address){
    $("#billing_address_id").val(id);
    $("#billing_address").val(address);
    }

  function place_order(){
       var customer_id = '{{ Auth::id() }}';
       var billing_address =  $("#billing_address_id").val();
    if(billing_address == ""){
        alert('Please choose address');
        return false;
    }else{
        $.ajax({
            url: '/place_order',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}", 
                customer_id:customer_id ,
                customer_address_id:billing_address,
                total:$("#total").val(),
                discount:$("#promo_amount").val(),
                sub_total:$("#sub_total").val(),
                delivery_charge:$("#delivery_cost").val(),
                promo_id:$("#promo_id").val(),
                vendor_id:1,
            },
            success: function (data) { 
                if(data == 1){
                    window.location = "/thankyou";
                }
            }
        });
    }
}

  </script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('MAP_KEY') }}&callback=myMap"></script>
