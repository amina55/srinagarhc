<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>High Court</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/font-awesome.min.css" />
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/jquery-ui.css">
    <link rel="stylesheet" href="../css/datatables.min.css">
    <link rel="stylesheet" href="../css/developer.css">
      <link rel="stylesheet" href="../css/varaiables.css">

    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0../css/font-awesome.min.css">
-->
    <script src="../js/jquery.min.js"></script>
      <script src="../js/jquery-ui.min.js"></script>
      <script src="../js/jquery.validate.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/helper.js"></script>
    <script src="../js/datatables.min.js"></script>
    <script src="../js/high-court.js"></script>


  </head>
  <body>
  <header>
      <div class="container-fluid">
          <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="col-sm-10">
                      <a href="../" class="logo pull-left no-text-decoration">
                          <img src="../images/logocopy.jpg" />
                      </a>
                  </div>

                  <div class="col-sm-2 mt20">
                      <?php if (!empty($_SESSION['logged_in']) ) { ?>
                          <div class="dropdown pull-right">
                              <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Hello <?php echo !empty($_SESSION['logged_in']) ? $_SESSION['logged_in'] : 'user' ?>
                                  <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu" aria-labelledby="dLabel">
                                  <li>
                                      <a href="../login/logout.php">Log out</a>
                                  </li>
                              </ul>
                          </div>
                      <?php } ?>
                  </div>
              </div>
          </div>
      </div>

  </header>

    <section class="container-fluid mt20">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

