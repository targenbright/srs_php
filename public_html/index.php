<?php
  require "includes/authentication.php";
  include "includes/misc-variables.php";
  include "includes/helper-functions.php";

  session_start();
  /**
   * Check whether the user is logged in. Used to determine
   * how the sidenav will be displayed.
   */
  $loggedIn = !(!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] != true);
?>

<head>
  <title>Enrollment Project</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- <?php include('includes/head-tag-contents.php'); ?> -->
  <link rel="stylesheet" href="css/bootstrap.css" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/scripts.js"></script>
</head>

<body>
  <!-- Small screen navbar -->
  <nav class="navbar navbar-inverse visible-xs">
    <div class="container">
      <div class="navbar-header">
        <!-- Hamburger button -->
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Enrollment Project</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <?= generateNavbarElements($loggedIn); ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Regular display navbar -->
  <div class="container-fluid h-100">
    <div class="row content">
      <div class="col-sm-3 sidenav hidden-xs">
        <h2>Enrollment Project</h2>
        <ul class="nav nav-pills nav-stacked">
          <?= generateNavbarElements($loggedIn); ?>
        </ul>
      </div>
      <div class="col-sm-9 container-fluid h-100 embed-responsive">
        <iframe class="embed-responsive-item" <?php if ($loggedIn): ?> src="pages/dashboard.php" <?php else : ?> src="pages/welcome.html" <?php endif; ?> name="targetframe" allowTransparency="true" scrolling="yes"></iframe>
      </div>
    </div>
  </div>
</body>