<?php
session_start();
include "dbconn.php";

if (isset($_POST['deleteRequest'])) {
  $requestID = mysqli_real_escape_string($conn, $_POST['requestID']);

  $sql = "UPDATE request SET isDeleted = 1 WHERE requestID = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "s", $requestID);

  if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_delete'] = "Event request cancelled successfully";
    header("Location: ../organizer/request/list-request.php");
  } else {
    $_SESSION['error'] = "Error cancelling event request: " . mysqli_error($conn);
    header("Location: ../organizer/request/view-request.php?requestID=" . $requestID);
  }
  exit();
}
?>