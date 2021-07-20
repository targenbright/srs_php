<?php
session_start();
require "../includes/authentication.php";
include "../includes/helper-functions.php";

$adultId = $_SESSION["adultId"];
$spouseId = $_SESSION["spouseId"];

$connection = createNewConnection();

$rolesQuery = "SELECT Role FROM ROLES AS r, ROLE_REL AS rr WHERE rr.AdultID = $adultId AND r.RoleID = rr.RoleID";
$rolesResult = $connection->query($rolesQuery);

$childrenQuery = "SELECT c.FirstName, c.LastName FROM CHILD AS c, PAR_CH_REL AS r WHERE r.ChildID = c.ChildID AND (r.AdultID = $adultId";
// Hack to handle spouse being null.
if ($spouseId) {
    $childrenQuery = $childrenQuery . " OR r.AdultID = $spouseId";
}
$childrenQuery = $childrenQuery . ")";
$childrenResult = $connection->query($childrenQuery);

$enrollmentQuery = "SELECT * FROM CHILD AS c, PAR_CH_REL AS r, ENROLLMENT AS e WHERE r.AdultID = $adultId AND c.ChildID = r.ChildID AND e.ChildID = r.ChildID";
$enrollmentResult = $connection->query($enrollmentQuery);

$tuitionQuery = "SELECT SUM(Cost) AS TotalCost FROM CHILD AS c, PAR_CH_REL AS r, ENROLLMENT AS e, ACTIVITY AS a WHERE r.AdultID = $adultId AND c.ChildID = r.ChildID AND e.ChildID = r.ChildID AND e.ActivityID = a.ActivityID";
$tuitionResult = $connection->query($tuitionQuery);

$connection->close();

include('../includes/head-tag-contents.php');
?>

<div class="p-3">
    <div class="row">
        <div class="col-sm-6">
            <h3>Your active roles are: </h3>
            <?php while ($line = $rolesResult->fetch_assoc()) {
                echo $line["Role"] . "<br>";
            } ?>
        </div>
        <div class="col-sm-6">
            <h3>Children:</h3>
            <?php while ($line = $childrenResult->fetch_assoc()) {
                echo $line["FirstName"] . " " . $line["LastName"] . "<br>";
            } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <h3>Enrollment:</h3>
            Your children are enrolled in <?= $enrollmentResult->num_rows ?> courses.
        </div>
        <div class="col-sm-6">
            <h3>Tuition:</h3>
            The tuition due is $<?= $tuitionResult->fetch_object()->TotalCost ?>.
        </div>
    </div>
</div>