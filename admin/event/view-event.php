<?php
session_start();
if (!isset($_SESSION['userlogged']) || ($_SESSION['userlogged'] != 1)) {
  header("Location: ../../index.php");
}

if (!isset($_SESSION['userID'])) {
  header("Location: ../../php/logout.php");
}

include "../../php/dbconn.php";

if (isset($_GET['eventID'])) {
  $eventID = mysqli_real_escape_string($conn, $_GET['eventID']);

  $sql = "SELECT e.*, o.orgName, o.orgTelNum, s.spaceName, s.spacePicture, 
          u.name as staffName, u.telNum as staffTelNum
          FROM event e
          LEFT JOIN organizer o ON e.orgID = o.orgID
          LEFT JOIN space s ON e.spaceID = s.spaceID
          LEFT JOIN user u ON e.assignedStaff = u.userID
          WHERE e.eventID = ? AND e.isDeleted = 0";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $eventID);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $event = $result->fetch_assoc();
  } else {
    die("Event not found");
  }
  $stmt->close();
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
              <a href="list-event.php" class="nav-link active">
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
              <h1 class="m-0">Events</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="list-event.php">List of Events</a></li>
                <li class="breadcrumb-item active">View Event Details</li>
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
              <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" id="successAlert">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; ?>
                </div>
              <?php unset($_SESSION['success']);
              endif; ?>
              <div class="card card-dark">
                <div class="card-header">
                  <h3 class="card-title">Event Details</h3>
                  <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-danger btn-sm float-right mr-2" data-toggle="modal" data-target="#deleteModal"><i class="fas fa-trash-alt"></i> Delete Event</button>
                    <button type="button" class="btn btn-primary btn-sm float-right" onclick="location.href='update-event.php?eventID=<?php echo $event['eventID']; ?>'"><i class="fas fa-edit"></i> Update Details</button>
                  </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form>
                  <div class="card-body">
                    <div class="form-group">
                      <label for="eventID">Event ID</label>
                      <input name="eventID" class="form-control" id="eventID" value="<?php echo $event['eventID']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="orgName">Organizer Name</label>
                      <input name="orgName" type="text" class="form-control" id="orgName"
                        value="<?php echo ($event['orgID']) ? $event['orgName'] : 'Anjuran MBTHO'; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="orgTelNum">Organizer Telephone Number</label>
                      <input name="orgTelNum" type="text" class="form-control" id="orgTelNum" pattern="[0-9]{10}"
                        value="<?php echo ($event['orgID']) ? $event['orgTelNum'] : '01111467006'; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="eventName">Event Name</label>
                      <input name="eventName" type="text" class="form-control" id="eventName" placeholder="Enter the new event name" title="Please enter the new event name" value="<?php echo $event['eventName']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="eventType">Event Type</label>
                      <input name="eventType" type="text" class="form-control" id="eventType" placeholder="Enter the new event type" title="Please enter the new event type" value="<?php echo $event['eventType']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="eventDate">Date</label>
                      <input name="eventDate" type="date" class="form-control" id="eventDate" placeholder="Enter the new event date" title="Please enter the new event date" value="<?php echo $event['eventDate']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="eventTime">Event Time:</label><br>
                      <label for="eventStartTime">Start Time</label>
                      <input name="eventStartTime" type="time" class="form-control" id="eventStartTime" placeholder="Please enter the start time for the event" title="Please enter the start time for the event" value="<?php echo $event['eventTimeStart']; ?>" readonly><br>
                      <label for="eventEndTime">End Time</label>
                      <input name="eventEndTime" type="time" class="form-control" id="eventEndTime" placeholder="Please enter the end time for the event" title="Please enter the end time for the event" value="<?php echo $event['eventTimeEnd']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="eventDescription">Event Description</label>
                      <textarea name="eventDescription" class="form-control" id="eventDescription" rows="3" placeholder="Enter the new event description" title="Please enter the new event description" style="resize: none" readonly><?php echo $event['eventDescription']; ?></textarea>
                    </div>
                    <div class="form-group">
                      <label for="eventSpace">Mosque Space</label>
                      <input name="eventSpace" type="text" class="form-control" id="eventSpace" value="<?php echo $event['spaceName']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="spaceImage">Mosque Space Image</label>
                      <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" style="background-color: rgba(0, 0, 0, 0.8); border: 2px solid #ccc; border-radius: 10px;">
                        <div class="carousel-inner text-center">
                          <div class="carousel-item active">
                            <img src="<?php echo !empty($event['spacePicture']) ?
                                        'data:image/jpeg;base64,' . $event['spacePicture'] :
                                        '../../images/logo-mbtho.png'; ?>"
                              class="img-fluid" style="max-height: 400px;">
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
                    </div><br>
                    <div class="form-group">
                      <label for="assignedStaff">Assigned Staff Details</label><br>
                      <label for="staffName">Staff Name</label>
                      <input name="staffName" type="text" class="form-control" id="staffName"
                        value="<?php echo $event['staffName']; ?>" readonly><br>
                      <label for="staffContact">Staff Contact</label>
                      <input name="staffContact" type="text" class="form-control" id="staffContact"
                        value="<?php echo $event['staffTelNum']; ?>" readonly>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer d-flex justify-content-between align-items-center">
                      <bon type="button" class="btn btn-dark" onclick="location.href='list-event.php'">Back</button>
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
  <script>
    $(document).ready(function() {
      // Auto dismiss alert after 3 seconds
      if ($('#successAlert').length > 0) {
        setTimeout(function() {
          $("#successAlert").fadeOut('slow');
        }, 3000);
      }
    });
  </script>
 <!-- Add Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Event</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="../../php/delete-event.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="eventID" value="<?php echo $event['eventID']; ?>">
                    <p>Are you sure you want to delete this event?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="deleteEvent" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>

</html>