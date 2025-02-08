<?php
session_start();
if (!isset($_SESSION['userlogged']) || ($_SESSION['userlogged'] != 1)) {
  header("Location: ../../index.php");
}

include "../../php/dbconn.php";

// Initialize event data with defaults at the top of file
$event = array(
    'eventID' => null,
    'orgID' => null,
    'orgName' => 'Anjuran MBTHO',
    'orgTelNum' => '01111467006',
    'eventName' => '',
    'eventType' => '',
    'eventDate' => '',
    'eventTimeStart' => '',
    'eventTimeEnd' => '',
    'eventDescription' => '',
    'spaceID' => '',
    'assignedStaff' => '',
    'spaceName' => '',
    'spacePicture' => ''
);

// Get spaces
$sqlSpace = "SELECT * FROM space WHERE isDeleted = 0";
$resultSpace = mysqli_query($conn, $sqlSpace);
$rowSpace = mysqli_num_rows($resultSpace);

$spaces = array();
while ($row = mysqli_fetch_assoc($resultSpace)) {
  $spaces[$row['spaceID']] = array(
    'spaceName' => $row['spaceName'],
    'spacePicture' => $row['spacePicture']
  );
}
mysqli_data_seek($resultSpace, 0); // Reset result pointer

// Get staff
$sqlStaff = "SELECT * FROM user WHERE isDeleted = 0";
$resultStaff = mysqli_query($conn, $sqlStaff);

// Get event details
if (isset($_GET['eventID'])) {
    $eventID = mysqli_real_escape_string($conn, $_GET['eventID']);
    
    $sql = "SELECT e.*, 
            COALESCE(o.orgName, 'Anjuran MBTHO') as orgName,
            COALESCE(o.orgTelNum, '01111467006') as orgTelNum,
            s.spaceName, s.spacePicture, 
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
        $eventData = $result->fetch_assoc();
        // Merge with defaults, preserving default values for missing keys
        $event = array_merge($event, $eventData);
    } else {
        $_SESSION['error'] = "Event not found";
        header("Location: list-event.php");
        exit();
    }
    $stmt->close();
}

// Handle form data if validation failed
if (isset($_SESSION['form_data'])) {
    $formData = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
    // Merge while preserving original event data for unchanged fields
    foreach ($formData as $key => $value) {
        if (!empty($value) || $key == 'orgID') { // Special handling for orgID
            $event[$key] = $value;
        }
    }
}

function checkUpdateConflicts($conn, $eventID, $eventDate, $eventTimeStart, $eventTimeEnd, $spaceID)
{
  $conflicts = array();

  // Check conflicts excluding current event
  $sql = "SELECT eventName, eventDate, eventTimeStart, eventTimeEnd, spaceName 
            FROM event e 
            LEFT JOIN space s ON e.spaceID = s.spaceID 
            WHERE e.isDeleted = 0 
            AND e.eventID != ? 
            AND e.spaceID = ? 
            AND e.eventDate = ? 
            AND ((e.eventTimeStart BETWEEN ? AND ?) 
            OR (e.eventTimeEnd BETWEEN ? AND ?) 
            OR (? BETWEEN e.eventTimeStart AND e.eventTimeEnd)
            OR (? BETWEEN e.eventTimeStart AND e.eventTimeEnd))";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    "iisssssss",
    $eventID,
    $spaceID,
    $eventDate,
    $eventTimeStart,
    $eventTimeEnd,
    $eventTimeStart,
    $eventTimeEnd,
    $eventTimeStart,
    $eventTimeEnd
  );

  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $conflictEvent = $result->fetch_assoc();
    $conflicts[] = array(
      'type' => 'reserve',
      'message' => "Space Conflict: Space is already scheduled for '{$conflictEvent['eventName']}' on " .
        date('d-m-Y', strtotime($conflictEvent['eventDate'])) .
        " ({$conflictEvent['eventTimeStart']} - {$conflictEvent['eventTimeEnd']})"
    );
  }

  if (strtotime($eventTimeEnd) <= strtotime($eventTimeStart)) {
    $conflicts[] = array(
      'type' => 'time',
      'message' => "Time Conflict: End time must be after start time"
    );
  }

  if (strtotime($eventDate) < strtotime(date('Y-m-d'))) {
    $conflicts[] = array(
      'type' => 'date',
      'message' => "Date Conflict: Cannot schedule events in the past"
    );
  }

  $stmt->close();
  return $conflicts;
}

// Handle form submission
if (isset($_POST['submit'])) {
  $_SESSION['form_data'] = $_POST;

  $eventID = mysqli_real_escape_string($conn, $_POST['eventID']);
  $orgName = mysqli_real_escape_string($conn, $_POST['orgName']);
  $orgTelNum = mysqli_real_escape_string($conn, $_POST['orgTelNum']);
  $eventName = mysqli_real_escape_string($conn, $_POST['eventName']);
  $eventType = mysqli_real_escape_string($conn, $_POST['eventType']);
  $eventDate = mysqli_real_escape_string($conn, $_POST['eventDate']);
  $eventTimeStart = mysqli_real_escape_string($conn, $_POST['eventTimeStart']);
  $eventTimeEnd = mysqli_real_escape_string($conn, $_POST['eventTimeEnd']);
  $eventDescription = mysqli_real_escape_string($conn, $_POST['eventDescription']);
  $spaceID = mysqli_real_escape_string($conn, $_POST['spaceID']);
  $assignedStaff = mysqli_real_escape_string($conn, $_POST['assignedStaff']);

  // Check for conflicts
  $conflicts = checkUpdateConflicts($conn, $eventID, $eventDate, $eventTimeStart, $eventTimeEnd, $spaceID);

  if (empty($conflicts)) {
    $sql = "UPDATE event SET 
                eventName = ?,
                eventType = ?,
                eventDate = ?,
                eventTimeStart = ?,
                eventTimeEnd = ?,
                eventDescription = ?,
                spaceID = ?,
                assignedStaff = ?
                WHERE eventID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
      "ssssssiis",
      $eventName,
      $eventType,
      $eventDate,
      $eventTimeStart,
      $eventTimeEnd,
      $eventDescription,
      $spaceID,
      $assignedStaff,
      $eventID
    );

    if ($stmt->execute()) {
      $_SESSION['success'] = "Event updated successfully";
      header("Location: view-event.php?eventID=" . $eventID);
      exit();
    } else {
      $error = "Error updating event: " . $conn->error;
    }
    $stmt->close();
  } else {
    $error = $conflicts[0]['message'];
    // Keep form data for redisplay
    $event = $_POST;
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
                <li class="breadcrumb-item"><a href="view-event.php?eventID=<?php echo $event['eventID']; ?>">View Event Details</a></li>
                <li class="breadcrumb-item active">Update Event Details</li>
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
              <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <?php echo $error; ?>
                </div>
              <?php endif; ?>
              <div class="card card-info">
                <div class="card-header">
                  <h3 class="card-title">Update Event Details</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form name="form" method="POST" action="update-event.php" enctype="multipart/form-data">
                  <div class="card-body">
                    <div class="form-group">
                      <label for="eventID">Event ID</label>
                      <input name="eventID" class="form-control" id="eventID" value="<?php echo $event['eventID']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="orgName">Organizer Name</label>
                        <input name="orgName" type="text" class="form-control" id="orgName" value="<?php echo $event['orgID'] === NULL ? 'Anjuran MBTHO' : htmlspecialchars($event['orgName']); ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="orgTelNum">Organizer Telephone Number</label>
                      <input name="orgTelNum" type="text" class="form-control" id="orgTelNum" value="<?php echo $event['orgID'] === NULL ? '01111467006' : htmlspecialchars($event['orgTelNum']); ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="eventName">Event Name</label>
                      <input name="eventName" type="text" class="form-control" id="eventName" placeholder="Enter the new event name" title="Please enter the new event name" value="<?php echo $event['eventName']; ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="eventType">Event Type</label>
                      <select name="eventType" class="form-control" required>
                        <option value="Islamic Talks" <?php echo ($event['eventType'] == 'Islamic Talks') ? 'selected' : ''; ?>>Islamic Talks</option>
                        <option value="Nikah/Wedding" <?php echo ($event['eventType'] == 'Nikah/Wedding') ? 'selected' : ''; ?>>Nikah/Wedding</option>
                        <option value="Class" <?php echo ($event['eventType'] == 'Class') ? 'selected' : ''; ?>>Class</option>
                        <option value="Others" <?php echo ($event['eventType'] == 'Others') ? 'selected' : ''; ?>>Others</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="eventDate">Date</label>
                      <input name="eventDate" type="date" class="form-control" id="eventDate" placeholder="Choose the date for the event" title="Please choose the date for the event" value="<?php echo $event['eventDate']; ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="eventTime">Event Time:</label><br>
                      <label for="eventTimeStart">Start Time</label>
                      <input name="eventTimeStart" type="time" class="form-control" id="eventTimeStart" placeholder="Please enter the start time for the event" title="Please enter the start time for the event" value="<?php echo $event['eventTimeStart']; ?>" required><br>
                      <label for="eventEndTime">End Time</label>
                      <input name="eventTimeEnd" type="time" class="form-control" id="eventTimeEnd" placeholder="Please enter the end time for the event" title="Please enter the end time for the event" value="<?php echo $event['eventTimeEnd']; ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="eventDescription">Event Description</label>
                      <input name="eventDescription" type="text" class="form-control" id="eventDescription" placeholder="Enter the description of the event" title="Please enter the description of the event" value="<?php echo $event['eventDescription']; ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="reqEventFacility">Choose Mosque Space</label>
                      <?php if ($rowSpace > 0) { ?>
                        <select name="spaceID" id="reqEventFacility" class="form-control" onchange="updateSpaceImage(this.value)" required>
                          <?php foreach ($spaces as $spaceID => $space) { ?>
                            <option value="<?php echo $spaceID; ?>" <?php echo ($event['spaceID'] == $spaceID) ? 'selected' : ''; ?>>
                              <?php echo $space['spaceName']; ?>
                            </option>
                          <?php } ?>
                        </select>
                      <?php } ?>
                    </div>
                    <div class="form-group">
                      <label for="spaceImage">Mosque Space Image</label>
                      <div id="spaceImageContainer" class="text-center" style="background-color: rgba(0, 0, 0, 0.8); border: 2px solid #ccc; border-radius: 10px;">
                        <img id="spaceImage" src="<?php
                                                  echo !empty($spaces[$event['spaceID']]['spacePicture']) ?
                                                    'data:image/jpeg;base64,' . $spaces[$event['spaceID']]['spacePicture'] :
                                                    '../../images/logo-mbtho.png';
                                                  ?>" class="d-block mx-auto" style="max-height: 500px; width: auto;" alt="Space Image">
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Assigned Staff</label>
                      <select name="assignedStaff" class="form-control" required>
                        <?php while ($staff = mysqli_fetch_assoc($resultStaff)) { ?>
                          <option value="<?php echo $staff['userID']; ?>"
                            <?php echo ($event['assignedStaff'] == $staff['userID']) ? 'selected' : ''; ?>>
                            <?php echo $staff['name']; ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <!-- /.card-body -->

                  <div class="card-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary mr-2" onclick="location.href='view-event.php?eventID=<?php echo $event['eventID']; ?>'">Back</button>
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
    const spaces = <?php echo json_encode($spaces); ?>;

    function updateSpaceImage(spaceID) {
      const imageElement = document.getElementById('spaceImage');
      if (spaceID && spaces[spaceID] && spaces[spaceID].spacePicture) {
        imageElement.src = 'data:image/jpeg;base64,' + spaces[spaceID].spacePicture;
      } else {
        imageElement.src = '../../images/logo-mbtho.png';
      }
    }
  </script>
  <script>
    $(function() {
      bsCustomFileInput.init();
    });
  </script>
</body>

</html>