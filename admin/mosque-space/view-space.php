<?php
session_start();
if (!isset($_SESSION['userlogged']) || ($_SESSION['userlogged'] != 1)) {
  header("Location: ../../index.php");
}

if (!isset($_SESSION['userID'])) {
  header("Location: ../../php/logout.php");
}

include "../../php/dbconn.php";

if (isset($_GET['spaceID'])) {
  $spaceID = mysqli_real_escape_string($conn, $_GET['spaceID']);
  $sql = "SELECT * FROM space WHERE spaceID='$spaceID'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    $space = mysqli_fetch_assoc($result);
  } else {
    die(mysqli_error($conn));
  }
}

// Assuming $space array already contains the row from database
if (!empty($space['spacePicture'])) {
  $imageData = $space['spacePicture'];
  $imgSrc = "data:image/jpeg;base64," . $imageData;
} else {
  $imgSrc = "../../images/logo-mbtho.png"; // fallback image
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
            <a href="#" class="d-block text-truncate"><?php if (isset($_SESSION['name'])) {
                                                        echo $_SESSION['name'];
                                                      } ?></a>
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
              <a href="list-space.php" class="nav-link active">
                <i class="nav-icon fas fa-mosque"></i>
                <p>
                  Mosque Spaces
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../staff/list-staff.php" class="nav-link">
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
              <h1 class="m-0">Mosque Spaces</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="list-space.php">List of Space</a></li>
                <li class="breadcrumb-item active">View Space Details</li>
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
              <div class="card card-dark">
                <div class="card-header">
                  <h3 class="card-title">Space Details</h3>
                  <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-danger btn-sm float-right mr-2" data-toggle="modal" data-target="#deleteModal"><i class="fas fa-trash-alt"></i> Delete Space</button>
                    <button type="button" class="btn btn-primary btn-sm float-right" onclick="location.href='update-space.php?spaceID=<?php echo $space['spaceID']; ?>'"><i class="fas fa-edit"></i> Update Details</button>
                  </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form>
                  <div class="card-body">
                    <div class="form-group">
                      <label for="spaceID">Space ID</label>
                      <input name="spaceID" class="form-control" id="spaceID" value="<?= $space['spaceID']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="spaceName">Space Name</label>
                      <input name="spaceName" class="form-control" id="spaceName" value="<?= $space['spaceName']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="spaceCapacity">Space Capacity (People)</label>
                      <input name="spaceCapacity" class="form-control" id="spaceCapacity" value="<?= $space['spaceCapacity']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="spaceType">Space Type</label>
                      <input name="spaceType" class="form-control" id="spaceType" value="<?= $space['spaceType']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="spaceImage">Image of Space</label>
                      <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" style="background-color: rgba(0, 0, 0, 0.8); border: 2px solid #ccc; border-radius: 10px;">
                        <div class="carousel-inner text-center">
                          <div class="carousel-item active">
                            <img src="<?php echo $imgSrc; ?>" class="d-block mx-auto" alt="Space Image" style="max-height: 500px;">
                          </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-target="#carouselExampleControls" data-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="sr-only">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-target="#carouselExampleControls" data-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="sr-only">Next</span>
                        </button>
                      </div>
                    </div>
                  </div>
                  <!-- /.card-body -->
                  <div class="card-footer d-flex justify-content-between align-items-center">
                    <bon type="button" class="btn btn-dark" onclick="location.href='list-space.php'">Back</button>
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

  <!-- Deletion Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Delete Confirmation</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="../../php/delete-space.php" method="POST">
          <div class="modal-body">
            <input type="hidden" name="spaceID" value="<?php echo $space['spaceID']; ?>">
            <p>Are you sure you want to delete this space?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" name="deleteSpace" class="btn btn-danger">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- End of Deletion Modal -->

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
    $(function() {
      bsCustomFileInput.init();
    });
  </script>
</body>

</html>