<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">

<div class="row">
  
  <!-- ./col -->
  <div class="col-lg-2 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-olive">
      <div class="inner">
          
        <a href="/admin/orders">
        <h3 style="color:#FFFFFF;">{{$total_orders}}
        
        </h3>

        <p style="color:#FFFFFF;">Total Orders</p>
        </a>
      </div>
      <div class="icon">
        <i class="fa fa-bar-chart"></i>
      </div>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-2 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-maroon">
      <div class="inner">
          <a href="/admin/orders?&id=&customer_id=&vendor_id=&delivered_by=&status=8">
            <h3 style="color:#FFFFFF;">{{$completed_orders}}</h3>
    
            <p style="color:#FFFFFF;">Completed Orders</p>
          </a>
      </div>
      <div class="icon">
        <i class="fa fa-bookmark"></i>
      </div>
    </div>
  </div>
  <!-- ./col -->
  
  <div class="col-lg-2 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-navy">
      <div class="inner">
          <a href="/admin/orders">
            <h3 style="color:#FFFFFF;">{{$pending_orders}}</h3>
    
            <p style="color:#FFFFFF;">Pending orders</p>
            </a>
      </div>
      <div class="icon">
        <i class="fa fa-bookmark"></i>
      </div>
    </div>
  </div>
  
  
  <div class="col-lg-2 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-blue">
      
          <div class="inner">
              <a href="/admin/customers">
            <h3 style="color:#FFFFFF;">{{$customers}}</h3>
            <p style="color:#FFFFFF;">Total Customers</p>
            </a>
          </div>
      
      <div class="icon">
        <i class="fa fa-user"></i>
      </div>
    </div>
  </div>
  


  <div class="col-lg-2 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-orange">
      <div class="inner">
          <a href="/admin/delivery_boys">
             <h3 style="color:#FFFFFF;">{{$delivery_boys}}</h3>
    
            <p style="color:#FFFFFF;">Total Delivery Boys</p>
            </a>
      </div>
      <div class="icon">
        <i class="fa fa-motorcycle"></i>
      </div>
    </div>
  </div>
  
  

  <!-- ./col -->


  <div class="col-lg-6">
  <canvas id="orders" width="400"></canvas>
</div>
<div class="col-lg-6">
  <canvas id="customers" width="400"></canvas>
</div>

<div class="col-lg-6">
  <canvas id="customer_viewed_products" width="400"></canvas>
</div>

<div class="col-lg-6">
  <canvas id="customer_favourite_products" width="400"></canvas>
</div>


<div class="col-lg-6" style="margin-top:100px;">
  <canvas id="customer_shared_products" width="400"></canvas>
</div>


</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>

<script>
var ctx_orders = document.getElementById('orders').getContext('2d');
var orders = new Chart(ctx_orders, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: '# of Orders',
            data: [{{ $orders_chart }}],
            backgroundColor: [
                'rgba(54, 162, 235, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(54, 162, 235, 0.2)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(54, 162, 235, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    callback: function(value) {if (value % 1 === 0) {return value;}}
                }
            }]
        }
    }
});

var ctx_customers = document.getElementById('customers').getContext('2d');
var customers = new Chart(ctx_customers, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: '# of Customers',
            data: [{{ $customers_chart }}],
            backgroundColor: [
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    callback: function(value) {if (value % 1 === 0) {return value;}}
                }
            }]
        }
    }
});
</script>

<!-- <script>
  var ctx_cvp = document.getElementById('customer_viewed_products').getContext('2d');
var customers = new Chart(ctx_cvp, {
    type: 'doughnut',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: '# of Customers Viewed Products',
            
            data: [0,0,2,5,7,9,0,123,5,5,],
            backgroundColor: [
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    callback: function(value) {if (value % 1 === 0) {return value;}}
                }
            }]
        }
    }
});
</script> -->

<script>  
  var ctx_cvp = document.getElementById('customer_viewed_products').getContext('2d');
var customers = new Chart(ctx_cvp, {
    type: 'bar',
    data: {
    labels: [
      @foreach($cvp_chart[1] as $item)
      '{{$item}}',
      @endforeach  
    ],
        datasets: [{
            label: '# Самые просматриваемые товары',
            data: [{{$cvp_chart[0]}}],
            backgroundColor: [
              'rgb(255, 99, 132)',
              'rgb(75, 192, 192)',
              'rgb(255, 205, 86)',
              'rgb(201, 203, 207)',
              'rgb(54, 162, 235)',
              'rgba(255, 99, 132, 0.2)',
              'rgba(255, 159, 64, 0.2)',
              'rgba(255, 205, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(54, 162, 235, 0.2)'
            ],
            borderColor: [
              'rgb(255, 99, 132)',
              'rgb(75, 192, 192)',
              'rgb(255, 205, 86)',
              'rgb(201, 203, 207)',
              'rgb(54, 162, 235)',
              'rgba(255, 99, 132, 0.2)',
              'rgba(255, 159, 64, 0.2)',
              'rgba(255, 205, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(54, 162, 235, 0.2)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    callback: function(value) {if (value % 1 === 0) {return value;}}
                }
            }],
             xAxes: [{
                display: false //this will remove all the x-axis grid lines
            }]
        }
    }
});
</script>




<script>  
  var ctx_cfp = document.getElementById('customer_favourite_products').getContext('2d');
  var cfp = new Chart(ctx_cfp, {
    type: 'bar',
    data: {
    labels: [
      @foreach($cfp_chart[1] as $item)
      '{{$item}}',
      @endforeach  
    ],
        datasets: [{
            label: '# Самые понравившиеся товары',
            data: [{{$cfp_chart[0]}}],
            backgroundColor: [
              'rgb(255, 99, 132)',
              'rgb(75, 192, 192)',
              'rgb(255, 205, 86)',
              'rgb(201, 203, 207)',
              'rgb(54, 162, 235)',
              'rgba(255, 99, 132, 0.2)',
              'rgba(255, 159, 64, 0.2)',
              'rgba(255, 205, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(54, 162, 235, 0.2)'
            ],
            borderColor: [
              'rgb(255, 99, 132)',
              'rgb(75, 192, 192)',
              'rgb(255, 205, 86)',
              'rgb(201, 203, 207)',
              'rgb(54, 162, 235)',
              'rgba(255, 99, 132, 0.2)',
              'rgba(255, 159, 64, 0.2)',
              'rgba(255, 205, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(54, 162, 235, 0.2)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    callback: function(value) {if (value % 1 === 0) {return value;}}
                }
            }],
             xAxes: [{
                display: false //this will remove all the x-axis grid lines
            }]
        }
    }
});
</script>


<script>  
  var ctx_cshp = document.getElementById('customer_shared_products').getContext('2d');
  var cshp = new Chart(ctx_cshp, {
    type: 'bar',
    data: {
    labels: [
      @foreach($cshp_chart[1] as $item) '{{ $item}}',@endforeach  
    ],
        datasets: [{
            label: '# Топ поделиться',
            data: [{{$cshp_chart[0]}}],
            backgroundColor: [
              'rgb(255, 99, 132)',
              'rgb(75, 192, 192)',
              'rgb(255, 205, 86)',
              'rgb(201, 203, 207)',
              'rgb(54, 162, 235)',
              'rgba(255, 99, 132, 0.2)',
              'rgba(255, 159, 64, 0.2)',
              'rgba(255, 205, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(54, 162, 235, 0.2)'
            ],
            borderColor: [
              'rgb(255, 99, 132)',
              'rgb(75, 192, 192)',
              'rgb(255, 205, 86)',
              'rgb(201, 203, 207)',
              'rgb(54, 162, 235)',
              'rgba(255, 99, 132, 0.2)',
              'rgba(255, 159, 64, 0.2)',
              'rgba(255, 205, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(54, 162, 235, 0.2)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    callback: function(value) {if (value % 1 === 0) {return value;}}
                }
            }],
             xAxes: [{
                display: false //this will remove all the x-axis grid lines
            }]
        }
    }
});
</script>