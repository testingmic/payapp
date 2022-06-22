<?php
// global variables
global $RootURI;

// Create a new object of the Auth Class
$AuthObj = new \App\Controllers\Auth();

// get the route information
$URI = uri_string();
$SPLIT = explode("/", $URI);
$Route = $SPLIT[0];
$RPage = $SPLIT[1] ?? null;
$URI = "{$Route}/{$RPage}";

$isLoggedIn = false;

// global variables
global $baseURL, $publicURL, $permitObj, $userData;

// set the user data to null
$userData = null;

// if the route is register or success
if (!in_array($Route, ['register', 'success']) && !isset($no_validation_check)) {
    // check the user login status
    $sessObject = $AuthObj->login_check();

    // // if the use is logged in
    $isLoggedIn = true;

    // // set the user data
    $userData = $sessObject->_userInfo;
}

// load the options
$baseURL = base_url() . "/";
$publicURL = $baseURL . "public/";
$AppName = 'PayApp';
$Developer = 'Emmallex Technologies';

// set a session object
$sessionObject = session();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="generator" content="">
    <title><?= $pagetitle ?? 'Dashboard'; ?> | <?= $AppName ?></title>

    <meta name="apple-mobile-web-app-capable" content="yes">

    <link rel="manifest" href="<?= $baseURL; ?>manifest.json" />

    <link rel="apple-touch-icon" href="<?= $publicURL; ?>assets/img/favicon180.png" sizes="180x180">
    <link rel="icon" href="<?= $publicURL; ?>assets/img/favicon32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="<?= $publicURL; ?>assets/img/favicon16.png" sizes="16x16" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $publicURL; ?>assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= $publicURL; ?>assets/vendor/swiperjs-6.6.2/swiper-bundle.min.css">
    <link href="<?= $publicURL; ?>assets/css/style.css" rel="stylesheet">
    <link href="<?= $publicURL; ?>assets/vendor/select2/select2.css" rel="stylesheet">
    <link href="<?= $publicURL; ?>assets/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
    <link href="<?= $publicURL; ?>assets/css/custom.css" rel="stylesheet">
</head>

<body class="body-scroll <?= $Route === 'splash' ? 'd-flex flex-column h-100' : null ?>" data-page="<?= empty($Route) ? 'dashboard' : $Route ?>">

    <div class="container-fluid loader-wrap">
        <div class="row h-100">
            <div class="col-10 col-md-6 col-lg-5 col-xl-3 mx-auto text-center align-self-center">
                <div class="loader-cube-wrap loader-cube-animate mx-auto">
                    <a href="<?= $baseURL ?>">
                        <img src="<?= $publicURL; ?>assets/img/logo.png" alt="Logo">
                    </a>
                </div>
                <p class="mt-4">It's time for track budget<br><strong>Please wait...</strong></p>
            </div>
        </div>
    </div>

    <?php if(!isset($noheader) && $isLoggedIn) { ?>
        <div class="sidebar-wrap  sidebar-pushcontent">
            <!-- Add overlay or fullmenu instead overlay -->
            <div class="closemenu text-muted">Close Menu</div>
            <div class="sidebar dark-bg">
                <!-- user information -->
                <div class="row my-3">
                    <div class="col-12 ">
                        <div class="card shadow-sm bg-opac text-white border-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-auto">
                                        <figure class="avatar avatar-44 rounded-15">
                                            <img src="<?= $publicURL ?><?= $userData['image'] ?? null ?>" alt="">
                                        </figure>
                                    </div>
                                    <div class="col px-0 align-self-center">
                                        <p class="mb-1"><?= $userData['firstname'] ?? null ?></p>
                                        <p class="text-muted size-12"><?= $userData['contact'] ?? null ?></p>
                                    </div>
                                    <div class="col-auto">
                                        <button title="Logout" onclick="return _logout();" class="btn btn-44 btn-light"><i class="bi bi-box-arrow-right"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="card bg-opac text-white border-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <h1 class="display-4"><?= $userData['balance'] ?? null ?></h1>
                                        </div>
                                        <div class="col-auto">
                                            <p class="text-muted">Wallet Balance</p>
                                        </div>
                                        <div class="col text-end">
                                            <p class="text-muted"><a href="<?= $baseURL ?>members/view/<?= $userData['user_id'] ?>" >Details</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- user emnu navigation -->
                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link <?= empty($Route) || $Route == 'dashboard' ? 'active' : null ?>" aria-current="page" href="<?= $baseURL ?>">
                                    <div class="avatar avatar-40 rounded icon"><i class="bi bi-house-door"></i></div>
                                    <div class="col">Dashboard</div>
                                    <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $Route == 'account' ? 'active' : null ?>" aria-current="page" href="<?= $baseURL ?>members/view/<?= $userData['user_id'] ?>">
                                    <div class="avatar avatar-40 rounded icon"><i class="bi bi-person"></i></div>
                                    <div class="col">My Account</div>
                                    <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?= $Route == 'loans' ? 'active' : null ?>" href="<?= $baseURL ?>loans" tabindex="-1">
                                    <div class="avatar avatar-40 rounded icon"><i class="bi bi-chat-text"></i></div>
                                    <div class="col">Loans</div>
                                    <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?= $Route == 'accounting' ? 'active' : null ?>" href="accounting" tabindex="-1">
                                    <div class="avatar avatar-40 rounded icon"><i class="bi bi-file-earmark-text"></i></div>
                                    <div class="col">Accounting</div>
                                    <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?= $Route == 'members' ? 'active' : null ?>" href="<?= $baseURL ?>members" tabindex="-1">
                                    <div class="avatar avatar-40 rounded icon"><i class="bi bi-person-bounding-box"></i></div>
                                    <div class="col">Members</div>
                                    <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?= $Route == 'notifications' ? 'active' : null ?>" href="<?= $baseURL ?>notifications" tabindex="-1">
                                    <div class="avatar avatar-40 rounded icon"><i class="bi bi-bell"></i></div>
                                    <div class="col">Notification</div>
                                    <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="#" onclick="return _logout();" tabindex="-1">
                                    <div class="avatar avatar-40 rounded icon"><i class="bi bi-box-arrow-right"></i></div>
                                    <div class="col">Logout</div>
                                    <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <main class="h-100 <?= $classelement ?? null ?>">
        <?php if(!isset($noheader)) { ?>
            <!-- Header -->
            <header class="header position-fixed">
                <div class="row">
                    <?php if($isLoggedIn) { ?>
                    <div class="col-auto">
                        <a href="javascript:void(0)" target="_self" class="btn btn-light btn-44 menu-btn">
                            <i class="bi bi-list"></i>
                        </a>
                    </div>
                    <?php } ?>
                    <div class="col align-self-center text-center">
                        <div class="logo-small">
                            <a href="<?= $baseURL ?>">
                                <img src="<?= $publicURL ?>assets/img/logo.png" alt="">
                            </a>
                            <h5><?= $pagetitle ?? $AppName ?></h5>
                        </div>
                    </div>
                    <?php if($isLoggedIn) { ?>
                    <div class="col-auto">
                        <a href="<?= $baseURL ?>notifications" target="_self" class="btn btn-light btn-44">
                            <i class="bi bi-bell"></i>
                            <span class="count-indicator"></span>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </header>
        <?php } ?>