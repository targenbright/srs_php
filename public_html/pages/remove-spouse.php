<?php
session_start();

require "../includes/authentication.php";
// include "misc-variables.php";
include "../includes/helper-functions.php";

$connection = createNewConnection();

// Get AccountID and AdultID for current user
$primaryAccountId = $_SESSION["accountId"];
$primaryAdultID = $_SESSION["adultId"];

$spouseAccountIDQuery = "SELECT AccountID FROM ACCOUNT WHERE SpouseID = $primaryAdultID";
$spouseAccountIDresult = $connection->query($spouseAccountIDQuery);
$spouseAccountID = $spouseAccountIDresult->fetch_object()->AccountID;

// Add spouse to this account
$updateQuery = "UPDATE `ACCOUNT` SET `SpouseID` = NULL WHERE `AccountID` = $primaryAccountId;";
// Add this account to spouse account
$updateQuery .= "UPDATE `ACCOUNT` SET `SpouseID` = NULL WHERE `AccountID` = $spouseAccountID";
mysqli_multi_query($connection, $updateQuery);
// Store the error, if any.
$errorMessage = $connection->error;

// Remove spouse from current session
$_SESSION["spouseId"] = NULL;

// Go back to profile
header("Location: my-profile.php");

?>