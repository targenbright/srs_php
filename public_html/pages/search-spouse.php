<?php
session_start();

require "../includes/authentication.php";
include "../includes/misc-variables.php";
include "../includes/helper-functions.php";

$connection = new mysqli($server, $sqlUsername, $sqlPassword, $databaseName);

if (isset($_POST["cancel"])) {
    header("Location: my-profile.php");
    exit;
}

if (isset($_POST["spouseID"])) {

    // Get AccountID and AdultID for newly added spouse
    $spouseAdultID = $_POST["spouseID"];
    $spouseAccountQuery = "SELECT AccountID FROM ACCOUNT WHERE PrimaryUserID = $spouseAdultID";
    $spouseAccountIDresult = $connection->query($spouseAccountQuery);
    $spouseAccountID = $spouseAccountIDresult->fetch_object()->AccountID;

    // Get AccountID and AdultID for current user
    $primaryAccountId = $_SESSION["accountId"];
    $primaryAdultID = $_SESSION["adultId"];

    // Add spouse to this account
    $updateQuery = "UPDATE `ACCOUNT` SET `SpouseID` = $spouseAdultID WHERE `AccountID` = $primaryAccountId;";
    // Add this account to spouse account
    $updateQuery .= "UPDATE `ACCOUNT` SET `SpouseID` = $primaryAdultID WHERE `AccountID` = $spouseAccountID";
    mysqli_multi_query($connection, $updateQuery);
    // Store the error, if any.
    $errorMessage = $connection->error;

    // Store spouseID session
    $_SESSION["spouseId"] = $spouseAdultID;
    // Go back to profile
    header("Location: my-profile.php");
    exit;
}

include('../includes/head-tag-contents.php');
?>

<div class="p-3">
    <form action="" method="post" name="searchSpouse" id="searchSpouse">
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="email">Search by email:</label>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-10">
                <input name="email" type="text" class="form-control" id="firstName" placeholder="john.doe@email.com">
            </div>
            <div class="form-group col-md-1">
                <button type="submit" class="btn btn-primary btn-block" style="width:100%">Search</button>
            </div>
            <div class="form-group col-md-1">
                <button type="submit" class="btn btn-danger btn-block" name="cancel" style="width:100%">Cancel</button>
            </div>
        </div>


    </form>
</div>

<?php

if (isset($_POST["email"])) {
    $email = $_POST['email'];

    $offeringsQuery = "SELECT * FROM ADULT WHERE Email = '$email'";
    $offeringsResult = $connection->query($offeringsQuery);

?>

    <div class="p-3">
        <?php spouseSearchToTable($offeringsResult); ?>
    </div>

<?php
}

?>