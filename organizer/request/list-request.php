<?php
session_start();
if (!isset($_SESSION['orglogged']) || ($_SESSION['orglogged'] != 1)) {
  header("Location: ../../index.php");
}

if (!isset($_SESSION['orgID'])) {
  header("Location: ../../php/logout.php");
}

include "../../php/dbconn.php";

//SQL Query to get all list of requests
$sql = "SELECT r.*, o.orgName FROM request r JOIN organizer o ON r.orgID = o.orgID WHERE isDeleted = 0 AND r.orgID = {$_SESSION['orgID']}";
$result = mysqli_query($conn, $sql);
$row = mysqli_num_rows($result);

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
            <a href="#" class="d-block text-truncate"><?php if (isset($_SESSION['orgName'])) {
                                                        echo $_SESSION['orgName'];
                                                      } ?></a>
            <a href="#" class="d-block">ORGANIZER</a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2 flex-grow-1">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item">
              <a href="../schedule/view-schedule.php" class="nav-link">
                <i class="nav-icon fas fa-calendar-alt"></i>
                <p>
                  Schedule
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="list-request.php" class="nav-link active">
                <i class="nav-icon fas fa-file-alt"></i>
                <p>
                  Request
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
              <h1 class="m-0">Event Requests</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">List of Requests</li>
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
                  <h3 class="card-title">List of Requests</h3>
                  <div class="d-flex justify-content-end">
                    <a href="create-request.php" class="btn btn-primary btn-sm float-right text-white">
                      <i class="fas fa-plus"></i> Create New Request
                    </a>
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="d-flex justify-content-center mb-3">
                    <div class="btn-group" role="group" aria-label="Approval Status Filter">
                      <button type="button" class="btn btn-secondary filter-btn active" data-filter="All">All</button>
                      <button type="button" class="btn btn-secondary filter-btn" data-filter="Pending">Pending</button>
                      <button type="button" class="btn btn-secondary filter-btn" data-filter="Approved">Approved</button>
                      <button type="button" class="btn btn-secondary filter-btn" data-filter="Not Approved">Not Approved</button>
                    </div>
                  </div>
                  <table id="example2" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Date of Request</th>
                        <th>Event Name</th>
                        <th>Event Type</th>
                        <th>Approval Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody id="requestTableBody">
                      <?php
                      function displayTableRows($result)
                      {
                        while ($request = mysqli_fetch_assoc($result)) {
                          $badgeClass = '';
                          $statusText = 'All';

                          if ($request['approvalStatus'] === NULL) {
                            $badgeClass = 'badge-secondary';
                            $statusText = 'To Be Reviewed';
                          } else if ($request['approvalStatus'] == 1) {
                            $badgeClass = 'badge-success';
                            $statusText = 'Approved';
                          } else if ($request['approvalStatus'] == 0) {
                            $badgeClass = 'badge-danger';
                            $statusText = 'Not Approved';
                          }
                      ?>
                          <tr>
                            <td><?php echo $request['dateOfRequest']; ?></td>
                            <td><?php echo $request['reqEventName']; ?></td>
                            <td><?php echo $request['reqEventType']; ?></td>
                            <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $statusText; ?></span></td>
                            <td>
                              <a href="view-request.php?requestID=<?php echo $request['requestID']; ?>" class="btn btn-info btnn-block btn-sm float-middle fas fa-eye"></a>
                            </td>
                          </tr>
                      <?php
                        }
                      }

                      // Initial display with all results
                      if ($row > 0) {
                        displayTableRows($result);
                      }
                      ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Date of Request</th>
                        <th>Event Name</th>
                        <th>Event Type</th>
                        <th>Approval Status</th>
                        <th>Action</th>
                      </tr>
                    </tfoot>
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
    $(function() {
      $("#example1").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
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
  <script>
    $(document).ready(function() {
      $('.filter-btn').click(function() {
        // Remove active class from all buttons and add to clicked button
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        // Get the filter value
        var filter = $(this).data('filter');

        // Clear existing table content
        $('#requestTableBody').empty();

        // Load filtered content dynamically
        $.ajax({
          url: 'fetch_requests.php', // Create a separate PHP file for fetching filtered data
          type: 'POST',
          data: {
            filter: filter,
            orgID: <?php echo $_SESSION['orgID']; ?> // Add orgID
          },
          success: function(response) {
            $('#requestTableBody').html(response);
          }
        });
      });
    });
  </script>
</body>

</html>