<?php

require "../includes/authentication.php";
include "../includes/helper-functions.php";

session_start();
$errorMessage = "";

if (isset($_POST["userEmail"]) && isset($_POST["password"])) :

    $loginId = $_POST["userEmail"];
    $loginPassword = $_POST["password"];
    $connection = new mysqli($server, $sqlUsername, $sqlPassword, $databaseName);

    if (authenticateUser($connection, $loginId, $loginPassword)) :
        $_SESSION["logged_in"] = true;
        $_SESSION["userId"] = $loginId;
        $accountIdQuery = "SELECT AccountID, PrimaryUserID, SpouseID FROM ACCOUNT AS a, ADULT AS d WHERE a.PrimaryUserID = d.AdultID AND d.Email = '$loginId'";
        $accountIdResult = $connection->query($accountIdQuery);
        $row = $accountIdResult->fetch_assoc();
        $_SESSION["accountId"] = $row["AccountID"];
        $_SESSION["adultId"] = $row["PrimaryUserID"];
        $_SESSION["spouseId"] = $row["SpouseID"];
?>





        <!-- Is there a better way to do this? -->
        <script>
            parent.document.location = "../index.php";
        </script>
<?php
        exit;
    else :
        $errorMessage = "Incorrect username or password.";
    endif;
    $connection->close();
endif;
?>
<?php
if ($errorMessage) {
    createFeedbackBanner("Successfully logged in.", $errorMessage);
}

include('../includes/head-tag-contents.php');
?>

<div class="container-fluid">
    <div class="p-3">
        <form action="" method="post" name="loginForm" id="loginForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="userEmail">Email address:</label>
                    <input name="userEmail" type="email" class="form-control" id="userEmail" placeholder="john.doe@email.com">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input name="password" type="password" class="form-control" id="password" placeholder="Password">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <a href="reset-password.php">Forgot password</a>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-block">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>