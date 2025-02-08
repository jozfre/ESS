<?php
session_start();
if (!isset($_SESSION['userlogged']) || ($_SESSION['userlogged'] != 1)) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_SESSION['userID'])) {
    header("Location: logout.php");
}

include "dbconn.php";

function checkEventConflicts($conn, $dateOfUse, $timeStart, $timeEnd, $spaceID) {
    $conflicts = array();
    
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
        $timeStart, 
        $timeEnd,
        $timeStart, 
        $timeEnd,
        $timeStart,
        $timeEnd
    );
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $conflictEvent = $result->fetch_assoc();
        $conflicts[] = array(
            'type' => 'booking',
            'message' => "Space Conflict: Space already booked for event '{$conflictEvent['eventName']}' on " . 
                        date('d-m-Y', strtotime($conflictEvent['eventDate'])) . 
                        " ({$conflictEvent['eventTimeStart']} - {$conflictEvent['eventTimeEnd']})"
        );
    }
    
    // Validate time
    if (strtotime($timeEnd) <= strtotime($timeStart)) {
        $conflicts[] = array(
            'type' => 'time',
            'message' => "Time Conflict: End time must be after start time"
        );
    }
    
    // Validate date
    if (strtotime($dateOfUse) < strtotime(date('Y-m-d'))) {
        $conflicts[] = array(
            'type' => 'date',
            'message' => "Date Conflict: Cannot approve requests for past dates"
        );
    }
    
    return $conflicts;
}

if (isset($_POST['approve'])) {
    $requestID = mysqli_real_escape_string($conn, $_POST['requestID']);
    $staffID = mysqli_real_escape_string($conn, $_POST['staffID']);

    // Get request details first
    $sqlRequest = "SELECT * FROM request WHERE requestID = ?";
    $stmtRequest = $conn->prepare($sqlRequest);
    $stmtRequest->bind_param("i", $requestID);
    $stmtRequest->execute();
    $request = $stmtRequest->get_result()->fetch_assoc();

     // Check for conflicts before proceeding
     $conflicts = checkEventConflicts(
        $conn,
        $request['dateOfUse'],
        $request['periodOfUseStart'],
        $request['periodOfUseEnd'],
        $request['spaceID']
    );

    if (!empty($conflicts)) {
        $_SESSION['error'] = $conflicts[0]['message'];
        header("Location: ../admin/request/view-request.php?requestID=" . $requestID);
        exit();
    }

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

        $_SESSION['approve'] = "Request approved and event created successfully";
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
