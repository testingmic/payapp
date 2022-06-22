<?php 
$no_header = true;
$pagetitle = 'Lockscreen';
// set the header
require "headtags.php";

// confirm if the user is loged in
$sessObject = $AuthObj->is_loggedin();

// set the user data
$userData = $sessObject->_userInfo;

// if the locked get parameter is parsed
if(empty($userData)) {
    // log the user out if locked was parsed
    header('location: ' . $baseURL . 'login');
    exit;
}
?>
<div class="col-lg-6">
    <div class="p-lg-5 p-4 pb-1 auth-one-bg h-100">
        <div class="bg-overlay"></div>
        <div class="position-relative h-100 d-flex flex-column">
            <div class="mb-4">
                <a href="<?= $baseURL ?>" class="d-block">
                    <img src="<?= $publicURL ?>assets/images/logo-light.png" alt="" height="18">
                </a>
            </div>
            <div class="mt-auto">
                <div class="mb-3">
                    <i class="ri-double-quotes-l display-4 text-success"></i>
                </div>

                <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active"
                            aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner text-center text-white pb-5">
                        <div class="carousel-item active">
                            <p class="fs-15 fst-italic">" Great! Clean code, clean design, easy for customization. Thanks very much! "</p>
                        </div>
                        <div class="carousel-item">
                            <p class="fs-15 fst-italic">" The theme is really great with an amazing customer support."</p>
                        </div>
                        <div class="carousel-item">
                            <p class="fs-15 fst-italic">" Great! Clean code, clean design, easy for customization. Thanks very much! "</p>
                        </div>
                    </div>
                </div>
                <!-- end carousel -->
            </div>
        </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="p-lg-4 p-1">
        <h5 class="text-primary">Lock Screen</h5>
        <p class="text-muted">Enter your password to unlock the screen!</p>
        <div class="user-thumb text-center">
            <img src="<?= $baseURL ?><?= !empty($userData['image']) ? $userData['image'] : "assets/images/avatar.png" ?>" class="rounded-circle img-thumbnail avatar-lg" alt="thumbnail">
            <h5 class="font-size-15 mt-3"><?= $userData['firstname'] ?> <?= $userData['lastname'] ?></h5>
        </div>
        <div class="p-2">
            <form id="authForm" action="<?= $baseURL ?>api/auth/unlock" method="POST" novalidate class="needs-validation">
                <div class="mb-2">
                    <label class="form-label">Password</label>
                    <input required name="password" type="password" class="form-control" id="password" placeholder="Enter your password">
                </div>
                
                <div class="text-center mt-2">
                    <button class="btn btn-success w-100" type="submit">Unlock</button>
                </div>
            </form><!-- end form -->
        </div>

        <div class="mt-5 text-center">
            <p class="mb-0">Not you? return <a href="<?= $baseURL ?>auth/login?locked" class="fw-semibold text-primary text-decoration-underline"> Signin </a> </p>
        </div>
    </div>
</div>
<?php require "foottags.php"; ?>