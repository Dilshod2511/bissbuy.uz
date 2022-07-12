
@include('templates.header')
@include('templates.product_modal')
      <div class="container">
        <!-- HERO SECTION-->
        <section class="py-5 bg-light">
          <div class="container">
            <div class="row px-4 px-lg-5 py-lg-4 align-items-center">
              <div class="col-lg-12">
                <h1 class="h2 text-center  text-uppercase mb-0">Thank You</h1>
                <h5 class="h5 mb-4 mt-3 text-center">Order placed suucessfully. Keep shoping with BISSBUY.</h5>
              </div>
              <div class="col-lg-12 text-center">
              <button type="button" class="btn  btn-dark" onclick="gotToShop()">Back to store</button>
            </div>
            </div>
          </div>
        </section>
      </div>

@include('templates.footer')
<script type="text/javascript">
  function gotToShop(){
    window.location.href = "/shop";

}
</script>