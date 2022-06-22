<?php
// set the header file
require "headtags.php";
?>
<div class="row h-100">
    <div class="col text-center align-self-center">
        <a href="<?= $baseURL ?>dashboard" class="logo-splash">
            <div class="loader-cube-wrap loader-cube-animate mx-auto">
                <img src="<?= $publicURL ?>assets/img/logo.png" alt="Logo">
            </div>
            <h2 class="text-white mt-4"><?= $AppName ?></h2>
        </a>
    </div>
</div>
<?php require "foottags.php"; ?>