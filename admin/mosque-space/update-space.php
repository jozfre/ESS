<?php
session_start();
if (!isset($_SESSION['userlogged']) || ($_SESSION['userlogged'] != 1)) {
  header("Location: ../../index.php");
}

if (!isset($_SESSION['userID'])) {
  header("Location: ../../php/logout.php");
}

include "../../php/dbconn.php";

// Add this after including dbconn.php
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

// Add after session checks
if (isset($_POST['submit'])) {
  $spaceID = mysqli_real_escape_string($conn, $_POST['spaceID']);
  $spaceName = mysqli_real_escape_string($conn, $_POST['spaceName']);
  $spaceCapacity = mysqli_real_escape_string($conn, $_POST['spaceCapacity']);
  $spaceType = mysqli_real_escape_string($conn, $_POST['spaceType']);

  // Check if new image uploaded
  if (!empty($_FILES['spaceImage']['tmp_name'])) {
    $fileData = file_get_contents($_FILES['spaceImage']['tmp_name']);
    $fileType = $_FILES['spaceImage']['type'];

    if (in_array($fileType, ['image/jpeg', 'image/png'])) {
      $imageBase64 = base64_encode($fileData);
      $sql = "UPDATE space SET 
                    spaceName = '$spaceName',
                    spaceCapacity = '$spaceCapacity',
                    spaceType = '$spaceType',
                    spacePicture = '$imageBase64'
                    WHERE spaceID = '$spaceID'";
    } else {
      $_SESSION['error'] = "Invalid file type. Only JPG and PNG allowed.";
      header("Location: update-space.php?spaceID=" . $spaceID);
      exit();
    }
  } else {
    // Update without changing image
    $sql = "UPDATE space SET 
                spaceName = '$spaceName',
                spaceCapacity = '$spaceCapacity',
                spaceType = '$spaceType'
                WHERE spaceID = '$spaceID'";
  }

  if (mysqli_query($conn, $sql)) {
    $_SESSION['success'] = "Space details updated successfully";
    header("Location: view-space.php?spaceID=" . $spaceID);
    exit();
  } else {
    $_SESSION['error'] = "Error: " . mysqli_error($conn);
    header("Location: update-space.php?spaceID=" . $spaceID);
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
                <li class="breadcrumb-item"><a href="list-space.php">List of Spaces</a></li>
                <li class="breadcrumb-item"><a href="view-space.php?spaceID=<?php echo $spaceID; ?>">View Space Details</a></li>
                <li class="breadcrumb-item active">Update Space Details</li>
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
                  <h3 class="card-title">Update Space Details</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="POST" action="update-space.php" enctype="multipart/form-data">
                  <div class="card-body">
                    <div class="form-group">
                      <label for="spaceID">Space ID</label>
                      <input name="spaceID" class="form-control" id="spaceID" value="<?= $space['spaceID']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="spaceName">Space Name</label>
                      <input name="spaceName" class="form-control" id="spaceName" value="<?= $space['spaceName']; ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="spaceCapacity">Space Capacity (People)</label>
                      <input name="spaceCapacity" type="number" class="form-control" id="spaceCapacity" value="<?= $space['spaceCapacity']; ?>" min="0" required>
                    </div>
                    <div class="form-group">
                      <label for="spaceType">Space Type</label>
                      <select name="spaceType" id="spaceType" class="form-control" required>
                        <option value="Prayer Hall" <?= ($space['spaceType'] == 'Prayer Hall') ? 'selected' : ''; ?>>Prayer Hall</option>
                        <option value="Closed Hall" <?= ($space['spaceType'] == 'Closed Hall') ? 'selected' : ''; ?>>Closed Hall</option>
                        <option value="Meeting Room" <?= ($space['spaceType'] == 'Meeting Room') ? 'selected' : ''; ?>>Meeting Room</option>
                        <option value="Office" <?= ($space['spaceType'] == 'Office') ? 'selected' : ''; ?>>Office</option>
                        <option value="Others" <?= ($space['spaceType'] == 'Others') ? 'selected' : ''; ?>>Office</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="spaceImage">Insert Space Image File</label>
                      <div class="custom-file">
                        <input type="file" name="spaceImage" class="custom-file-input" id="spaceImage">
                        <label class="custom-file-label" for="spaceImage">Choose file (.jpg/.jpeg/.png)</label>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="spaceImage">Image of Space</label>
                      <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" style="background-color: rgba(0, 0, 0, 0.8); border: 2px solid #ccc; border-radius: 10px;">
                        <div class="carousel-inner text-center">
                          <div class="carousel-item active">
                            <?php if (!empty($space['spacePicture'])): ?>
                              <img src="data:image/jpeg;base64,<?php echo $space['spacePicture']; ?>" class="d-block mx-auto" style="max-height: 400px; width: auto;" alt="Space Image">
                            <?php else: ?>
                              <img src="../../images/logo-mbtho.png" class="d-block mx-auto" alt="Default Image">
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer d-flex justify-content-end">
                      <button type="button" class="btn btn-dark mr-2" onclick="location.href='view-space.php?spaceID=<?php echo $spaceID; ?>'">Back</button>
                      <button type="submit" class="btn btn-primary" name="submit">Save Updated Details</button>
                    </div>
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
  <script>
    document.getElementById('spaceImage').addEventListener('change', function(e) {
      const file = e.target.files[0];
      const fileReader = new FileReader();

      if (file && file.type.match('image.*')) {
        fileReader.onload = function(e) {
          document.querySelector('.carousel-item.active img').src = e.target.result;
          document.querySelector('.custom-file-label').textContent = file.name;
        }
        fileReader.readAsDataURL(file);
      } else {
        alert('Please select an image file (JPG/PNG)');
        this.value = '';
      }
    });
  </script>
</body>

</html>