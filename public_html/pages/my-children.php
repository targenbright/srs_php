<?php
session_start();

require "../includes/authentication.php";
include "../includes/helper-functions.php";

$connection = new mysqli($server, $sqlUsername, $sqlPassword, $databaseName);
$accountId = $_SESSION["accountId"];
$adultId = $_SESSION["adultId"];
$spouseId = $_SESSION["spouseId"];
// $childQuery = "SELECT c.LastName, c.FirstName, c.DateOfBirth, c.Gender, c.GradeLevel FROM CHILD AS c, PAR_CH_REL AS r, ADULT AS a, ACCOUNT AS x WHERE x.AccountID = $accountId AND x.PrimaryUserID = a.AdultID AND r.AdultID = a.AdultID AND r.ChildID = c.ChildID";
// Show all children with associations to
$myChildrenQuery = "SELECT c.FirstName, c.LastName, c.DateOfBirth, c.Gender, c.GradeLevel FROM CHILD AS c, PAR_CH_REL AS r WHERE c.ChildID = r.ChildID AND r.AdultID = $adultId";
$myResult = $connection->query($myChildrenQuery);
$spouseChildrenQuery = "SELECT c.FirstName, c.LastName, c.DateOfBirth, c.Gender, c.GradeLevel FROM CHILD AS c, PAR_CH_REL AS r WHERE c.ChildID = r.ChildID AND r.AdultID = $spouseId";
$spouseResult = $connection->query($spouseChildrenQuery);
$connection->close();

include('../includes/head-tag-contents.php');
?>
<div class="p-3">
    <h4>Your children:</h4>
    <?= queryToTable($myResult); ?>
    <a class="btn btn-default" href="add-child.php">Add Child</a>
    <h4>Your Spouse's children:</h4>
    <?= queryToTable($spouseResult); ?>
</div>