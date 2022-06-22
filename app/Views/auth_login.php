<?php
$no_header = true;
$pagetitle = 'Login';
// set the header
require "headtags.php";

// confirm if the user is loged in
$sessObject = $AuthObj->is_loggedin();

// if the locked get parameter is parsed
if (isset($_GET['locked']) && !empty($sessObject->_userEmail)) {
    // log the user out if locked was parsed
    $AuthObj->logout(['ci_session' => $sessObject]);
}
?>
<div class="col-10 col-md-6 col-lg-5 col-xl-3 mx-auto align-self-center text-center py-4">
    <h1 class="mb-4 text-color-theme">Sign in</h1>
    <form method="POST" id="authForm" autocomplete="Off" action="<?= $baseURL ?>api/auth/_login" class="was-validated needs-validation" novalidate>
        <div class="form-group form-floating mb-3 is-valid">
            <input name="username" type="text" class="form-control" id="email" placeholder="Username">
            <label class="form-control-label" for="email">Username</label>
        </div>

        <div class="form-group form-floating is-invalid mb-3">
            <input name="password" type="password" class="form-control " id="password" placeholder="Password">
            <label class="form-control-label" for="password">Password</label>
            <button type="button" class="text-danger tooltip-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="Enter valid Password" id="passworderror">
                <i class="bi bi-info-circle"></i>
            </button>
        </div>
        <p class="mb-3 text-center">
            <a href="<?= $baseURL ?>auth/reset" class="">Forgot your password?</a>
        </p>
        <button type="submit" class="btn btn-lg btn-default w-100 mb-4 shadow">
            Sign in
        </button>
    </form>
</div>
<div hidden class="col-12 text-center mt-auto">
    <div class="row justify-content-center footer-info">
        <div class="col-auto">
            <p class="text-muted">Or you can continue with </p>
        </div>
        <div class="col-auto ps-0">
            <a href="#" class="p-1"><i class="bi bi-twitter"></i></a>
            <a href="#" class="p-1"><i class="bi bi-google"></i></a>
            <a href="#" class="p-1"><i class="bi bi-facebook"></i></a>
        </div>
    </div>
</div>
<?php require "foottags.php"; ?>