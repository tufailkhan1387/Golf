<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="POS - Bootstrap Admin Template">
		<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects">
        <meta name="author" content="Dreamguys - Bootstrap Admin Template">
        <meta name="robots" content="noindex, nofollow">
        <title>Login - Pos admin template</title>
		
		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
		
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
		
        <!-- Fontawesome CSS -->
		<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
		<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
		
		<!-- Main CSS -->
        <link rel="stylesheet" href="assets/css/style.css">
        <!-- Toastr CSS -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

 <!-- Toastr JS -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <style>
            .login-wrapper{
                display: flex;
    justify-content: center;
    align-items: center;
            }
           
        </style>
		
    </head>
    <body class="account-page">

        <div id="global-loader" >
			<div class="whirly-loader"> </div>
		</div>
	
		<!-- Main Wrapper -->
        <div class="main-wrapper">
			<div class="account-content">
				<div class="login-wrapper forgot-pass-wrap ">
                    <div class="login-content">
                        <form method="post" action="{{route('forget_password_post')}}">
                            @csrf
                            <div class="login-userset">
                                <div class="login-logo logo-normal">
                                    <img src="assets/img/timbor-logo.png" alt="">
                                </div>
                               <a href="index.html" class="login-logo logo-white">
                                   <img src="assets/img/timbor-logo.png"  alt="">
                               </a>
                               <div class="login-userheading">
                                   <h3>Forgot password?</h3>
                                   <h4>If you forgot your password, well, then we’ll email you instructions to reset your password.</h4>
                               </div>
                               <div class="form-login">
                                   <label>Email</label>
                                   <div class="form-addons">
                                       <input name="email" type="email" class="form-control">
                                       <img src="assets/img/icons/mail.svg" alt="img">
                                   </div>
                               </div>
                               <div class="form-login">
                                   <button type="submit" class="btn btn-login">Submit</button>
                               </div>
                               <div class="signinform text-center">
                                   <h4>Return to<a href="{{route('login')}}" class="hover-a"> login </a></h4>
                               </div>
                               
                           </div>
                        </form>
                    </div>
                </div>
			</div>
        </div>
		<!-- /Main Wrapper -->
		<div class="customizer-links" id="setdata">
			<ul class="sticky-sidebar">
				<li class="sidebar-icons">
					<a href="#" class="navigation-add" data-bs-toggle="tooltip" data-bs-placement="left"
						data-bs-original-title="Theme">
						<i data-feather="settings" class="feather-five"></i>
					</a>
				</li>
			</ul>
		</div>

		<!-- jQuery -->
        <script src="assets/js/jquery-3.7.1.min.js"></script>

         <!-- Feather Icon JS -->
		<script src="assets/js/feather.min.js"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="assets/js/bootstrap.bundle.min.js"></script>
		
		<!-- Custom JS -->
        <script src="assets/js/theme-script.js"></script>	
		<script src="assets/js/script.js"></script>

	
    </body>
    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
    
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    
        @if (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif
    
        @if (session('info'))
            toastr.info("{{ session('info') }}");
        @endif
    </script>
</html>