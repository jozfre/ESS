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
              <li class="breadcrumb-item active">Add New Space</li>
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
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">New Space Details Form</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form name="form" method="POST" action="create-space.php" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="form-group">
                    <label for="spaceName">Space Name</label>
                    <input name="spaceName" type="text" class="form-control" id="spaceName" placeholder="Enter the new space name" title="Please enter the new space name" required>
                  </div>
                  <div class="form-group">
                    <label for="spaceCapacity">Space Capacity (People)</label>
                    <input name="spaceCapacity" type="number" class="form-control" id="spaceCapacity" min="0" placeholder="Enter the new space capacity" title="Please enter the space capacity" required>
                  </div>
                  <div class="form-group">
                    <label for="spaceType">Space Type</label>
                    <select name="spaceType" id="spaceType" class="form-control" placeholder="Choose Space Type" required>
                      <option value="Prayer Hall">Prayer Hall</option>
                      <option value="Closed Hall">Closed Hall</option>
                      <option value="Meeting Room<">Meeting Room</option>
                      <option value="Office">Office</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="spaceImage">Insert Space Image File</label>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="spaceImage">
                      <label class="custom-file-label" for="spaceImage" aria-describedby="inputGroupFile">Choose file (.jpg/.jpeg/.png)</label>
                    </div>
                  </div>
                <!-- /.card-body -->

                <div class="card-footer d-flex justify-content-end">
                  <button type="button" class="btn btn-secondary mr-2" onclick="location.href='list-space.php'">Back</button>
                  <button type="submit" class="btn btn-primary" name="submit">Submit</button>
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
<!-- DataTables  & Plugins -->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../../plugins/jszip/jszip.min.js"></script>
<script src="../../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
</body>
</html>
