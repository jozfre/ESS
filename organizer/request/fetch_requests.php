<?php
include "../../php/dbconn.php";

$filter = isset($_POST['filter']) ? $_POST['filter'] : 'All';
$orgID = isset($_POST['orgID']) ? $_POST['orgID'] : $_SESSION['orgID'];

$sql = "SELECT r.*, o.orgName 
        FROM request r 
        JOIN organizer o ON r.orgID = o.orgID 
        WHERE r.isDeleted = 0 
        AND r.orgID = '$orgID'";


if ($filter == "Pending") {
    $sql .= " AND approvalStatus IS NULL";
} elseif ($filter == "Approved") {
    $sql .= " AND approvalStatus = 1";
} elseif ($filter == "Not Approved") {
    $sql .= " AND approvalStatus = 0";
}

$result = mysqli_query($conn, $sql);

while ($request = mysqli_fetch_assoc($result)) {
    $badgeClass = '';
    $statusText = 'All';

    if ($request['approvalStatus'] === NULL) {
        $badgeClass = 'badge-secondary';
        $statusText = 'To Be Reviewed';
    } else if ($request['approvalStatus'] == 1) {
        $badgeClass = 'badge-success';
        $statusText = 'Approved';
    } else if ($request['approvalStatus'] == 0) {
        $badgeClass = 'badge-danger';
        $statusText = 'Not Approved';
    }

    echo "<tr>
            <td>{$request['dateOfRequest']}</td>
            <td>{$request['reqEventName']}</td>
            <td>{$request['reqEventType']}</td>
            <td><span class='badge {$badgeClass}'>{$statusText}</span></td>
            <td>
              <a href='view-request.php?requestID={$request['requestID']}' class='btn btn-info btn-sm fas fa-eye'></a>
            </td>
          </tr>";
}
