@include('templates.header')
@include('templates.product_modal')
     
      <div class="container">
        <!-- HERO SECTION-->
        <section class=" bg-light">
          <div class="container">
            <div class="row px-4 px-lg-5 py-lg-4 align-items-center">
              <div class="col-lg-6">
                <h1 class="h2 text-uppercase mb-0">Profile</h1>
              </div>
              <!-- <div class="col-lg-6 text-lg-right">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb justify-content-lg-end mb-0 px-0">
                    <li class="breadcrumb-item">Welcome</li>
                    <li class="breadcrumb-item active"> {{Auth::user()->first_name}}!</li>
                  </ol>
                </nav>
              </div> -->
            </div>
          </div>
        </section>
        <section class="">
          <div class="container p-0">
            <div class="row">
              <!-- SHOP LISTING-->
              <div class="col-lg-12 order-1 order-lg-2 mb-5 mb-lg-0">
                <div class="row mb-3 align-items-center">
                  <div class="col-lg-6 mb-2 mb-lg-0">
                    <!-- <p class="text-small text-muted mb-0">Showing 1–12 of 53 results</p> -->
                  </div>
                </div>
                <div class="row">
        <!-- <div class="col-xl-4 order-xl-1 mb-5 mb-xl-0">
          
        </div> -->
        <div class="col-xl-12 order-xl-2 mb-1">
          <div class="card card-profile shadow">
            <div class="row justify-content-center">
              <div class="col-lg-3 order-lg-2">
                <div class="card-profile-image">
                  <?php
                  if($profile->profile_picture != '' || $profile->profile_picture !== null){
                   $profile_image = env('APP_URL').'/uploads/'.$profile->profile_picture; 
                 }else{
                   $profile_image = env('APP_URL').'/uploads/images/avatar.png'; 
                 }
                 ?>
                 @csrf
                  <a href="#">
                    <img id="profile_preview" src="{{ $profile_image }}" class="">
                  </a>
                </div>
              </div>
            </div>
            <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
              <form method="POST" id="uploadimage" action="/profile">
              <div class="">
                <a href="javascript:void(0)" onclick="open_picker();" class="btn btn-dark float-right">Change Profile Picture</a>
                 <input style="display: none;" onchange="update_picture(this.value);" type="file" name="profile_picture" id="profile_picture" accept="image/*">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />
                        <input type="hidden" name="id" id="id" value="{{ $profile->id }}" />
                        
              </div>
            </form>
            </div>
            <div class="card-body">
                <hr class="my-4">
                <h4 class="mb-4">User information</h4>
                 <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label>First Name<span class="text-danger">*</span></label>
                        <input id="customer_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ $profile->first_name }}" placeholder="First name" required >
                        
                        @error('first_name')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label>Last Name<span class="text-danger">*</span></label>
                        <input id="customer_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ $profile->last_name }}" placeholder="Last name" required >
                        
                        @error('last_name')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-group">
                        <label>Phone <span class="text-danger">*</span></label>
                        <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ $profile->phone_number }}" placeholder="Phone" required autocomplete="email">
                        
                        @error('phone_number')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-group">
                        <label>Email<span class="text-danger">*</span></label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ $profile->email }}" required autocomplete="email">

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-group">
                        <label>Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" name="password" autocomplete="new-password">

                      </div>
                    </div>
                  </div>
                
                 <div style="margin-bottom:50px!important">
                <button  type="button" onclick="profile_update();" style="width:200px;" class="btn btn-dark float-right">Update</button>
              </div>
                <hr class="my-4">
                <!-- Address -->
                <div class="row">
                  <div class="col">
                <h4 class="mb-4">Contact information</h4>
              </div>
              <div class="col">
                   <button  data-toggle="modal"  data-target="#add_address_model" data-dismiss="modal" style="width:200px;" class="btn btn-dark float-right">Add New Address</button>
                 </div>
                 </div>
                    <!-- <div class="col-md-9">
                    </div>
                    <div class="col-md-3">
                      <a data-toggle="modal" style="color:white;" data-target="#add_address_model" data-dismiss="modal"class="btn btn-secodary mb-2 mr-2">Add Address</a>
                    </div> -->
                  <input type="hidden" name="id" id="id" value="{{ $profile->id }}" />
                  <input type="hidden" name="address_id" id="address_id" />
                  <div class="row">
                    @foreach($addresses as $value)
                      <div class="col-xl-4 order-xl-2 mb-1" id="address_{{ $value->id }}">
                          <article class="post bg-white shadow rounded">
                              <div class="post-content bg-white rounded" style="padding:15px!important">
                                  <a class="post-title">{{ $value->google_address }}</a>
                                  <div style="margin:10px;"></div>
                                  <div class="post-footer border-top">
                                      <ul class="post-meta list-unstyled list-inline mb-0">
                                          <li class="list-inline-item float-right"><i class="fas fa-trash mr-2"></i><a onclick="delete_address({{ $value->id }});" >Delete</a></li>
                                          <li class="list-inline-item"><i class="fa fa-edit mr-2"></i><a onclick="edit_address('{{ $value->id }}', '{{ $value->address }}','{{ $value->unique_id }}');" data-toggle="modal" data-target="#add_address_model"> Edit </a></li>
                                      </ul>
                                  </div>
                              </div>
                          </article>
                      </div>
                    @endforeach
                  </div>
            </div>
          </div>
        </div>
      </div>
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

        <!-- Modal -->
        <div class="modal fade" id="add_address_model" role="dialog">
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
      <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
  </div>
</div>

</div>
      </div>


<!-- Modal -->

@include('templates.footer')
<style>
.active{
  color: #b68b23!important;
}
</style>
 <script>

    $("#uploadimage").on('submit',(function(e) {
    e.preventDefault();
    $.ajax({
    url: "/profile_image", 
    type: "POST",             // Type of request to be send, called as method
    data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
    contentType: false,       // The content type used when sending data to the server.
    cache: false,             // To unable request pages to be cached
    processData:false,        // To send DOMDocument or non processed data file it is set to false
    success: function(data)   // A function to be called if request succeeds
    {
    //alert(data);
    if(data == 1){
          alert('successfully updated');
        }  
    }
    });
    }));

        function edit_address(id,address,latitude,longitude,door_no,customer_address){
          $("#address_id").val(id);
  //$("#customer_address").val(customer_address);
  $("#scrolled_address").val(address);
  $("#search").val(address);
  $("#lat").val(latitude);
  $("#lng").val(longitude);
  $("#door_no").val(door_no);
  myMap(latitude,longitude);

}

function delete_address(id){
  if(confirm("Are you sure to delete this address?")){
    $.ajax({
      url: '/address_delete',
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}", 
        address_id:id
      },
      success: function (data) { 
        if(data == 1){
          alert('successfully deleted');
          $("#address_"+id).hide()
        }
      }
    });
    
  }
}

function profile_update(){
  var customer_name = $("#customer_name").val();
  var id = $("#id").val();
  var email = $("#email").val();
  var phone_number = $("#phone_number").val();
  var password = $("#password").val();
  $.ajax({
    url: '/profile_update',
    type: 'POST',
    data: {
      _token: "{{ csrf_token() }}", 
      customer_id:id ,
      customer_name:customer_name ,
      email:email ,
      phone_number:phone_number ,
      password:password 
    },
    success: function (data) { 
      if(data == 1){
        alert('Profile successfully updated');
        $("#password").val('');
      }else{
        alert('Something went wrong');
        $("#password").val('');
      }
    }
  });
}

function open_picker(){
  $("#profile_picture").click();
}

function update_picture(val){
  var input = document.getElementById("profile_picture");
  var fReader = new FileReader();
  fReader.readAsDataURL(input.files[0]);
  fReader.onloadend = function(event){
    var img = document.getElementById("profile_preview");
    img.src = event.target.result;
    $( "#uploadimage" ).submit();
  }
}


/*function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active"; 
}*/

/*if('{{ @$_GET["tab"] }}' == 'address'){
  document.getElementById("address_tab").click();
}else{
  document.getElementById("defaultOpen").click();
}*/

// Get the element with id="defaultOpen" and click on it


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
            $('#add_address_model').modal('hide');
            window.location = "/profile";
        }
    });
  }else{
      $.ajax({
          url: '/edit_address',
          type: 'POST',
          data: {
              _token: "{{ csrf_token() }}", 
              address_id:address_id ,
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


function myMap(lat = 0, lng = 0) {
  if(lat == 0 && lng == 0){
    var mapProp= {
      center:new google.maps.LatLng(9.034233, 7.590749),
      zoom:16,
    };
    document.getElementById("lat").value = 9.034233;
    document.getElementById("lng").value = 7.590749;
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

function open_model(id){
  $("#current_id").val(id);
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('MAP_KEY') }}&callback=myMap"></script>
