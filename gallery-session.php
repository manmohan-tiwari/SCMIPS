<?php
/**
 * SCMIPS - Dynamic Session Gallery View (Phase 10)
 * 
 * Dynamically displays all published events and photographs for a given academic session.
 * Replaces static HTML pages (gallery201819.html, gallery201920.html, gallery202122.html)
 * with 100% design fidelity and simpleLightbox integration.
 */

require_once __DIR__ . '/includes/public_gallery_helper.php';

$identifier = $_GET['year'] ?? ($_GET['session_id'] ?? null);
$session = $identifier ? get_session_by_identifier($identifier) : null;
$assetPrefix = get_public_asset_prefix();

$events = $session ? get_session_published_events((int)$session['id']) : [];
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
    <title><?= $session ? htmlspecialchars($session['session_name']) : 'Gallery Session' ?> :: SCMIPS</title>
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
    <style type="text/css">
      .bg-primary {
        background-color: #021371 !important;
      }
      .btn-secondary {
        color: #212529;
        background-color: #f1a238;
        border-color: #f1a238;
      }
      header .drop-menu .nav-item>.nav-link {
        background: #f1a238 !important;
      }
      .navbar .navbar-nav>.nav-item:hover > a {
        background: #f1a238 !important;
      }
      a.brand-logo {
        background: none;
        min-height: 0 !important;
        height: auto;
      }
      .view-heading {
        margin-top: 2rem;
        margin-bottom: 1.5rem;
        font-weight: 700;
        color: #021371;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      .galleryImg {
        cursor: pointer;
        transition: transform .25s ease, box-shadow .25s ease;
      }
      .galleryImg:hover {
        transform: scale(1.03);
      }
      .galleryImg img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
      }
      .event-separator {
        border-top: 1px solid #e5e5e5;
        margin: 3rem 0;
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
                  <li class="nav-item"><a href="about-trust.html" class="nav-link">About SCMIPS</a></li>
                  <li class="nav-item"><a href="course-details.html" class="nav-link">Courses</a></li>
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

      <main class="main" style="margin-top: 140px; min-height: 50vh;">
        <?php if (!$session): ?>
          <section class="container py-5 text-center">
            <h2 class="view-heading">Session Not Found</h2>
            <p class="text-muted">The requested academic session does not exist or has been archived.</p>
            <a href="gallery.php" class="btn btn-secondary text-light mt-3">&laquo; Return to Gallery Hub</a>
          </section>
        <?php else: ?>
          <!-- Session Header & Back Link -->
          <section class="container pt-4 pb-2">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
              <a href="gallery.php" class="btn btn-sm btn-outline-dark mb-2">
                <i class="fa fa-arrow-left"></i> &laquo; All Academic Sessions
              </a>
              <span class="badge badge-light border text-dark p-2 mb-2 font-weight-bold">
                <?= htmlspecialchars($session['academic_year']) ?>
              </span>
            </div>
            <h2 class="view-heading text-center mt-3">
              <?= htmlspecialchars(strtoupper($session['session_name'])) ?>
            </h2>
          </section>

          <?php if (empty($events)): ?>
            <section class="container py-5 text-center text-muted">
              <p>No published events or photographs are currently available for this session.</p>
              <a href="gallery.php" class="btn btn-sm btn-secondary text-light">&laquo; Back to Gallery</a>
            </section>
          <?php else: ?>
            <!-- Dynamic Event Sections Loop -->
            <?php foreach ($events as $idx => $ev): ?>
              <?php $images = get_event_published_images((int)$ev['id']); ?>
              <section class="container">
                <h2 class="view-heading text-center" style="font-size: 1.5rem; color: #262626;">
                  <?= htmlspecialchars($ev['title']) ?>
                </h2>

                <?php if (!empty($ev['description'])): ?>
                  <p class="text-center text-muted small mx-auto mb-4" style="max-width: 700px;">
                    <?= htmlspecialchars($ev['description']) ?>
                  </p>
                <?php endif; ?>

                <div id="photo-gallery" class="mb-5 static-content">
                  <section class="row owl-carousel-wrapper mt-3 mb-4">
                    <?php if (empty($images)): ?>
                      <div class="col-12 text-center text-muted small py-3">
                        <em>Photographs for this event will be published shortly.</em>
                      </div>
                    <?php else: ?>
                      <?php foreach ($images as $img): ?>
                        <?php $resolvedUrl = resolve_gallery_image_url($img['file_path']); ?>
                        <div data-imgsrc="<?= htmlspecialchars($resolvedUrl) ?>" class="galleryImg col-xl-3 col-lg-3 col-md-6 mb-4">
                          <img src="<?= htmlspecialchars($resolvedUrl) ?>" class="img-fluid img-thumbnail" 
                               alt="<?= htmlspecialchars($img['original_name'] ?: $ev['title']) ?>" loading="lazy">
                        </div>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </section>
                </div>
              </section>
              <?php if ($idx < count($events) - 1): ?>
                <div class="container"><hr class="event-separator"></div>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endif; ?>
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
    <script>
      $(document).ready(function() {
        if (typeof $.fn.simpleLightbox !== 'undefined') {
          $('#photo-gallery .galleryImg, .galleryImg').simpleLightbox({
            sourceAttr: "data-imgsrc"
          });
        }
      });
    </script>
  </body>
</html>
