<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>BISSBUY | Shoping</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="{{ asset('web/vendor/bootstrap/css/bootstrap.min.css', true) }}">
    <!-- Lightbox-->
    <link rel="stylesheet" href="{{ asset('web/vendor/lightbox2/css/lightbox.min.css', true) }}">
    <!-- Range slider-->
    <link rel="stylesheet" href="{{ asset('web/vendor/nouislider/nouislider.min.css', true) }}">
    <!-- Bootstrap select-->
    <link rel="stylesheet" href="{{ asset('web/vendor/bootstrap-select/css/bootstrap-select.min.css', true) }}">
    <!-- Owl Carousel-->
    <link rel="stylesheet" href="{{ asset('web/vendor/owl.carousel2/assets/owl.carousel.min.css', true) }}">
    <link rel="stylesheet" href="{{ asset('web/vendor/owl.carousel2/assets/owl.theme.default.css', true) }}">
    <!-- Google fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Libre+Franklin:wght@300;400;700&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Martel+Sans:wght@300;400;800&amp;display=swap">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="{{ asset('web/css/style.default.css', true) }}" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="{{ asset('web/css/custom.css', true) }}">
    <!-- Favicon-->
    <link rel="shortcut icon" href="{{ asset('web/img/favicon.png', true) }}">
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
  </head>
  <body>
    <div class="page-holder">
      <!-- navbar-->
      <header class="header bg-white">
        <div class="container px-0 px-lg-3">
          <nav class="navbar navbar-expand-lg navbar-light py-3 px-lg-0"><a class="navbar-brand" href="/home"><span class="font-weight-bold text-uppercase text-dark">BISSBUY</span></a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                  <!-- Link--><a class="nav-link active" href="/home">Home</a>
                </li>
                <li class="nav-item">
                  <!-- Link--><a class="nav-link" href="/shop">Shop</a>
                </li>
              </ul>
              <ul class="navbar-nav ml-auto">
              @auth
                <li class="nav-item"><a class="nav-link" href="/cart"> <i class="fas fa-dolly-flatbed mr-1 text-gray"></i>Cart<small class="text-gray" id="cart_count">({{ count(Session::get('cart', [])) }})</small></a></li>
                <li class="nav-item"><a class="nav-link" href="/cart"> <i class="fas fa-heart mr-1 text-danger"></i><small class="text-gray"> (1)</small></a></li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" id="pagesDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-alt mr-1 text-gray"></i>Welcome {{Auth::user()->first_name}}!</a>
                  <div class="dropdown-menu mt-3" aria-labelledby="pagesDropdown">
                    <a class="dropdown-item border-0 transition-link" href="/profile">Profile</a>
                    <a class="dropdown-item border-0 transition-link" href="/order">My Orders</a>
                    <a class="dropdown-item border-0 transition-link" href="/logout">Logout</a>
                  </div>
                </li>
              @endauth
              @guest
                <li class="nav-item"><a class="nav-link" href="/register"> <i class="fas fa-user-alt mr-1 text-gray"></i>SignUp</a></li>
                @endguest

              </ul>
            </div>
          </nav>
        </div>
      </header>
