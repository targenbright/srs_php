<?php
session_start();

require "../includes/authentication.php";
// include "misc-variables.php";
include "../includes/helper-functions.php";

$connection = createNewConnection();

$accountId = $_SESSION["accountId"];
$accountQuery = "SELECT PrimaryUserID, SpouseID FROM ACCOUNT WHERE AccountID = $accountId";

$accountQueryResult = $connection->query($accountQuery);
$row = $accountQueryResult->fetch_assoc();
$userId = $row["PrimaryUserID"];
$spouseId = $row["SpouseID"];
$adultInfoQuery = "SELECT FirstName, LastName, Address, City, State, Zip, PhoneNumber, Email FROM ADULT WHERE AdultID = $userId";
// Add spouse to query, if applicable. Better way to do this?
if ($spouseId) {
  $adultInfoQuery = $adultInfoQuery . " OR AdultID = $spouseId";
}

$adultInfoQueryResult = $connection->query($adultInfoQuery);

$connection->close();

include('../includes/head-tag-contents.php');
?>

<div class="p-3">
  <h5>Contact Information:</h5>
  <?= queryToTable($adultInfoQueryResult) ?>
</div>
<div class="p-3">
  <!-- TODO: Make these buttons actually do something -->
  <a class="btn btn-default" href="edit-personal-info.php">Edit my information</a>

  <?php
  /** Change what the button says based on whether
   * a spouse has been added to the account.
   */
  if ($adultInfoQueryResult->num_rows > 1) {
  ?>
    <a class="btn btn-default" href="edit-spouse-info.php">Edit Spouse Information</a>
    <a class="btn btn-default" href="remove-spouse.php">Remove Spouse</a>
  <?php
  } else {
  ?>
    <a class="btn btn-default" href="add-spouse.php">Add Spouse</a>
  <?php
  }
  ?>
  </a>
</div>