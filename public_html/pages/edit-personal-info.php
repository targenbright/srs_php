<?php
session_start();

require "../includes/authentication.php";
include "../includes/misc-variables.php";
include "../includes/helper-functions.php";

if (isset($_POST["cancel"])) {
    header("Location: my-profile.php");
    exit;
}

$connection = createNewConnection();

// Attempt to execute the update statement before fetching the data.
// Check whether one of the fields is set to determine whether the user has updated information.
if (isset($_POST["firstName"]) || isset($_POST["lastName"])) :
    $adultId = $_SESSION["adultId"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $address = $_POST["address"];
    $city = $_POST["city"];
    $state = $_POST["state"];
    $zip = $_POST["zip"];
    $email = $_POST["userEmail"];
    $phoneNumber = $_POST["phoneNumber"];

    /**
     * Do we need to check whether the phone number and email address
     * are already in use by another account? Search for email and/or
     * phone number where AdultID is not current adult?
     */
    if (empty($firstName) || empty($lastName)) {
        $errorMessage = "First Name and Last Name fields must not be blank.";
    } else {
        $updateQuery = "UPDATE ADULT SET LastName='$lastName', FirstName='$firstName', Address='$address', City='$city', State='$state', Zip='$zip', Email='$email', PhoneNumber='$phoneNumber' WHERE AdultID=$adultId";
        $connection->query($updateQuery);
        // Store the error, if any.
        $errorMessage = $connection->error;
    }
    // If first or last name is empty, do not do anything. These are the NOT NULL values
    // If any of the input boxes match the old data
    // Skip that update
    // If any of the input boxes do not match old data...
    // Update database
    createFeedbackBanner("Your information has been updated.", $errorMessage);
endif;

// Retrieve the information currently stored in the database.
$accountId = $_SESSION["accountId"];
$accountQuery = "SELECT PrimaryUserID FROM ACCOUNT WHERE AccountID = $accountId";

$accountQueryResult = $connection->query($accountQuery);
$adultID = $accountQueryResult->fetch_object()->PrimaryUserID;

$adultInfoQuery = "SELECT d.FirstName, d.LastName, d.Address, d.City, d.State, d.Zip, d.Email, d.PhoneNumber FROM ADULT as d WHERE AdultID = $adultID";
$data = $connection->query($adultInfoQuery);

// fetch_object() did not behave as expected, this seems to work well enough.
$row = $data->fetch_assoc();

$connection->close();

include('../includes/head-tag-contents.php');
?>

<div class="p-3">
    <form action="" method="post" name="formSignup" id="formSignup">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="firstName">First Name:</label>
                <input name="firstName" type="text" class="form-control disabled" id="firstName" placeholder="John" value="<?= $row["FirstName"] ?>" readonly="readonly">
            </div>
            <div class="form-group col-md-6">
                <label for="lastName">Last Name:</label>
                <input name="lastName" type="text" class="form-control" id="lastName" placeholder="Doe" value="<?= $row["LastName"] ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="address">Address:</label>
                <input name="address" type="text" class="form-control" id="address" placeholder="1234 Main Street" value="<?= $row["Address"] ?>">
            </div>
            <div class="form-group col-md-3">
                <label for="city">City:</label>
                <input name="city" type="text" class="form-control" id="city" placeholder="Cityville" value="<?= $row["City"] ?>">
            </div>
            <div class="form-group col-md-3">
                <label for="state">State:</label>
                <select class="form-control" id="state" name="state">
                    <option value="">State</option>
                    <?php foreach ($states as &$state) : ?>
                        <?php
                        if ($state === $row["State"]) {
                            echo '<option selected value="' . $state . '">' . $state . '</option>';
                        } else {
                            echo '<option value="' . $state . '">' . $state . '</option>';
                        }
                        ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="zip">Zip code:</label>
                <input name="zip" type="text" class="form-control" id="zip" placeholder="12345" value="<?= $row["Zip"] ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="userEmail">Email address:</label>
                <input name="userEmail" type="email" class="form-control" id="userEmail" placeholder="john.doe@email.com" value="<?= $row["Email"] ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="phoneNumber">Phone Number:</label>
                <input name="phoneNumber" type="tel" class="form-control" id="phoneNumber" placeholder="555-555-5555" value="<?= $row["PhoneNumber"] ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <button type="submit" class="btn btn-success btn-block">Update</button>
            </div>
            <div class="form-group col-md-6">
                <button type="submit" class="btn btn-danger btn-block" name="cancel">Cancel</button>
            </div>
        </div>
    </form>
</div>