<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>High Court</title>
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/varaiables.css">
    <link rel="stylesheet" href="../css/mystyle.css">
    <link rel="stylesheet" href="../css/jquery-ui.css">

    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery-ui.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</head>
<body>

  <div class="container-fluid">
      <div class="page-header">
          <div class="row">
              <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                  <a href="../" class="no-text-decoration">
                      <img class="img-responsive" src="../images/logocopy.jpg" />
                  </a>
              </div>
              <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 mt20">
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
              </div>
          </div>
      </div>
  </div>