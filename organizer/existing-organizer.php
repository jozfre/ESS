<?php
include "../php/dbconn.php";
session_start();

if (isset($_SESSION['orgID'])) {
  header("Location: schedule/view-schedule.php");
  exit();
}

$error = '';

if (isset($_POST['accessOrg'])) {
  $orgName = mysqli_real_escape_string($conn, trim($_POST['orgName'])); // Trim spaces
  $orgTelNum = mysqli_real_escape_string($conn, trim($_POST['orgTelNum'])); // Trim spaces

  // Convert input to lowercase
  $orgName = strtolower($orgName);

  // Query with case-insensitive name check
  $query = "SELECT * FROM organizer WHERE LOWER(TRIM(orgName)) = '$orgName'";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    
    // Convert database orgName to lowercase and trim before comparison
    if (strtolower(trim($row['orgName'])) == $orgName && trim($row['orgTelNum']) == $orgTelNum) {
      $_SESSION['orgID'] = $row['orgID'];
      $_SESSION['orgName'] = $row['orgName'];
      $_SESSION['orgTelNum'] = $row['orgTelNum'];
      $_SESSION['orglogged'] = 1;
      header("Location: schedule/view-schedule.php");
      exit();
    } else {
      $error = 'Invalid name or telephone number.';
    }
  } else {
    $error = 'Organizer is not registered.';
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Event Scheduling System (ESS)</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <!-- /.login-logo -->
    <div class="login-logo"></div>
  </div>
  <div class="card card-outline card-green">
    <div class="card-header text-center">
      <img src="../images/logo-mbtho.png" alt="Logo MBTHO" class="img-fluid" style="max-width: 150px;">
    </div>
    <div class="card-header text-center">
      <a class="h1"><b>Event Scheduling System</b> (ESS)</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Please enter your name and telephone number to access as organizer</p>
      <?php if ($error): ?>
        <div class="alert alert-danger alert-center alert-dismissible fade show" role="alert">
          <?php echo $error; ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      <form action="existing-organizer.php" method="post">
        <label for="orgName" class="form-label">Name</label>
        <div class="input-group mb-3">
          <input name="orgName" id="orgName" type="text" class="form-control" placeholder="Enter your name">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <label for="orgTelNum" class="form-label">Telephone Number</label>
        <div class="input-group mb-3">
          <input name="orgTelNum" id="orgTelNum" type="tel" class="form-control" placeholder="Enter your telephone number" pattern="[0-9]{10}" title="Please enter a valid telephone number">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-phone"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">

          </div>
          <!-- /.col -->
          <div class="col-12">
            <button type="submit" name="accessOrg" class="btn btn-primary btn-block">Access as Organizer</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <div class="social-auth-links text-center mb-3">
        <p>- OR -</p>
        <a href="../" class="btn btn-block btn-light">
          Go Back to Login Page
        </a>
      </div>
      <!-- /.social-auth-links -->
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="../../plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../../dist/js/adminlte.min.js"></script>
</body>

</html>