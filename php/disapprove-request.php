<?php
session_start();
if (!isset($_SESSION['userlogged']) || ($_SESSION['userlogged'] != 1)) {
    header("Location: ../../index.php");
    exit();
}

if (!isset($_SESSION['userID'])) {
    header("Location: logout.php");
}

include "dbconn.php";

if (isset($_POST['disapprove'])) {
    $requestID = mysqli_real_escape_string($conn, $_POST['requestID']);

    // Format approver details with admin info
    $approverName = $_SESSION['name'];
    $approverTelNum = $_SESSION['telNum'];
    $approverDetails = "Disapproved by: " . $approverName . "\nContact: " . $approverTelNum;

    $sql = "UPDATE request SET 
            approvalStatus = 0,
            approverDetails = ?
            WHERE requestID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $approverDetails, $requestID);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Request disapproved";
        header("Location: ../admin/request/view-request.php?requestID=" . $requestID);
    } else {
        $_SESSION['error'] = "Error disapproving request: " . $conn->error;
        header("Location: ../admin/request/view-request.php?requestID=" . $requestID);
    }
    exit();
}
