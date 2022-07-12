                       
@include('templates.header')
<div class="container">
    <div class="row py-5 mt-4 align-items-center">
        <!-- For Demo Purpose -->
        <div class="col-md-5 pr-lg-5 mb-5 mb-md-0">
            <img src="{{ asset('web/img/category-1.jpg') }}" alt="" class="img-fluid mb-3 d-none d-md-block">
            <h1>LogIn to your Account</h1>
            <p class="font-italic text-muted mb-0">Enjoy the unlimited services from ULike.</p>
        </div>
 
        <!-- Registeration Form -->
        <div class="col-md-7 col-lg-6 ml-auto">
            @if(@$message)
                <div class="alert alert-success" role="alert">
                    {{ $message }}
                </div>
            @endif
            <form class="login-form" method="post" action="/login" >
                @csrf
                <div class="row">
                    <!-- Phone Number -->
                    <div class="input-group col-lg-12 mb-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white px-4 border-md border-right-0">
                                <i class="fa fa-phone-square text-muted"></i>
                            </span>
                        </div>
                        <input id="phone_number" type="tel" name="phone_number" placeholder="Phone Number" class="form-control bg-white border-md border-left-0 pl-3 @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" required autocomplete="phone_number">
                        @error('phone_number')
                            <span class="invalid-feedback " role="alert">
                                <strong>{{$message}}</strong>
                            </span>
                        @enderror
                    </div>
                    <!-- Password -->
                    <div class="input-group col-lg-12 mb-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white px-4 border-md border-right-0">
                                <i class="fa fa-lock text-muted"></i>
                            </span>
                        </div>
                        <input id="password" type="password" name="password" placeholder="Password" class="form-control bg-white border-left-0 border-md  @error('password') is-invalid @enderror" required autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{$message}}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group col-lg-12 mx-auto mb-0">
                       <button type="submit" class="btn btn-primary btn-block py-2">LogIn</button>
                    </div>

                </div>
                    
                    <!-- Divider Text -->
                    <div class="form-group col-lg-12 mx-auto d-flex align-items-center my-4">
                        <div class="border-bottom w-100 ml-5"></div>
                        <span class="px-2 small text-muted font-weight-bold text-muted">OR</span>
                        <div class="border-bottom w-100 mr-5"></div>
                    </div>


                    <!-- Already Registered -->
                    <div class="text-center w-100">
                        <p class="text-muted font-weight-bold">New user? <a href="/register" class="text-primary ml-2">SignUp</a></p>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

@include('templates.footer')
<style>
	/*
*
* ==========================================
* CUSTOM UTIL CLASSES
* ==========================================
*
*/

.border-md {
    border-width: 2px;
}

.btn-facebook {
    background: #405D9D;
    border: none;
}

.btn-facebook:hover, .btn-facebook:focus {
    background: #314879;
}

.btn-twitter {
    background: #42AEEC;
    border: none;
}

.btn-twitter:hover, .btn-twitter:focus {
    background: #1799e4;
}



/*
*
* ==========================================
* FOR DEMO PURPOSES
* ==========================================
*
*/

body {
    min-height: 100vh;
}

.form-control:not(select) {
    padding: 1.5rem 0.5rem;
}

select.form-control {
    height: 52px;
    padding-left: 0.5rem;
}

.form-control::placeholder {
    color: #ccc;
    font-weight: bold;
    font-size: 0.9rem;
}
.form-control:focus {
    box-shadow: none;
}

	</style>