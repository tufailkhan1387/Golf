<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="POS - Bootstrap Admin Template">
		<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects">
        <meta name="author" content="Dreamguys - Bootstrap Admin Template">
        <meta name="robots" content="noindex, nofollow">
        <title>Verify OTP - Pos Admin Template</title>
		
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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        
        <style>
            .login-wrapper {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }
        </style>
    </head>
    <body class="account-page">

        <!-- Main Wrapper -->
        <div class="main-wrapper">
			<div class="account-content">
				<div class="login-wrapper forgot-pass-wrap">
                    <div class="login-content">
                        <form method="post" action="{{ route('verify_otp_post') }}">
                            @csrf
                            <div class="login-userset">
                                <div class="login-logo logo-normal">
                                    <img src="assets/img/timbor-logo.png" alt="">
                                </div>
                               <div class="login-userheading">
                                   <h3>Enter OTP</h3>
                                   <h4>Please enter the 4-digit OTP sent to your email.</h4>
                               </div>

                               <!-- Email (hidden) -->
                               <input type="hidden" name="email" value="{{ $email }}">

                               <!-- OTP Input -->
                               <div class="form-login">
                                   <label>OTP</label>
                                   <div class="form-addons">
                                       <input name="otp" type="text" class="form-control" placeholder="Enter 4-digit OTP" maxlength="4" required>
                                       <img src="assets/img/icons/key.svg" alt="OTP">
                                   </div>
                               </div>

                               <!-- Submit Button -->
                               <div class="form-login">
                                   <button type="submit" class="btn btn-login">Verify OTP</button>
                               </div>

                               <!-- Go Back to Login -->
                               <div class="signinform text-center">
                                   <h4>Return to <a href="{{ route('login') }}" class="hover-a">login</a></h4>
                               </div>
                           </div>
                        </form>
                    </div>
                </div>
			</div>
        </div>
        <!-- /Main Wrapper -->

		<!-- jQuery -->
        <script src="assets/js/jquery-3.7.1.min.js"></script>
        <!-- Bootstrap Core JS -->
        <script src="assets/js/bootstrap.bundle.min.js"></script>
        <!-- Custom JS -->
        <script src="assets/js/script.js"></script>

        <!-- Toastr Notifications -->
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
    </body>
</html>
