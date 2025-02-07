<?php
include "../../php/dbconn.php";

$filter = isset($_POST['filter']) ? $_POST['filter'] : 'All';

// Base SQL query
$sql = "SELECT e.*, o.orgName, s.spaceName, u.name as staffName 
        FROM event e
        LEFT JOIN organizer o ON e.orgID = o.orgID
        LEFT JOIN space s ON e.spaceID = s.spaceID
        LEFT JOIN user u ON e.assignedStaff = u.userID
        WHERE e.isDeleted = 0";

// New SQL queries for each filter
$sqlToday = $sql . " ORDER BY e.eventDate ASC, e.eventTimeStart ASC";
$sqlToday = $sql . " AND DATE(e.eventDate) = CURDATE() ORDER BY e.eventDate ASC, e.eventTimeStart ASC";
$sqlWeek = $sql . " AND YEARWEEK(e.eventDate, 1) = YEARWEEK(CURDATE(), 1) ORDER BY e.eventDate ASC, e.eventTimeStart ASC";
$sqlMonth = $sql . " AND MONTH(e.eventDate) = MONTH(CURDATE()) AND YEAR(e.eventDate) = YEAR(CURDATE()) ORDER BY e.eventDate ASC, e.eventTimeStart ASC";

// Execute the appropriate query based on the filter
if ($filter == "Today") {
    $result = mysqli_query($conn, $sqlToday);
} elseif ($filter == "This Week") {
    $result = mysqli_query($conn, $sqlWeek);
} elseif ($filter == "This Month") {
    $result = mysqli_query($conn, $sqlMonth);
} else {
    $result = mysqli_query($conn, $sql);
}

// Fetch and display the results
while ($event = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . ($event['orgID'] === NULL ? 'Anjuran MBTHO' : $event['orgName']) . "</td>";
    echo "<td>{$event['eventName']}</td>";
    echo "<td>{$event['eventType']}</td>";
    echo "<td>" . date('d-m-Y', strtotime($event['eventDate'])) . "</td>";
    echo "<td>{$event['eventTimeStart']}</td>";
    echo "<td>{$event['eventTimeEnd']}</td>";
    echo "<td>
            <a href='view-event.php?eventID={$event['eventID']}' class='btn btn-info btn-sm'>
                <i class='fas fa-eye'></i>
            </a>
          </td>";
    echo "</tr>";
}
?>
