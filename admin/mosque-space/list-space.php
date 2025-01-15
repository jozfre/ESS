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
          <a href="#" class="d-block">Johan Zafri</a>
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
              <li class="breadcrumb-item active">List of Spaces</li>
            </ol>
          </div>
          <!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">List of Spaces</h3>
                <div class="d-flex justify-content-end">
                    <a href="create-space.php" class="btn btn-primary btn-sm float-right text-white">
                    <i class="fas fa-plus"></i> Add New Space
                    </a>
                </div>
            </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Space ID</th>
                    <th>Space Name</th>
                    <th>Space Capacity (People)</th>
                    <th>Space Type</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td>0001</td>
                    <td>Dewan Singgah Seri</td>
                    <td>1000</td>
                    <td>Prayer Hall</td>
                    <td>
                    <a href="view-space.php?token=<?php echo $user['token']; ?>" class="btn btn-info btnn-block btn-sm float-middle fas fa-eye"></a>
                    </td>
                  </tr>
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>Space ID</th>
                    <th>Space Name</th>
                    <th>Space Capacity (People)</th>
                    <th>Space Type</th>
                    <th>Action</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
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
