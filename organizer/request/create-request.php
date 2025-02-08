<?php
session_start();
if (!isset($_SESSION['orglogged']) || ($_SESSION['orglogged'] != 1)) {
    header("Location: ../../index.php");
}

if (!isset($_SESSION['orgID'])) {
    header("Location: ../../php/logout.php");
}

include "../../php/dbconn.php";

function checkRequestConflicts($conn, $dateOfUse, $periodOfUseStart, $periodOfUseEnd, $spaceID) {
    $conflicts = array();
    
    // Check for existing events in the same space and date/time
    $sql = "SELECT eventName, eventDate, eventTimeStart, eventTimeEnd, spaceName 
            FROM event e 
            LEFT JOIN space s ON e.spaceID = s.spaceID 
            WHERE e.isDeleted = 0 
            AND e.spaceID = ? 
            AND e.eventDate = ? 
            AND ((e.eventTimeStart BETWEEN ? AND ?) 
            OR (e.eventTimeEnd BETWEEN ? AND ?) 
            OR (? BETWEEN e.eventTimeStart AND e.eventTimeEnd)
            OR (? BETWEEN e.eventTimeStart AND e.eventTimeEnd))";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", 
        $spaceID, 
        $dateOfUse, 
        $periodOfUseStart, 
        $periodOfUseEnd,
        $periodOfUseStart, 
        $periodOfUseEnd,
        $periodOfUseStart,
        $periodOfUseEnd
    );
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $conflictEvent = $result->fetch_assoc();
        $conflicts[] = array(
            'type' => 'reserve',
            'message' => "Space Conflict: '{$conflictEvent['spaceName']}' is already scheduled for event '{$conflictEvent['eventName']}' on " . 
                        date('d-m-Y', strtotime($conflictEvent['eventDate'])) . 
                        " from {$conflictEvent['eventTimeStart']} to {$conflictEvent['eventTimeEnd']}"
        );
    }
    
    // Check if end time is after start time
    if (strtotime($periodOfUseEnd) <= strtotime($periodOfUseStart)) {
        $conflicts[] = array(
            'type' => 'time',
            'message' => "Time Conflict: End time must be after start time"
        );
    }
    
    // Check if date is not in the past
    if (strtotime($dateOfUse) < strtotime(date('Y-m-d'))) {
        $conflicts[] = array(
            'type' => 'date',
            'message' => "Date Conflict: Cannot create requests for past dates"
        );
    }
    
    $stmt->close();
    return $conflicts;
}

// SQL query to get all space
$sqlSpace = "SELECT * FROM space where isDeleted = 0";
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

if(isset($_POST['submit'])) {
    // Get form data
    $orgID = $_SESSION['orgID'];
    $dateOfRequest = date('Y-m-d');
    $applicantAddress = mysqli_real_escape_string($conn, trim($_POST['applicantAddress']));
    $reqEventName = mysqli_real_escape_string($conn, trim($_POST['reqEventName']));
    $reqEventType = mysqli_real_escape_string($conn, trim($_POST['reqEventType']));
    $dateOfUse = mysqli_real_escape_string($conn, $_POST['dateOfUse']);
    $periodOfUseStart = mysqli_real_escape_string($conn, $_POST['periodOfUseStart']);
    $periodOfUseEnd = mysqli_real_escape_string($conn, $_POST['periodOfUseEnd']);
    $purposeOfUse = mysqli_real_escape_string($conn, trim($_POST['purposeOfUse']));
    $applicantSignature = mysqli_real_escape_string($conn, trim($_POST['applicantSignature']));
    $spaceID = mysqli_real_escape_string($conn, $_POST['spaceID']);
    
    // Set defaults
    $approvalStatus = NULL;
    $approverDetails = NULL;
    $isDeleted = 0;

    // Check for conflicts
    $conflicts = checkRequestConflicts($conn, $dateOfUse, $periodOfUseStart, $periodOfUseEnd, $spaceID);

    if (empty($conflicts)) {
        $sql = "INSERT INTO request (dateOfRequest, applicantAddress, reqEventName, 
            reqEventType, dateOfUse, periodOfUseStart, periodOfUseEnd, 
            purposeOfUse, applicantSignature, approvalStatus, approverDetails, 
            isDeleted, orgID, spaceID) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssiiiii", 
            $dateOfRequest,
            $applicantAddress, 
            $reqEventName,
            $reqEventType,
            $dateOfUse,
            $periodOfUseStart,
            $periodOfUseEnd,
            $purposeOfUse,
            $applicantSignature,
            $approvalStatus,
            $approverDetails,
            $isDeleted,
            $orgID,
            $spaceID
        );

        if($stmt->execute()) {
            $requestID = $stmt->insert_id; // Get the newly inserted request ID
            $_SESSION['success'] = "Request created successfully";
            header("Location: view-request.php?requestID=" . $requestID);
            exit();
        } else {
            $error = "Error creating request: " . $conn->error;
        }
        $stmt->close();
    } else {
        // Store form data in session for redisplay
        $_SESSION['form_data'] = $_POST;
        // Get first conflict message
        $error = $conflicts[0]['message'];
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
                                <li class="breadcrumb-item"><a href="list-request.php">List of Requests</a></li>
                                <li class="breadcrumb-item active">Create New Event Request</li>
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
                              <!-- Add after form starts -->
                              <?php if (isset($error)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                            <?php echo $error; ?>
                                        </div>
                                    <?php endif; ?>
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">New Event Request Details Form</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form name="form" method="POST" action="create-request.php" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="dateOfRequest">Date of Request</label>
                                            <input name="dateOfRequest" type="date" class="form-control" id="dateOfRequest" value="<?php echo date('Y-m-d'); ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="orgName">Organizer Name</label>
                                            <input name="orgName" type="text" class="form-control" id="orgName" value="<?php echo $_SESSION['orgName'] ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="orgTelNum">Organizer Telephone Number</label>
                                            <input name="orgTelNum" type="text" class="form-control" id="orgTelNum" pattern="[0-9]{10}" value="<?php echo $_SESSION['orgTelNum'] ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="applicantAddress">Organizer Address</label>
                                            <input name="applicantAddress" type="text" class="form-control" id="applicantAddress" placeholder="Enter your correspondence address" title="Please enter your correspondence address" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="reqEventName">Request Event Name</label>
                                            <input name="reqEventName" type="text" class="form-control" id="reqEventName" placeholder="Enter the request event name" title="Please enter the new request event name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="reqEventType">Event Type</label>
                                            <select name="reqEventType" id="reqEventType" class="form-control" placeholder="Choose Event Type" required>
                                                <option value="Islamic Talks">Islamic Talks</option>
                                                <option value="Nikah/Wedding">Nikah/Wedding</option>
                                                <option value="Class">Class</option>
                                                <option value="Others">Others</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="dateOfUse">Date of Use</label>
                                            <input name="dateOfUse" type="date" class="form-control" id="dateOfUse" placeholder="Choose the date of use for the event" title="Please choose the date of use for the event" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="periodOfUse">Period of Use:</label><br>
                                            <label for="periodOfUseStart">Start Time</label>
                                            <input name="periodOfUseStart" type="time" class="form-control" id="periodOfUseStart" placeholder="Please enter the period of use (start time) for the event" title="Please enter the period of use (start time) for the event" required><br>
                                            <label for="periodOfUseEnd">End Time</label>
                                            <input name="periodOfUseEnd" type="time" class="form-control" id="periodOfUseEnd" placeholder="Please enter the period of use (end time) for the event" title="Please enter the period of use (end time) for the event" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="purposeOfUse">Purpose of Use</label>
                                            <input name="purposeOfUse" type="text" class="form-control" id="purposeOfUse" placeholder="Enter the purpose of the event" title="Please enter the purpose of the event" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="spaceID">Choose Mosque Space</label>
                                            <?php if ($rowSpace > 0) { ?>
                                                <select name="spaceID" id="spaceID" class="form-control" onchange="updateSpaceImage(this.value)" required>
                                                    <?php foreach ($spaces as $spaceID => $space) { ?>
                                                        <option value="<?php echo $spaceID; ?>"><?php echo $space['spaceName']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php } else { ?>
                                                <input name="spaceID" type="text" class="form-control" placeholder="No mosque space available" readonly>
                                            <?php } ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="spaceImage">Mosque Space Image</label>
                                            <div id="spaceImageContainer" class="text-center" style="background-color: rgba(0, 0, 0, 0.8); border: 2px solid #ccc; border-radius: 10px;">
                                                <img id="spaceImage" src="<?php echo !empty($firstSpace['spacePicture']) ? 'data:image/jpeg;base64,' . $firstSpace['spacePicture'] : '../../images/logo-mbtho.png'; ?>" class="d-block mx-auto" style="max-height: 500px; width: auto;" alt="Space Image">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="applicantSignature">Applicant Signature</label>
                                            <input name="applicantSignature" type="text" class="form-control" id="applicantSignature" placeholder="Enter the your full name for digital signature" title="Please enter your full name for digital signature" required>
                                        </div>

                                        <!-- /.card-body -->

                                        <div class="card-footer d-flex justify-content-end">
                                            <button type="button" class="btn btn-secondary mr-2" onclick="location.href='list-request.php'">Back</button>
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
        const spaces = <?php echo json_encode($spaces); ?>;

        // Load first space image on page load
        window.onload = function() {
            const firstSpaceID = document.getElementById('spaceID').value;
            updateSpaceImage(firstSpaceID);
        }

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