<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="POS - Bootstrap Admin Template">
		<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects">
        <meta name="author" content="Dreamguys - Bootstrap Admin Template">
        <meta name="robots" content="noindex, nofollow">
        <title>Reset Password - Pos Admin Template</title>
		
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
                        <form method="post" action="{{ route('reset_password_post') }}">
                            @csrf
                            <div class="login-userset">
                                <div class="login-logo logo-normal">
                                    <img src="assets/img/timbor-logo.png" alt="">
                                </div>
                                <div class="login-userheading">
                                    <h3>Reset Password</h3>
                                    <h4>Enter your new password below.</h4>
                                </div>

                                <!-- Hidden Email -->
                                <input type="hidden" name="email" value="{{ $email }}">

                                <!-- New Password Field -->
                                <div class="form-login">
                                    <label>New Password</label>
                                    <div class="form-addons">
                                        <input name="password" type="password" class="form-control" placeholder="Enter new password" required>
                                        
                                    </div>
                                </div>

                                <!-- Confirm Password Field -->
                                <div class="form-login">
                                    <label>Confirm Password</label>
                                    <div class="form-addons">
                                        <input name="password_confirmation" type="password" class="form-control" placeholder="Confirm new password" required>
                                      
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="form-login">
                                    <button type="submit" class="btn btn-login">Reset Password</button>
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
        </script>
    </body>
</html>
