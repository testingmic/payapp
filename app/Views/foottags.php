    </main>
    <footer class="footer">
        <div class="container">
            <ul class="nav nav-pills nav-justified">
                <li class="nav-item">
                    <a class="nav-link active" href="<?= $baseURL ?>">
                        <span>
                            <i class="nav-icon bi bi-house"></i>
                            <span class="nav-text">Home</span>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $baseURL ?>reports">
                        <span>
                            <i class="nav-icon bi bi-laptop"></i>
                            <span class="nav-text">Reports</span>
                        </span>
                    </a>
                </li>
                <li class="nav-item centerbutton">
                    <div class="nav-link">
                        <span class="theme-radial-gradient">
                            <i class="close bi bi-x"></i>
                            <img src="<?= $publicURL ?>assets/img/centerbutton.svg" class="nav-icon" alt="" />
                        </span>
                        <div class="nav-menu-popover justify-content-between">

                            <a class="btn btn-lg btn-icon-text" href="<?= $baseURL ?>accounting/payout">
                                <i class="bi bi-credit-card size-32"></i><span>Pay</span>
                            </a>

                            <a class="btn btn-lg btn-icon-text" href="<?= $baseURL ?>loans/requestloan">
                                <i class="bi bi-arrow-up-right-circle size-32"></i><span>Request</span>
                            </a>

                            <a class="btn btn-lg btn-icon-text" href="<?= $baseURL ?>loans/list">
                                <i class="bi bi-receipt size-32"></i><span>Loans</span>
                            </a>

                            <a class="btn btn-lg btn-icon-text" href="<?= $baseURL ?>accounting/receive">
                                <i class="bi bi-arrow-down-left-circle size-32"></i><span>Receive</span>
                            </a>
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $baseURL ?>rewards">
                        <span>
                            <i class="nav-icon bi bi-gift"></i>
                            <span class="nav-text">Rewards</span>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $baseURL ?>accounting/vault">
                        <span>
                            <i class="nav-icon bi bi-wallet2"></i>
                            <span class="nav-text">Wallet</span>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </footer>
    <?php if( !isset($noheader) && $isLoggedIn) { ?>
    <div class="position-fixed bottom-0 start-50 translate-middle-x  z-index-10">
        <div class="toast mb-3" role="alert" aria-live="assertive" aria-atomic="true" id="toastinstall" data-bs-animation="true">
            <div class="toast-header">
                <img src="<?= $publicURL ?>assets/img/favicon32.png" class="rounded me-2" alt="...">
                <strong class="me-auto">Install PWA App</strong>
                <small>now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <div class="row">
                    <div class="col">
                        Click "Install" to install the app & experience indepedent.
                    </div>
                    <div class="col-auto align-self-center ps-0">
                        <button class="btn-default btn btn-sm" id="addtohome">Install</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <script>var baseURL = "<?= $baseURL ?>"; </script>
    <script src="<?= $publicURL; ?>assets/js/jquery-3.3.1.min.js"></script>
    <script src="<?= $publicURL; ?>assets/js/popper.min.js"></script>
    <script src="<?= $publicURL; ?>assets/vendor/bootstrap-5/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $publicURL; ?>assets/js/jquery.cookie.js"></script>
    <script src="<?= $publicURL; ?>assets/js/main.js"></script>
    <script src="<?= $publicURL; ?>assets/js/color-scheme.js"></script>
    <script src="<?= $publicURL; ?>assets/js/pwa-services.js"></script>
    <?php if($isLoggedIn) { ?>
        <script src="<?= $publicURL; ?>assets/vendor/chart-js-3.3.1/chart.min.js"></script>
        <script src="<?= $publicURL; ?>assets/vendor/progressbar-js/progressbar.min.js"></script>
        <script src="<?= $publicURL; ?>assets/vendor/swiperjs-6.6.2/swiper-bundle.min.js"></script>
    <?php } ?>
    <script src="<?= $publicURL; ?>assets/vendor/select2/select2.js"></script>
    <script src="<?= $publicURL; ?>assets/vendor/sweetalert2/sweetalert2.min.js"></script>
    <?php if($isLoggedIn) { ?>
    <script src="<?= $publicURL; ?>assets/js/app.js"></script>
    <?php } ?>
    <script src="<?= $publicURL; ?>assets/vendor/toastr/toastr.js"></script>
    <script src="<?= $publicURL; ?>assets/js/script.js"></script>
    <?php if(isset($js_list) && is_array($js_list)) { ?>
        <?php foreach($js_list as $js) { ?>
            <script src="<?= $publicURL ?><?= $js ?>"></script>
        <?php } ?>
    <?php } ?>
    <script>
    <?php if(!$isLoggedIn) { ?>
        _auth_form();
    <?php } ?>
    if(!$(`form[id="authForm"]`).length && !$(`div[id="all_users_login_form"]`).length) {
        _ajax_cronjob();
        setInterval(() => { _ajax_cronjob(); }, $.interval);
    }
    </script>
</body>
</html>