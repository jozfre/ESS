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
  <!-- fullCalendar -->
  <link rel="stylesheet" href="../../plugins/fullcalendar/main.css">
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
            <a href="view-schedule.php" class="nav-link active">
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
            <h1 class="m-0">Schedule</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">View Schedule</li>
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
          <!-- /.col -->
          <div class="col-12">
            <div class="card card-primary">
              <div class="card-body p-0">
                <!-- THE CALENDAR -->
                <div id="calendar"></div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- jQuery UI -->
<script src="../../plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- fullCalendar 2.2.5 -->
<script src="../../plugins/moment/moment.min.js"></script>
<script src="../../plugins/fullcalendar/main.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      themeSystem: 'bootstrap',
      events: [
        {
          title: 'Kuliah Ustaz',
          start: '2025-01-01',
          backgroundColor: '#f56954',
          borderColor: '#f56954',
          allDay: true
        },
        {
          title: 'Kursus Haji',
          start: '2025-01-07',
          end: '2025-01-10',
          backgroundColor: '#f39c12',
          borderColor: '#f39c12'
        },
        {
          title: 'Mesyuarat Masjid',
          start: '2025-01-11',
          end: '2025-01-13',
          backgroundColor: '#0073b7',
          borderColor: '#0073b7'
        }
      ]
    });

    calendar.render();
  });
</script>
</body>
</html>
