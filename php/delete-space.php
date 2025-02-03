<?php
session_start();
include "dbconn.php";

if (isset($_POST['delete'])) {
  $spaceID = mysqli_real_escape_string($conn, $_POST['spaceID']);

  $sql = "UPDATE space SET isDeleted = 1 WHERE spaceID = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "s", $spaceID);

  if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Space deleted successfully";
    header("Location: ../admin/mosque-space/list-space.php");
  } else {
    $_SESSION['error'] = "Error deleting space: " . mysqli_error($conn);
    header("Location: ../admin/mosque-space/view-space.php?spaceID=" . $spaceID);
  }
  exit();
}
