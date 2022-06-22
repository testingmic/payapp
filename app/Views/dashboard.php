<?php
// set the header file
require "headtags.php";

$welcome = welcome_msg();
?>
<div class="main-container container">
    <div class="row mb-4">
        <div class="col-auto">
            <div class="avatar avatar-50 shadow rounded-10">
                <img src="<?= $publicURL ?><?= $userData['image'] ?>" alt="">
            </div>
        </div>
        <div class="col align-self-center ps-0">
            <h4 class="text-color-theme"><span class="fw-normal">Hi</span>, <?= $userData['firstname'] ?></h4>
            <p class="text-muted"><?= $welcome['greeting'] ?></p>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12 px-0">
            <div class="swiper-container cardswiper">
                <div class="swiper-wrapper">

                    <?php if (!empty($accounts)) { ?>
                        <?php foreach ($accounts as $account) { ?>
                            <div class="swiper-slide">
                                <div class="card <?= $account['background_color'] ?>">
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-auto align-self-center">
                                            </div>
                                            <div class="col align-self-center text-end">
                                                <p class="small">
                                                    <span class="text-uppercase size-10">Validity</span><br>
                                                    <span class="text-muted">
                                                        <?= date("m/y", strtotime($account['expiry_date'])) ?>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <h4 class="fw-normal mb-2">
                                                    <?= $account['balance'] ?>
                                                    <span class="small text-muted"><?= $account['currency'] ?></span>
                                                </h4>
                                                <p class="mb-0 text-muted size-12">
                                                    <?php
                                                    $split = str_split($account['account_number'], 4);
                                                    foreach ($split as $item) {
                                                        echo $item . " ";
                                                    }
                                                    ?>
                                                </p>
                                                <p class="text-muted size-12"><?= ucfirst($account['account_type']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>

    <?php if ($AuthObj->hasAccess('members', 'list')) { ?>
        <div class="row mb-3">
            <div class="col">
                <h6 class="title">Members</h6>
            </div>
            <div class="col-auto">
                <a href="<?= $baseURL ?>members/list" class="small">View all</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 px-0">
                <div class="swiper-container connectionwiper">
                    <div class="swiper-wrapper">

                        <?php if (!empty($members)) { ?>
                            <?php foreach ($members as $user) { ?>
                                <div class="swiper-slide">
                                    <a href="<?= $baseURL ?>members/view/<?= $user['user_id'] ?>" class="card text-center">
                                        <div class="card-body">
                                            <figure class="avatar avatar-50 shadow-sm mb-1 rounded-10">
                                                <img src="<?= $publicURL ?><?= $user['image'] ?>" alt="">
                                            </figure>
                                            <p class="text-color-theme size-12 small"><?= $user['firstname'] ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php } ?>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="row mb-3">
        <div class="col">
            <h6 class="title">Account Summary</h6>
        </div>
        <div class="col-auto"></div>
    </div>
    <div class="row account-summary mb-4">
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-auto">
                            <div class="circle-small">
                                <div id="circleprogressone"></div>
                                <div class="avatar avatar-30 alert-primary text-primary rounded-circle">
                                    <i class="bi bi-globe"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto align-self-center ps-0">
                            <p class="small mb-1 text-muted">Deposits</p>
                            <p>0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-auto">
                            <div class="circle-small">
                                <div id="circleprogresstwo"></div>
                                <div class="avatar avatar-30 alert-danger text-danger rounded-circle">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto align-self-center ps-0">
                            <p class="small mb-1 text-muted">Withdrawal</p>
                            <p>0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-3">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-auto">
                            <div class="circle-small">
                                <div id="circleprogresstwo"></div>
                                <div class="avatar avatar-30 alert-success text-success rounded-circle">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto align-self-center ps-0">
                            <p class="small mb-1 text-muted">Balance</p>
                            <p>0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-12 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-auto">
                            <div class="avatar avatar-40 alert-danger text-danger rounded-circle">
                                <i class="bi bi-house"></i>
                            </div>
                        </div>
                        <div class="col align-self-center ps-0">
                            <div class="row mb-2">
                                <div class="col">
                                    <p class="small text-muted mb-0">Loans</p>
                                    <p>0.00</p>
                                </div>
                                <div class="col-auto text-end">
                                    <p class="small text-muted mb-0">Next EMI</p>
                                    <p class="small"></p>
                                </div>
                            </div>

                            <div class="progress alert-danger h-4">
                                <div class="progress-bar bg-danger w-50" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <h6 class="title">Transactions<br><small class="fw-normal text-muted">Today, 24 Aug 2021</small>
            </h6>
        </div>
        <div class="col-auto align-self-center">
            <a href="transactions.html" class="small">View all</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 px-0">
            <ul class="list-group list-group-flush bg-none">
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-auto">
                            <div class="avatar avatar-50 shadow rounded-10 ">
                                <img src="assets/img/company4.jpg" alt="">
                            </div>
                        </div>
                        <div class="col align-self-center ps-0">
                            <p class="text-color-theme mb-0">Zomato</p>
                            <p class="text-muted size-12">Food</p>
                        </div>
                        <div class="col align-self-center text-end">
                            <p class="mb-0">-25.00</p>
                            <p class="text-muted size-12">Debit Card 4545</p>
                        </div>
                    </div>
                </li>

                <li class="list-group-item">
                    <div class="row">
                        <div class="col-auto">
                            <div class="avatar avatar-50 shadow rounded-10">
                                <img src="assets/img/company5.png" alt="">
                            </div>
                        </div>
                        <div class="col align-self-center ps-0">
                            <p class="text-color-theme mb-0">Uber</p>
                            <p class="text-muted size-12">Travel</p>
                        </div>
                        <div class="col align-self-center text-end">
                            <p class="mb-0">-26.00</p>
                            <p class="text-muted size-12">Debit Card 4545</p>
                        </div>
                    </div>
                </li>

                <li class="list-group-item">
                    <div class="row">
                        <div class="col-auto">
                            <div class="avatar avatar-50 shadow rounded-10">
                                <img src="assets/img/company1.png" alt="">
                            </div>
                        </div>
                        <div class="col align-self-center ps-0">
                            <p class="text-color-theme mb-0">Starbucks</p>
                            <p class="text-muted size-12">Food</p>
                        </div>
                        <div class="col align-self-center text-end">
                            <p class="mb-0">-18.00</p>
                            <p class="text-muted size-12">Cash</p>
                        </div>
                    </div>
                </li>

                <li class="list-group-item">
                    <div class="row">
                        <div class="col-auto">
                            <div class="avatar avatar-50 shadow rounded-10">
                                <img src="assets/img/company3.jpg" alt="">
                            </div>
                        </div>
                        <div class="col align-self-center ps-0">
                            <p class="text-color-theme mb-0">Walmart</p>
                            <p class="text-muted size-12">Clothing</p>
                        </div>
                        <div class="col align-self-center text-end">
                            <p class="mb-0">-105.00</p>
                            <p class="text-muted size-12">Wallet</p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

</div>
<?php require "foottags.php"; ?>