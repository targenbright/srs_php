<?php
session_start();
require "../includes/authentication.php";
require "../includes/helper-functions.php";

$connection = createNewConnection();

$adultId = $_SESSION["adultId"];
// $findChildrenQuery = "SELECT DISTINCT c.ChildID, c.LastName, c.FirstName FROM CHILD AS c, PAR_CH_REL AS r WHERE c.ChildId = r.ChildID AND r.AdultID = $adultId";
// $findChildrenQueryResult = $connection->query($findChildrenQuery);
$isTeacherQuery = "SELECT * FROM ROLES AS r, ROLE_REL AS rr WHERE r.RoleID = rr.RoleID AND rr.AdultID = $adultId AND r.Role = 'Teacher'";
$isTeacherResult = $connection->query($isTeacherQuery);

$discountRate = ($isTeacherResult->num_rows > 0) ? 0.90 : 1;

$tuitionQuery = "SELECT c.FirstName, c.LastName, SUM(COST) AS Subtotal, ROUND( SUM(Cost) * $discountRate, 2) AS Total FROM ACTIVITY AS a, ENROLLMENT AS e, ADULT AS d, PAR_CH_REL AS r, CHILD AS c WHERE d.AdultID = $adultId AND r.AdultID = d.AdultID AND c.ChildID = r.ChildID AND c.ChildID = e.ChildID AND e.ActivityID = a.ActivityID GROUP BY r.ChildID";
$tuitionResult = $connection->query($tuitionQuery);
$connection->close();

include('../includes/head-tag-contents.php');

if ($discountRate < 1) {
    createFeedbackBanner("Teacher discount applied", "");
}
?>

<body>
    <div class="p-3">
        <!-- Create a new table for each child -->
        <?php queryToTable($tuitionResult); ?>
    </div>
</body>