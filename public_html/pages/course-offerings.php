<?php
session_start();
require "../includes/authentication.php";
include "../includes/helper-functions.php";

$connection = createNewConnection();

$standardOfferingsQuery = "SELECT Name, Room, BeginTime, EndTime, LastName AS Instructor, Cost FROM ADULT AS a, INSTRUCTOR_REL AS i, ACTIVITY AS c WHERE i.AdultID = a.AdultID AND c.ActivityID = i.ActivityID";
$standardOfferingsResult = $connection->query($standardOfferingsQuery);

$afterSchoolOfferingsQuery = "SELECT Name, Room, BeginTime, EndTime, LastName AS Instructor, Cost FROM ADULT AS a, AFTER_SCH_REL AS i, ACTIVITY AS c WHERE i.AdultID = a.AdultID AND c.ActivityID = i.ActivityID";
$afterSchoolOfferingsResult = $connection->query($afterSchoolOfferingsQuery);

$connection->close();

include('../includes/head-tag-contents.php');
?>

<body>
    <div class="p-3">
        <h4>Standard Classes:</h4>
        <?= queryToTable($standardOfferingsResult); ?>
        <h4>Extracurricular Activities:</h4>
        <?= queryToTable($afterSchoolOfferingsResult); ?>
    </div>
</body>