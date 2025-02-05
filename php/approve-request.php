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

if (isset($_POST['approve'])) {
    $requestID = mysqli_real_escape_string($conn, $_POST['requestID']);
    $staffID = mysqli_real_escape_string($conn, $_POST['staffID']);
    // Format approver details with admin info
    $approverName = $_SESSION['name'];
    $approverTelNum = $_SESSION['telNum'];
    $approverDetails = "Approved by: " . $approverName . "\nContact: " . $approverTelNum;


    // Start transaction
    $conn->begin_transaction();

    try {
        // Update request status
        $sql = "UPDATE request SET 
                approvalStatus = 1,
                approverDetails = ?
                WHERE requestID = ?";

        echo $sql . "<br>";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $approverDetails, $requestID);
        $stmt->execute();

        // Get request details
        $sqlRequest = "SELECT * FROM request WHERE requestID = ?";
        $stmtRequest = $conn->prepare($sqlRequest);
        $stmtRequest->bind_param("i", $requestID);
        $stmtRequest->execute();
        $request = $stmtRequest->get_result()->fetch_assoc();


        // Insert new event
        $sqlEvent = "INSERT INTO event (
            eventName, 
            eventType, 
            eventDate, 
            eventTimeStart, 
            eventTimeEnd,
            eventDescription,
            isDeleted,
            assignedStaff,
            orgID,
            spaceID
        ) VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?, ?)";

        $stmtEvent = $conn->prepare($sqlEvent);
        $stmtEvent->bind_param(
            "ssssssiii",
            $request['reqEventName'],
            $request['reqEventType'],
            $request['dateOfUse'],
            $request['periodOfUseStart'],
            $request['periodOfUseEnd'],
            $request['purposeOfUse'],
            $staffID,
            $request['orgID'],
            $request['spaceID']
        );
        $stmtEvent->execute();

        // Commit transaction
        $conn->commit();

        $_SESSION['success'] = "Request approved and event created successfully";
        header("Location: ../admin/request/view-request.php?requestID=" . $requestID);
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $_SESSION['error'] = "Error: " . $e->getMessage();
        echo $e->getMessage();
        // header("Location: ../admin/request/view-request.php?requestID=" . $requestID);
    }
    exit();
}
