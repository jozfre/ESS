<?php
session_start();
if (!isset($_SESSION['orglogged']) || ($_SESSION['orglogged'] != 1)) {
    header("Location: ../../index.php");
}

if (!isset($_SESSION['orgID'])) {
    header("Location: ../../php/logout.php");
}

include "../../php/dbconn.php";

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

if (isset($_GET['requestID'])) {
    $requestID = mysqli_real_escape_string($conn, $_GET['requestID']);
    $sql = "SELECT r.*, s.spaceName, s.spacePicture, o.orgName, o.orgTelNum 
            FROM request r
            JOIN space s ON r.spaceID = s.spaceID
            JOIN organizer o ON r.orgID = o.orgID
            WHERE r.requestID = '$requestID' AND r.isDeleted = 0";;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $request = mysqli_fetch_assoc($result);
    } else {
        die(mysqli_error($conn));
    }
}

// Add after session checks
if (isset($_POST['submit'])) {
    $requestID = mysqli_real_escape_string($conn, $_POST['requestID']);
    $dateOfRequest =  mysqli_real_escape_string($conn, $_POST['dateOfRequest']);
    $applicantAddress = mysqli_real_escape_string($conn, trim($_POST['applicantAddress']));
    $reqEventName = mysqli_real_escape_string($conn, trim($_POST['reqEventName']));
    $reqEventType = mysqli_real_escape_string($conn, $_POST['reqEventType']);
    $dateOfUse = mysqli_real_escape_string($conn, $_POST['dateOfUse']);
    $periodOfUseBefore = mysqli_real_escape_string($conn, $_POST['periodOfUseBefore']);
    $periodOfUseAfter = mysqli_real_escape_string($conn, $_POST['periodOfUseAfter']);
    $purposeOfUse = mysqli_real_escape_string($conn, trim($_POST['purposeOfUse']));
    $spaceID = mysqli_real_escape_string($conn, $_POST['spaceID']);

    $sql = "UPDATE request 
            SET applicantAddress = '$applicantAddress', reqEventName = '$reqEventName', reqEventType = '$reqEventType', dateOfUse = '$dateOfUse', periodOfUseStart = '$periodOfUseBefore', periodOfUseEnd = '$periodOfUseAfter', purposeOfUse = '$purposeOfUse', spaceID = '$spaceID'
            WHERE requestID = '$requestID'";


    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Space updated successfully";
        header("Location: view-request.php?requestID=" . $requestID);
        exit();
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
        header("Location: update-request.php?requestID=" . $requestID);
        exit();
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
    <script>
        src = "https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"
    </script>
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
                                <li class="breadcrumb-item"><a href="view-request.php?requestID=<?php echo $request['requestID']; ?>">View Request Details</a></li>
                                <li class="breadcrumb-item active">Update Request Details</li>
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
                                    <h3 class="card-title">Update Request Details</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form name="form" method="POST" action="update-request.php" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="requestID">Request ID</label>
                                            <input name="requestID" class="form-control" id="requestID" value="<?php echo $request['requestID']; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="dateOfRequest">Date of Request</label>
                                            <input name="dateOfRequest" type="date" class="form-control" id="dateOfRequest" value="<?php echo date('Y-m-d'); ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="orgName">Organizer Name</label>
                                            <input name="orgName" type="text" class="form-control" id="orgName" value="<?php echo $request['orgName']; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="orgTelNum">Organizer Telephone Number</label>
                                            <input name="orgTelNum" type="text" class="form-control" id="orgTelNum" value="<?php echo $request['orgTelNum']; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="applicantAddress">Organizer Address</label>
                                            <input name="applicantAddress" type="text" class="form-control" id="applicantAddress" placeholder="Enter your correspondence address" title="Please enter your correspondence address" value="<?php echo $request['applicantAddress']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="reqEventName">Request Event Name</label>
                                            <input name="reqEventName" type="text" class="form-control" id="reqEventName" placeholder="Enter the request event name" title="Please enter the new request event name" value="<?php echo $request['reqEventName']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="reqEventType">Event Type</label>
                                            <select name="reqEventType" id="reqEventType" class="form-control" required>
                                                <option value="Islamic Talks" <?php echo ($request['reqEventType'] == 'Islamic Talks') ? 'selected' : ''; ?>>Islamic Talks</option>
                                                <option value="Nikah/Wedding" <?php echo ($request['reqEventType'] == 'Nikah/Wedding') ? 'selected' : ''; ?>>Nikah/Wedding</option>
                                                <option value="Class" <?php echo ($request['reqEventType'] == 'Class') ? 'selected' : ''; ?>>Class</option>
                                                <option value="Others" <?php echo ($request['reqEventType'] == 'Others') ? 'selected' : ''; ?>>Others</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="dateOfUse">Date of Use</label>
                                            <input name="dateOfUse" type="date" class="form-control" id="dateOfUse" placeholder="Choose the date of use for the event" title="Please choose the date of use for the event" value="<?php echo $request['dateOfUse']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="periodOfUse">Period of Use:</label><br>
                                            <label for="periodOfUseBefore">Start Time</label>
                                            <input name="periodOfUseBefore" type="time" class="form-control" id="periodOfUseBefore" placeholder="Please enter the period of use (start time) for the event" title="Please enter the period of use (start time) for the event" value="<?php echo $request['periodOfUseStart']; ?>" required><br>
                                            <label for="periodOfUseAfter">End Time</label>
                                            <input name="periodOfUseAfter" type="time" class="form-control" id="periodOfUseAfter" placeholder="Please enter the period of use (end time) for the event" title="Please enter the period of use (end time) for the event" value="<?php echo $request['periodOfUseEnd']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="purposeOfUse">Purpose of Use</label>
                                            <input name="purposeOfUse" type="text" class="form-control" id="purposeOfUse" placeholder="Enter the purpose of the event" title="Please enter the purpose of the event" value="<?php echo $request['purposeOfUse']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="reqEventFacility">Choose Mosque Space</label>
                                            <?php if ($rowSpace > 0) { ?>
                                                <select name="spaceID" id="reqEventFacility" class="form-control" onchange="updateSpaceImage(this.value)" required>
                                                    <?php foreach ($spaces as $spaceID => $space) { ?>
                                                        <option value="<?php echo $spaceID; ?>" <?php echo ($request['spaceID'] == $spaceID) ? 'selected' : ''; ?>>
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
                                                                            echo !empty($spaces[$request['spaceID']]['spacePicture']) ?
                                                                                'data:image/jpeg;base64,' . $spaces[$request['spaceID']]['spacePicture'] :
                                                                                '../../images/logo-mbtho.png';
                                                                            ?>" class="d-block mx-auto" style="max-height: 500px; width: auto;" alt="Space Image">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="applicantSignature">Applicant Signature</label>
                                            <input name="applicantSignature" type="text" class="form-control" id="applicantSignature" placeholder="Enter the your full name for digital signature" title="Please enter your full name for digital signature" value="<?php echo $request['applicantSignature']; ?>" readonly>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer d-flex justify-content-end">
                                            <button type="button" class="btn btn-dark mr-2" onclick="location.href='view-request.php?requestID=<?php echo $request['requestID']; ?>'">Back</button>
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