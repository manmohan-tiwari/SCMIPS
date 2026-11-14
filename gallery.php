<?php
/**
 * SCMIPS - Dynamic Public Photo Gallery Portal (Phase 10)
 * 
 * Preserves 100% of the authentic SCMIPS design, layout, styling, and navigation,
 * while dynamically rendering academic sessions from the MySQL database.
 */

require_once __DIR__ . '/includes/public_gallery_helper.php';

$sessions = get_published_gallery_sessions();
$assetPrefix = get_public_asset_prefix();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="author" content="Shri Cloth Market Institute of Professional Studies, Indore">
    <meta name="keywords" content="BBA, BCA, B.Sc. (CS / IT), Indore, MP, SCMIPS, scmips">
    <meta name="description" content="Shri Cloth Market Institute of Professional Studies, Indore | Management and Computer Science Institute | BBA, BCA, B.Sc. (CS / IT).">
    <meta property="og:title" content="Shri Cloth Market Institute of Professional Studies, Indore">
    <meta property="og:description" content="Shri Cloth Market Institute of Professional Studies, Indore has glorious history under Shri M. T. Cloth Market Association, Indore. The institute is approved by Govt. of Madhya Pradesh and affiliated to Devi Ahilya Vishwavidyalaya.">
    <meta property="og:image" content="<?= $assetPrefix ?>assets/images/logo.png">
    <meta property="og:url" content="gallery.php">
    <meta property="og:site_name" content="Shri Cloth Market Institute of Professional Studies, Indore">
    <link rel="shortcut icon" href="<?= $assetPrefix ?>assets/images/favicon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    <link href="<?= $assetPrefix ?>assets/css/style.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/elegant-fonts.css">
    <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/themify-icons.css">
    <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/swiper.min.css">
    <link rel="stylesheet" href="<?= $assetPrefix ?>assets/style.css">
    <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/bootstrap-select.min.css">
    <title>Shri Cloth Market Institute of Professional Studies :: Photo Gallery</title>
    <style type="text/css">
      .bg-primary {
        background-color: #021371 !important;
      }
      .btn-secondary {
        color: #212529;
        background-color: #f1a238;
        border-color: #f1a238;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.15), 0 1px 1px rgba(0,0,0,0.075);
      }
      header .drop-menu .nav-item>.nav-link {
        background: #f1a238 !important;
      }
      .navbar .navbar-nav>.nav-item:hover > a {
        background: #f1a238 !important;
      }
      .navbarDivider {
        border: none;
      }
      .carousel-item img {
        max-height: 400px;
        object-fit: cover;
      }
      .keep-updated {
        background-color: #f1a238;
      }
      a.brand-logo {
        background: none;
        min-height: 0 !important;
        height: auto;
        max-height: 100%;
        min-width: 0;
        width: auto;
        overflow: hidden;
      }
      .home-page-icon-boxes {
        padding: 60px 0 90px 0;
      }
      .icon-box {
        padding: 40px 20px;
        border-radius: 24px;
        background: #ecf2f5;
        text-align: center;
        transition: all .35s;
        min-height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
      }
      .icon-box:hover {
        background: #021371;
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(2, 19, 113, 0.2);
      }
      .icon-box h5 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
        color: #262626;
        transition: color .2s;
      }
      .icon-box:hover h5 {
        color: #ffffff;
      }
      .icon-box small {
        display: block;
        margin-top: 6px;
        color: #6c757d;
        font-size: 0.8rem;
      }
      .icon-box:hover small {
        color: #f1a238;
      }
      a.session-card-link {
        text-decoration: none;
      }
      .foot-logo {
        display: flex;
        align-items: flex-start;
      }
      .footer-top {
        background-color: #4e4e4e !important;
      }
      section.footer-bottom.bg-primary {
        background: #f1a238 !important;
      }
    </style>
  </head>
  <body>
    <section class="grid">
      <!-- Header Begins -->
      <header class="head-header">
        <section class="header fixed-top shadow">
          <section class="top-header navbar-dark bg-primary">
            <div class="container d-flex">
              <a href="index.html" class="brand-logo shadow-sm justify-content-center align-items-center d-inline-flex align-top flex-column">
                <img class="img-fluid" src="<?= $assetPrefix ?>assets/images/logo.png" alt="Shri Cloth Market Institute of Professional Studies">
              </a>
              <div class="d-flex align-items-center flex-grow-1">
                <div class="text-light d-flex flex-column ml-3">
                  <h1 class="logo-text">Shri Cloth Market Institute of Professional Studies, Indore</h1>
                  <small class="text-uppercase">APPROVED BY Dept. of Higher Education, M.P.</small>
                  <small class="text-uppercase">AFFILIATED TO DAVV, INDORE (M.P.)</small>
                </div>
                <span class="ml-auto navbarDivider d-none d-sm-inline-flex">
                  <a href="admission-enquiry.html" class="btn btn-secondary text-light">ADMISSION ENQUIRY</a>
                </span>
              </div>
            </div>
          </section>
          <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
              <a href="admission-enquiry.html" class="btn btn-secondary text-light d-sm-none">ADMISSION ENQUIRY</a>
              <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav">
                  <li class="nav-item"><a href="index.html" class="nav-link">Home</a></li>
                  <li class="nav-item">
                    <a href="about-trust.html" class="nav-link has-submenu">About SCMIPS</a>
                    <ul class="drop-menu animated fadeIn">
                      <li class="nav-item"><a class="nav-link" href="about-trust.html">Profile</a></li>
                      <li class="nav-item"><a class="nav-link" href="vision.html">Vision and Mission</a></li>
                      <li class="nav-item"><a class="nav-link" href="foundmsg.html">Foundation Society Message</a></li> 
                      <li class="nav-item"><a class="nav-link" href="principal.html">About Principal</a></li>
                      <li class="nav-item"><a class="nav-link" href="rules.html">Rules and Regulations</a></li>
                      <li class="nav-item"><a class="nav-link" href="ragging.html">Anti-Ragging Rules</a></li>
                      <li class="nav-item"><a class="nav-link" href="vishakha.html">Vishakha</a></li>
                    </ul>
                  </li>
                  <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link has-submenu">Courses</a>
                    <ul class="drop-menu animated fadeIn">
                      <li class="nav-item"><a class="nav-link" href="course-details.html">Courses Offered</a></li>
                      <li class="nav-item"><a class="nav-link" href="fee-structure.html">Fee</a></li>
                      <li class="nav-item"><a class="nav-link" href="seats.html">Seats</a></li>
                    </ul>
                  </li>
                  <li class="nav-item"><a href="events.html" class="nav-link">Events</a></li> 
                  <li class="nav-item"><a href="gallery.php" class="nav-link active font-weight-bold">Gallery</a></li> 
                  <li class="nav-item"><a href="contact.html" class="nav-link">Contact</a></li> 
                </ul>
              </div>
            </div>
          </nav>
        </section>
      </header>
      <!-- Header Ends -->

      <main class="main" style="margin-top: 140px;">
        <h2 class="view-heading text-center mt-4">PHOTO GALLERY</h2>
        <div class="home-page-icon-boxes">
          <div class="container">
            <div class="row">
              <?php if (empty($sessions)): ?>
                <div class="col-12 text-center py-5">
                  <p class="text-muted">No gallery sessions are currently published. Please check back soon.</p>
                </div>
              <?php else: ?>
                <?php foreach ($sessions as $session): ?>
                  <div class="col-12 col-md-4 col-lg-3 mt-3">
                    <a href="gallery-session.php?year=<?= urlencode($session['academic_year']) ?>" class="session-card-link">
                      <div class="icon-box"> 
                        <div>
                          <h5><?= htmlspecialchars(strtoupper($session['session_name'])) ?></h5>
                          <small><?= (int)$session['published_event_count'] ?> Event<?= (int)$session['published_event_count'] === 1 ? '' : 's' ?></small>
                        </div>
                      </div>
                    </a>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </main>

      <!-- Footer Begins -->
      <footer class="footer wow fadeIn">
        <section class="footer-top">
          <div class="container">
            <div class="row">
              <div class="col-md-4 mb-3">
                <div class="pl-3">
                  <h4>Useful Links</h4>
                  <ul class="list-unstyled footer-nav">
                    <li><a href="about-trust.html">The Institute</a></li>
                    <li><a href="course-details.html">Professional Courses</a></li>
                    <li><a href="course-details.html">Traditional Courses</a></li>
                    <li><a href="openings.html">Career</a></li>
                    <li><a href="admission-enquiry.html">Admission Enquiry</a></li>
                    <li><a href="placement.html">Placement</a></li>
                  </ul>
                </div>
              </div>

              <div class="col-md-4 mb-3">
                <div class="foot-logo">
                  <img src="<?= $assetPrefix ?>assets/images/flogo.jpg" class="img-fluid" alt="SCMIPS Logo" />
                </div>
              </div>

              <div class="col-md-4 mb-3">
                <h4>Get In Touch</h4>
                <ul class="social-links">
                  <li><a href="https://www.facebook.com/scmipscollege" target="_blank"><img src="<?= $assetPrefix ?>assets/images/facebook.svg" alt="Facebook"></a></li>
                  <li><a href="#" target="_blank"><img src="<?= $assetPrefix ?>assets/images/twitter.svg" alt="Twitter"></a></li>
                  <li><a href="https://www.youtube.com/channel/UCJNR1Bm2BQN3XHIWMHXMdxw" target="_blank"><img src="<?= $assetPrefix ?>assets/images/youtube.svg" alt="YouTube"></a></li>
                  <li><a href="#" target="_blank"><img src="<?= $assetPrefix ?>assets/images/linkedin.svg" alt="LinkedIn"></a></li>
                  <li><a href="#" target="_blank"><img src="<?= $assetPrefix ?>assets/images/instagram.svg" alt="Instagram"></a></li>
                </ul>
                <section class="address">
                  <div class="d-flex">
                    <span class="icn-block text-secondary"><i class="icon-location" role="icon"></i></span>
                    <div class="pl-3">
                      <div>Vidya Parisar, 70 Ganeshganj, Near Bada Ganpati Area,</div>
                      <div>Indore- 452002 Madhya Pradesh, India</div>
                    </div>
                  </div>
                  <div class="d-flex">
                    <span class="icn-block text-danger"><i class="icon-phone" role="icon"></i></span>
                    <div class="pl-3">
                      <div><b>Mobile</b>: 9425911332, 9827349273</div>
                      <div><b>Phone</b>: +91-731-2410280, 2410015</div>
                      <div><b>Admission Enquiry</b>: 6262079111, 6262079222, 6262079333</div>
                    </div>
                  </div>
                </section>
              </div>
            </div>
          </div>
        </section>
        <section class="footer-bottom bg-primary text-light d-flex align-items-center justify-content-center" style="font-size: 12px; padding: 12px 0;">
          &copy; <?= date('Y') ?> scmips.in, All Rights Reserved.
        </section>
      </footer>
    </section>

    <!-- Scripts -->
    <script type="text/javascript" src="<?= $assetPrefix ?>assets/js/jquery.js"></script>
    <script type="text/javascript" src="<?= $assetPrefix ?>assets/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="<?= $assetPrefix ?>assets/js/main.js"></script>
  </body>
</html>
