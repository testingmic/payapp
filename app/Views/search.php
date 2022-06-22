<?php
// set the header file
require "headtags.php";
?>
<div class="vertical-overlay"></div>
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <?= breadcrumb('Search Results', ['dashboard', 'Dashboard']); ?>
                </div>
            </div>
            <div class="row">
                

                <div class="card">
                    <div class="card-header border-0">
                        <div class="row justify-content-center mb-4">
                            <div class="col-lg-6">
                                <div class="row g-2">
                                    <div class="col">
                                        <div class="position-relative mb-3">
                                            <input data-input="searchterm" type="text" class="form-control form-control-lg bg-light border-light" placeholder="Search here.." value="<?= $searchterm ?>">
                                            <a data-item="voicesearch" class="btn btn-link hidden link-success btn-lg position-absolute end-0 top-0" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="ri-mic-fill"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button onclick="javascript:_search_term()" type="submit" class="btn btn-primary btn-lg waves-effect waves-light"><i class="mdi mdi-magnify me-1"></i> Search</button>
                                    </div>
                                </div>
                            </div><!--end col-->
                            <div class="col-lg-12">
                                <h5 class="fs-16 fw-semibold text-center mb-0">Showing results for "<span class="text-primary fw-medium fst-italic"><?= $searchterm ?></span> "</h5>
                            </div>
                        </div><!--end row-->

                        <div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
                            <div class="offcanvas-body">
                                <button type="button" class="btn-close text-reset float-end" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                <div class="d-flex flex-column h-100 justify-content-center align-items-center">
                                    <div class="search-voice">
                                        <i class="ri-mic-fill cursor align-middle"></i>
                                        <span class="voice-wave"></span>
                                        <span class="voice-wave"></span>
                                        <span class="voice-wave"></span>
                                    </div>
                                    <h4 data-item="searchterm">Talk to me, what can I do for you?</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#members" role="tab" aria-selected="false">
                                    <i class="ri-user-line text-muted align-bottom me-1"></i> Members
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" id="images-tab" href="#events" role="tab" aria-selected="true">
                                    <i class="ri-image-fill text-muted align-bottom me-1"></i> Events
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#organizations" role="tab" aria-selected="false">
                                    <i class="ri-list-unordered text-muted align-bottom me-1"></i> Organizations
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#bibleclasses" role="tab" aria-selected="false">
                                    <i class="ri-video-line text-muted align-bottom me-1"></i> Classes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#correspondence" role="tab" aria-selected="false">
                                    <i class="las la-envelope-open text-muted align-bottom me-1"></i> Correspondences
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content text-muted">
                            <div class="tab-pane active" id="members" role="tabpanel">
                                <div class="team-list grid-view-filter row">
                                    <?php if(isset($results['members'])) { ?>
                                        <?php foreach($results['members'] as $member) { ?>
                                            <div class="col-lg-3 col-md-4">
                                                <div class="card team-box">
                                                    <div class="team-cover">
                                                        <img src="<?= $baseURL ?><?= $member['banner'] ?>" alt="" class="img-fluid">
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <div class="row align-items-center team-row">
                                                            <div class="col team-settings">
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="bookmark-icon flex-shrink-0 me-2">
                                                                            <input type="checkbox" id="favourite1" class="bookmark-input bookmark-hide">
                                                                            <label for="favourite1" class="btn-star">
                                                                                <svg width="20" height="20">
                                                                                    <use xlink:href="#icon-star"></use>
                                                                                </svg>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col">
                                                                <div class="team-profile-img">
                                                                    <div class="avatar-lg img-thumbnail rounded-circle flex-shrink-0">
                                                                        <img src="<?= $baseURL ?><?= $member['image'] ?>" alt="" class="img-fluid d-block rounded-circle">
                                                                    </div>
                                                                    <div class="team-content">
                                                                        <a href="<?= $baseURL ?>members/view/<?= $member['item_id'] ?>?h=<?= $searchterm ?>" aria-controls="offcanvasExample">
                                                                            <h5 class="fs-17 mb-1 text-uppercase"><?= $member['title'] ?> <?= $member['firstname'] ?> <?= $member['middlename'] ?> <?= $member['lastname'] ?></h5>
                                                                        </a>
                                                                        <p class="text-muted mb-0"><?= !empty($member['profession']) ? $member['profession'] : "<br>"; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col">
                                                                <div class="row text-muted text-center">
                                                                    <div class="col-6 border-end border-end-dashed">
                                                                        <h5 class="mb-1"><?= $member['gender'] ?></h5>
                                                                        <p class="text-muted mb-0">Gender</p>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <h5 class="mb-1"><?= $member['date_of_birth'] ?></h5>
                                                                        <p class="text-muted mb-0">Date of Birth</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-2 col">
                                                                <div class="text-end">
                                                                    <a href="<?= $baseURL ?>members/view/<?= $member['item_id'] ?>?h=<?= $searchterm ?>" class="btn btn-outline-success view-btn">View Profile</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!--end card-->
                                            </div>
                                        <?php } ?>
                                        <?php if(empty($results['members'])) { ?>
                                            <div class="alert alert-warning mb-0">No results found for the search term</div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="tab-pane" id="events" role="tabpanel">
                                <div class="row">
                                    <?php if(isset($results['events'])) { ?>
                                        <?php foreach($results['events'] as $event) { ?>
                                            <div class="col-lg-6">
                                                <div class="card border">
                                                    <div class="card-body">
                                                        <div class="d-sm-flex">
                                                            <div class="flex-grow-1 ms-sm-4 mt-3 mt-sm-0">
                                                                <ul class="list-inline mb-2">
                                                                    <li class="list-inline-item"><span class="badge badge-soft-secondary fs-12"><?= $event['name'] ?? null ?></span></li>
                                                                </ul>
                                                                <h5><a href="<?= $baseURL ?>events/view/<?= $event['item_id'] ?>?h=<?= $searchterm ?>"><?= $event['title'] ?></a></h5>
                                                                <div class="mb-2"><?= $event['caption'] ?></div>
                                                                <ul class="list-inline mb-0">
                                                                    <li class="list-inline-item"><i class="ri-user-3-fill text-success align-middle me-1"></i> <?= $event['venue'] ?></li>
                                                                    <li class="list-inline-item"><i class="ri-calendar-2-fill text-success align-middle me-1"></i> <?= date("d M, Y", strtotime($event['event_date'])) ?></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!--end card-->
                                            </div>
                                        <?php } ?>
                                        <?php if(empty($results['events'])) { ?>
                                            <div class="alert alert-warning mb-0">No events found for the search term</div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="tab-pane" id="organizations" role="tabpanel">
                                <div class="row">
                                    <?php if(isset($results['organizations'])) { ?>
                                        <?php foreach($results['organizations'] as $organization) { ?>
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card border-grey bg-gray ribbon-box right overflow-hidden">
                                                    <div class="card-body text-center p-4">
                                                        <h5 class="mb-1 mt-4"><a href="<?= $baseURL ?>organizations/view/<?= $organization['item_id'] ?>?h=<?= $searchterm ?>" class="link-primary"><?= $organization['name'] ?></a></h5>
                                                        <div class="row justify-content-center">
                                                            <div class="col-lg-8">
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="row mt-4">
                                                            <div class="col-lg-12 border-end-dashed border-end">
                                                                <h5><?= $organization['members_count'] ?? 0 ?></h5>
                                                                <span class="text-muted">Members Count</span>
                                                            </div>
                                                        </div>
                                                        <div class="mt-4">
                                                            <a href="<?= $baseURL ?>organizations/view/<?= $organization['item_id'] ?>?h=<?= $searchterm ?>" class="btn btn-outline-success w-100">View Details</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if(empty($results['organizations'])) { ?>
                                            <div class="alert alert-warning mb-0">No organizations found for the search term</div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>                            
                            </div>
                            <div class="tab-pane" id="bibleclasses" role="tabpanel">
                                <div class="row">
                                    <?php if(isset($results['bibleclasses'])) { ?>
                                        <?php foreach($results['bibleclasses'] as $class) { ?>
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card border-grey ribbon-box right overflow-hidden">
                                                    <div class="card-body text-center p-4">
                                                        <h5 class="mb-1 mt-4"><a href="<?= $baseURL ?>bibleclasses/view/<?= $class['item_id'] ?>?h=<?= $searchterm ?>" class="link-primary"><?= $class['name'] ?></a></h5>
                                                        <p class="text-muted mb-4"><?= $class['language'] ?></p>
                                                        <div class="row justify-content-center">
                                                            <div class="col-lg-8">
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="row mt-4">
                                                            <div class="col-lg-12 border-end-dashed border-end">
                                                                <h5><?= $class['members_count'] ?? 0 ?></h5>
                                                                <span class="text-muted">Members Count</span>
                                                            </div>
                                                        </div>
                                                        <div class="mt-4">
                                                            <a href="<?= $baseURL ?>bibleclasses/view/<?= $class['item_id'] ?>?h=<?= $searchterm ?>" class="btn btn-outline-success w-100">View Details</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if(empty($results['bibleclasses'])) { ?>
                                            <div class="alert alert-warning mb-0">No bible classes found for the search term</div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="tab-pane" id="correspondence" role="tabpanel">
                                <div class="row">
                                    <?php if(isset($results['correspondence'])) { ?>
                                        <?php foreach($results['correspondence'] as $event) { ?>
                                            <div class="col-lg-6">
                                                <div class="card border">
                                                    <div class="card-body">
                                                        <div class="d-sm-flex">
                                                            <div class="flex-grow-1 ms-sm-4 mt-3 mt-sm-0">
                                                                <ul class="list-inline mb-2">
                                                                    <li class="list-inline-item"><span class="badge badge-soft-secondary fs-12"><?= $event['reference_id'] ?? null ?></span></li>
                                                                </ul>
                                                                <h5><a href="<?= $baseURL ?>correspondence/view/<?= $event['item_id'] ?>?h=<?= $searchterm ?>"><?= $event['title'] ?></a></h5>
                                                                <div class="mb-2"><?= word_limiter($event['description'], 30) ?></div>
                                                                <ul class="list-inline mb-0">
                                                                    <li class="list-inline-item"><i class="ri-user-3-fill text-success align-middle me-1"></i> <?= $event['signatories'] ?></li>
                                                                    <li class="list-inline-item"><i class="ri-calendar-2-fill text-success align-middle me-1"></i> <?= date("d M, Y", strtotime($event['date_of_letter'])) ?></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!--end card-->
                                            </div>
                                        <?php } ?>
                                        <?php if(empty($results['correspondence'])) { ?>
                                            <div class="alert alert-warning mb-0">No correspondence found for the search term</div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                                
            </div>
        </div>
    </div>
</div>
<?php require "foottags.php"; ?>