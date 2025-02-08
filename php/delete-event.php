<?php
session_start();
if(!isset($_SESSION['userlogged']) || ($_SESSION['userlogged'] != 1)) {
    header("Location: ../index.php");
}

include "dbconn.php";

if(isset($_POST['deleteEvent'])) {
    $eventID = mysqli_real_escape_string($conn, $_POST['eventID']);
    
    // Soft delete - update isDeleted flag
    $sql = "UPDATE event SET isDeleted = 1 WHERE eventID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventID);
    
    if($stmt->execute()) {
        $_SESSION['success_delete'] = "Event deleted successfully";
        header("Location: ../admin/event/list-event.php");
    } else {
        $_SESSION['error'] = "Error deleting event: " . $conn->error;
        header("Location: ../admin/event/view-event.php?eventID=" . $eventID);
 
    }
    exit();
}
?>