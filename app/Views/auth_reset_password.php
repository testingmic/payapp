<?php 
$no_header = true;
$pagetitle = 'Reset Password';
require "headtags.php";
?>
<div class="col-10 col-md-6 col-lg-5 col-xl-3 mx-auto align-self-center text-center py-4">
    <h1 class="mb-4 text-color-theme">Right here you can reset it back</h1>
    <p class="text-muted mb-4">Provide your registered email ID or phone number to reset your password</p>
    <form method="POST" autocomplete="Off" id="authForm" action="<?= $baseURL ?>api/auth/resetpassword" class="was-validated needs-validation" novalidate>
        <div class="form-group form-floating mb-3 is-valid">
            <input name="email" type="email" class="form-control" id="email" placeholder="Email Address">
            <label class="form-control-label" for="email">Email Address</label>
        </div>
        <button type="submit" class="btn btn-lg btn-default w-100 mb-4 shadow">
            Reset Password
        </button>
        <p class="mb-3 text-center">
            <a href="<?= $baseURL ?>auth/login" class="">Signin instead?</a>
        </p>
    </form>
</div>
<?php require "foottags.php"; ?>