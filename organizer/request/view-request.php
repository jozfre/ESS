<?php
session_start();
if(!isset($_SESSION['orglogged']) || ($_SESSION['orglogged'] != 1))
{
    header("Location: ../../index.php");
}

if(!isset($_SESSION['orgID']))
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
                        <a href="#" class="d-block text-truncate"><?php if(isset($_SESSION['orgName'])) { echo $_SESSION['orgName']; } ?></a>
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
                                <li class="breadcrumb-item active">View Request Details</li>
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
                                    <h3 class="card-title">Request Details</h3>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-danger btn-sm float-right mr-2" data-toggle="modal" data-target="#deleteModal"><i class="fas fa-window-close"></i> Cancel Request</button>
                                        <button type="button" class="btn btn-primary btn-sm float-right" onclick="location.href='update-request.php'"><i class="fas fa-edit"></i> Update Details</button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form name="form" method="POST" action="create-request.php" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="requestID">Request ID</label>
                                            <input name="requestID" class="form-control" id="requestID" value="0001" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="dateOfRequest">Date of Request</label>
                                            <input name="dateOfRequest" type="date" class="form-control" id="dateOfRequest" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="orgName">Organizer Name</label>
                                            <input name="orgName" type="text" class="form-control" id="orgName" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="orgTelNum">Organizer Telephone Number</label>
                                            <input name="orgTelNum" type="text" class="form-control" id="orgTelNum" pattern="[0-9]{10}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="applicantAddress">Organizer Address</label>
                                            <input name="applicantAddress" type="text" class="form-control" id="applicantAddress" placeholder="Enter your correspondence address" title="Please enter your correspondence address" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="reqEventName">Request Event Name</label>
                                            <input name="reqEventName" type="text" class="form-control" id="reqEventName" placeholder="Enter the request event name" title="Please enter the new request event name" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="reqEventType">Event Type</label>
                                            <select name="reqEventType" id="reqEventType" class="form-control" placeholder="Choose Event Type" readonly>
                                                <option value="Islamic Talks">Islamic Talks</option>
                                                <option value="Wedding">Wedding</option>
                                                <option value="Class">Class</option>
                                                <option value="Others">Others</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="dateOfUse">Date of Use</label>
                                            <input name="dateOfUse" type="date" class="form-control" id="dateOfUse" placeholder="Choose the date of use for the event" title="Please choose the date of use for the event" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="periodOfUse">Period of Use:</label><br>
                                            <label for="periodOfUseBefore">Start Time</label>
                                            <input name="periodOfUseBefore" type="time" class="form-control" id="periodOfUseBefore" placeholder="Please enter the period of use (start time) for the event" title="Please enter the period of use (start time) for the event" readonly><br>
                                            <label for="periodOfUseAfter">End Time</label>
                                            <input name="periodOfUseAfter" type="time" class="form-control" id="periodOfUseAfter" placeholder="Please enter the period of use (end time) for the event" title="Please enter the period of use (end time) for the event" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="purposeOfUse">Purpose of Use</label>
                                            <input name="purposeOfUse" type="text" class="form-control" id="purposeOfUse" placeholder="Enter the purpose of the event" title="Please enter the purpose of the event" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="reqEventFacility">Choose Mosque Space</label>
                                            <select name="reqEventFacility" id="reqEventFacility" class="form-control" placeholder="Choose Mosque Space" readonly>
                                                <option value="Prayer Hall">Prayer Hall</option>
                                                <option value="Closed Hall">Closed Hall</option>
                                                <option value="Meeting Room<">Meeting Room</option>
                                                <option value="Office">Office</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="spaceImage">Mosque Space Image</label>
                                            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" style="background-color: rgba(0, 0, 0, 0.8); border: 2px solid #ccc; border-radius: 10px;">
                                                <div class="carousel-inner text-center">
                                                    <div class="carousel-item active">
                                                        <img src="../../images/logo-mbtho.png" class="d-block mx-auto" alt="...">
                                                    </div>
                                                    <div class="carousel-item">
                                                        <img src="../../images/logo-mbtho.png" class="d-block mx-auto" alt="...">
                                                    </div>
                                                    <div class="carousel-item">
                                                        <img src="../../images/logo-mbtho.png" class="d-block mx-auto" alt="...">
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
                                        <div class="form-group">
                                            <label for="applicantSignature">Applicant Signature</label>
                                            <input name="applicantSignature" type="text" class="form-control" id="applicantSignature" placeholder="Enter the your full name for digital signature" title="Please enter your full name for digital signature" readonly>
                                        </div>

                                        <!-- /.card-body -->
                                        <div class="card-footer d-flex justify-content-between align-items-center">
                                            <button type="button" class="btn btn-dark" onclick="location.href='list-request.php'">Back</button>
                                        </div>
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
    <!-- Deletion Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Cancel Request</h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel the following request?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-primary">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Deletion Modal -->
</body>

</html>