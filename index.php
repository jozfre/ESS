<?php
include "php/dbconn.php";
session_start();

if (isset($_SESSION['userID'])) {
  header("Location: admin/dashboard.php");
  exit();
}

$error = '';

if (isset($_POST['login'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  // Replace with your own query to check the credentials
  $query = "SELECT * FROM user WHERE email='$email' AND password='$password' AND isDeleted = 0";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['userID'] = $row['userID'];
    $_SESSION['name'] = $row['name'];
    $_SESSION['telNum'] = $row['telNum'];
    $_SESSION['email'] = $row['email'];
    $_SESSION['userlogged'] = 1;
    header("Location: admin/dashboard.php");
    exit();
  } else {
    $error = 'Invalid email or password.';
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
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <!-- /.login-logo -->
    <div class="login-logo"></div>
  </div>
  <div class="card card-outline card-green">
    <div class="card-header text-center">
      <img src="images/logo-mbtho.png" alt="Logo MBTHO" class="img-fluid" style="max-width: 150px;">
    </div>
    <div class="card-header text-center">
      <a class="h1"><b>Event Scheduling System</b> (ESS)</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Enter your email and password below to log in as admin</p>

      <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo $error; ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </div>
      <?php endif; ?>

      <form action="index.php" method="post">
        <label for="email" class="form-label">Email</label>
        <div class="input-group mb-3">
          <input name="email" id="email" type="email" class="form-control" placeholder="example@email.com" title="Please enter your email" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <label for="password" class="form-label">Password</label>
        <div class="input-group mb-3">
          <input name="password" id="password" type="password" class="form-control" placeholder="********" title="Please enter your password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">

          </div>
          <!-- /.col -->
          <div class="col-12">
            <button type="submit" name="login" class="btn btn-success btn-block">Log In as Admin</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <div class="social-auth-links text-center mb-3">
        <p>- OR -</p>
        <a href="organizer/continue-as-organizer.php" class="btn btn-block btn-dark">
          Continue as Organizer
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