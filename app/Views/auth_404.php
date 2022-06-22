<?php 
$no_header = true;
$authparticles = true;
// set the header
require "headtags.php";
?>
<div class="row h-100 ">
    <div class="col-12 col-md-6 col-lg-5 col-xl-3 mx-auto py-4 text-center align-self-center">
        <figure class="mw-100 text-center mb-3">
            <img src="<?= $publicURL ?>assets/img/404.png" alt="" class="mw-100">
        </figure>
        <h1 class="mb-0 text-color-theme">Oops!...</h1>
        <p class="text-muted mb-4">The page you are looking for is not found or removed.</p>
        <a href="<?= $baseURL ?>" class="btn btn-default btn-lg">Back to Home</a>
    </div>
</div>
<?php require "foottags.php"; ?>