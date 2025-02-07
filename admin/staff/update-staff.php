<?php
session_start();
if(!isset($_SESSION['userlogged']) || ($_SESSION['userlogged'] != 1))
{
    header("Location: ../../index.php");
}

if(!isset($_SESSION['userID']))
{
    header("Location: ../../php/logout.php");
}

include "../../php/dbconn.php";

//sql to retrieve data for this user
if (isset($_GET['userID'])) {
  $userID = mysqli_real_escape_string($conn, $_GET['userID']);
  $sql = "SELECT * FROM user WHERE userID='$userID'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
  } else {
    die(mysqli_error($conn));
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = mysqli_real_escape_string($conn, trim($_POST['name']));
  $email = mysqli_real_escape_string($conn, trim($_POST['email']));
  $telNum = mysqli_real_escape_string($conn, trim($_POST['telNum']));
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);

  if ($password === $confirmPassword) {
    $sql = "UPDATE user SET name='$name', email='$email', telNum='$telNum', password='$password' WHERE userID='$userID'";
    if (mysqli_query($conn, $sql)) {
      $_SESSION['update_success'] = true;
      $_SESSION['name'] = $name;
      header("Location: view-staff.php?userID=$userID");
      exit();
    } else {
      echo "Error updating record: " . mysqli_error($conn);
    }
  } else {
    echo "Passwords do not match.";
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
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 d-flex flex-column" style="height: 100vh;">
    <!-- Brand Logo -->
    <a class="brand-link">
      <img src="../../images/logo-mbtho.png" alt="logo-mbtho" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light text-wrap d-none d-sm-inline text-wrap" style="white-space: normal;" title="Event Scheduling System (ESS)">ESS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column flex-grow-1">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../../images/user-icon.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block text-truncate"><?php if(isset($_SESSION['name'])) { echo $_SESSION['name']; } ?></a>
          <a href="#" class="d-block">ADMIN</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2 flex-grow-1">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="../dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../schedule/view-schedule.php" class="nav-link">
              <i class="nav-icon fas fa-calendar-alt"></i>
              <p>
                Schedule
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../event/list-event.php" class="nav-link">
              <i class="nav-icon fas fa-ticket-alt"></i>
              <p>
                Event
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../request/list-request.php" class="nav-link">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>
                Request
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../mosque-space/list-space.php" class="nav-link">
              <i class="nav-icon fas fa-mosque"></i>
              <p>
                Mosque Spaces
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="list-staff.php" class="nav-link active">
              <i class="nav-icon far fa-address-card"></i>
              <p>
                Staff
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
      <div class="mt-auto mb-3">
            <a href="../../php/logout.php" class="btn btn-danger btn-block text-white">
                <i class="nav-icon fas fa-sign-out-alt"></i> Log Out
            </a>
        </div>
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Staff</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="list-staff.php">List of Staff</a></li>
                <li class="breadcrumb-item"><a href="view-staff.php?userID=<?php echo $user['userID']; ?>">View Staff Details</a></li>
                <li class="breadcrumb-item active">Update Staff Details</li>
            </ol>
          </div>
          <!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Update Staff Details</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form id="updateForm" action="update-staff.php?userID=<?php echo $userID; ?>" method="post" onsubmit="return validateForm()">
                <div class="card-body">
                  <div class="form-group">
                    <label for="userID">Staff ID</label>
                    <input name="userID" class="form-control" id="userID" value="<?= $user['userID'];?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="name">Staff Name</label>
                    <input name="name" class="form-control" id="name" value="<?= $user['name'];?>" required>
                  </div>
                  <div class="form-group">
                    <label for="telNum">Telephone Number</label>
                    <input name="telNum" class="form-control" id="telNum" pattern="[0-9]{10}|[0-9]{11}" value="<?= $user['telNum'];?>" required>
                  </div>
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input name="email" class="form-control" id="email" value="<?= $user['email'];?>" required>
                  </div>
                  <div class="form-group">
                    <label for="password">Password</label>
                    <input name="password" type="password" class="form-control" id="password" value="<?= $user['password'];?>" required>
                  </div>
                  <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input name="confirmPassword" type="password" class="form-control" id="confirmPassword" required>
                  </div>
                  <div id="error-message" class="text-danger"></div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer d-flex justify-content-end">
                  <button type="button" class="btn btn-dark mr-2" onclick="location.href='view-staff.php?userID=<?php echo $user['userID']; ?>'">Back</button>
                  <button type="submit" class="btn btn-primary" name="submit">Save Updated Details</button>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
          <!--/.col (left) -->
          <!-- right column -->
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- bs-custom-file-input -->
<script src="../../plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- Page specific script -->
<script>
function validateForm() {
  var password = document.getElementById("password").value;
  var confirmPassword = document.getElementById("confirmPassword").value;
  var errorMessage = document.getElementById("error-message");

  if (password !== confirmPassword) {
    errorMessage.textContent = "Passwords do not match.";
    return false;
  }

  return true;
}
</script>
<script>
  $(function () {
  bsCustomFileInput.init();
});
</script>
</body>
</html>
