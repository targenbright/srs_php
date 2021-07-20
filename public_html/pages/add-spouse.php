<?php
session_start();

require "../includes/authentication.php";
include "../includes/misc-variables.php";

$connection = new mysqli($server, $sqlUsername, $sqlPassword, $databaseName);

if (isset($_POST["cancel"])) {
    unset($_SESSION["newSpouse"]);
    header("Location: my-profile.php");
    exit;
}

if (isset($_POST["existing"])) {

    header("Location: search-spouse.php");
    exit;
}

// Attempt to execute the update statement before fetching the data.
// Check whether one of the fields is set to determine whether the user has updated information.
if (isset($_POST["firstName"]) || isset($_POST["lastName"])) :
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $address = $_POST["address"];
    $city = $_POST["city"];
    $state = $_POST["state"];
    $zip = $_POST["zip"];
    $email = $_POST["userEmail"];
    $phoneNumber = $_POST["phoneNumber"];
    $roles = $_POST["roles"];
    $password = $_POST["password"];
    $rePassword = $_POST["rePassword"];

    // Check whether the spouse already has an entry in the database.
    $spouseQuery = "SELECT AdultID FROM ADULT WHERE Email = '$email'";
    $spouseResult = $connection->query($spouseQuery);

    if (mysqli_num_rows($spouseResult)==0) {
        $spouseId = $spouseResult->fetch_object()->AdultID;
    }

    // Check to make sure necessary fields are given
    if (empty($firstName) || empty($lastName) || empty($email) || empty($roles)) {
        ?>
        <div class="alert alert-danger">
            <Strong>Error updating information: </Strong> First Name, Last Name, Email, and Role fields must not be blank.
        </div>
        <?php
    } else if ($spouseId) {
        ?>
        <div class="alert alert-danger">
            <Strong>Person already exists: </Strong> Search for them by email in existing accounts.
        </div>
        <?php
    } else {
        if ($password == $rePassword) {
            $insertQuery = "INSERT INTO ADULT (FirstName, LastName, Address, City, State, Zip, Email, PhoneNumber) VALUES ('$firstName', '$lastName', '$address', '$city', '$state', '$zip', '$email', '$phoneNumber')";
            $insertQueryResult = $connection->query($insertQuery);

            $spouseQuery = "SELECT AdultID FROM ADULT WHERE Email = '$email'";
            $spouseResult = $connection->query($spouseQuery);
            $spouseId = $spouseResult->fetch_object()->AdultID;

            $_SESSION["spouseId"] = $spouseId;
            $accountId = $_SESSION["accountId"];

            $updateAccountQuery = "UPDATE ACCOUNT SET SpouseID = $spouseId WHERE AccountID = '$accountId'";
            $connection->query($updateAccountQuery);

            $errorMessage = $connection->error;

            $adultIDQuery = "SELECT PrimaryUserID FROM ACCOUNT WHERE AccountID = $accountId";
            $adultIDQueryResult = $connection->query($adultIDQuery);
            $adultId = $adultIDQueryResult->fetch_object()->PrimaryUserID;

            // Hash the password.
            $md5Password = md5($password);
            $addAccountQuery = "INSERT INTO ACCOUNT VALUES (NULL, $spouseId, $adultId, '$md5Password')";
            $result = $connection->query($addAccountQuery) or die("Error creating account.");

            foreach ($roles as &$role) {
                // Search for the RoleID associated with the current role.
                $query = "SELECT RoleID FROM ROLES WHERE Role = '$role'";
                $roleSearchResult = $connection->query($query);
                $roleId = $roleSearchResult->fetch_object()->RoleID;

                // Add the ROLE_REL into the database.
                $insertQuery = "INSERT INTO ROLE_REL VALUES($spouseId, $roleId)";
                $connection->query($insertQuery) or die("Error inserting into ROLE_REL table.");
            }
        } else {
            $errorMessage = "Passwords do not match";
        }
        
        if ($errorMessage || $connection->connect_errno) : ?>
            <div class="alert alert-danger">
                <Strong>Error updating information: </Strong> <?= $errorMessage; ?>
            </div>
        <?php
        else : 
            unset($_SESSION["newSpouse"]);
            ?>

            <div class="alert alert-success">
                <Strong>Success!</Strong> Your spouse has created an account.
            </div>
        <?php
        endif; ?>
    <?php
    }
endif;

// Retrieve the information currently stored in the database.
$accountId = $_SESSION["accountId"];
$accountQuery = "SELECT SpouseID FROM ACCOUNT WHERE AccountID = $accountId";

$accountQueryResult = $connection->query($accountQuery);
$adultID = $accountQueryResult->fetch_object()->SpouseID;

if ($adultID) {
    $adultInfoQuery = "SELECT d.FirstName, d.LastName, d.Address, d.City, d.State, d.Zip, d.Email, d.PhoneNumber FROM ADULT as d WHERE AdultID = $adultID";
    $data = $connection->query($adultInfoQuery);

    // fetch_object() did not behave as expected, this seems to work well enough.
    $row = $data->fetch_assoc();
}

$connection->close();

if (isset($_POST["existing"])) {
    header("Location: search-spouse.php");
    exit;
}

include('../includes/head-tag-contents.php');

if (!isset($_POST["existing"]) && !isset($_POST["new"]) && !isset($_SESSION["newSpouse"])) { ?>
    <div class="p-3">
    <form action="" method="post" name="formSignup" id="formSignup">
        <div class="form-row">
            <div class="form-group col-md-12">
                <h4 class="text-center">Is your spouse an existing member or a new member?</h4>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <button type="submit" class="btn btn-default btn-block" name="existing">Existing</button>
            </div>
            <div class="form-group col-md-6">
                <button type="submit" class="btn btn-default btn-block" name="new">New</button>
            </div>
        </div>
    </form>
</div>
<?php 
} else if (isset($_POST["new"]) || $_SESSION["newSpouse"] === true) {
    $_SESSION["newSpouse"] = true;
    if (isset($firstName)) {
        ?>
        <div class="p-3">
        <form action="" method="post" name="formSignup" id="formSignup">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="firstName">First Name:</label>
                    <input name="firstName" type="text" class="form-control" id="firstName" placeholder="John" value="<?= $firstName ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="lastName">Last Name:</label>
                    <input name="lastName" type="text" class="form-control" id="lastName" placeholder="Doe" value="<?= $lastName ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="address">Address:</label>
                    <input name="address" type="text" class="form-control" id="address" placeholder="1234 Main Street" value="<?= $address ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="city">City:</label>
                    <input name="city" type="text" class="form-control" id="city" placeholder="Cityville" value="<?= $city ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="state">State:</label>
                    <select class="form-control" id="state" name="state">
                        <option selected value="">State</option>
                        <?php foreach ($states as &$state) : ?>
                            <option value="<?= $state ?>"><?= $state ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="zip">Zip code:</label>
                    <input name="zip" type="text" class="form-control" id="zip" placeholder="12345" value="<?= $zip ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="userEmail">Email address:</label>
                    <input name="userEmail" type="email" class="form-control" id="userEmail" placeholder="john.doe@email.com" value="<?= $email ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="phoneNumber">Phone Number:</label>
                    <input name="phoneNumber" type="tel" class="form-control" id="phoneNumber" placeholder="555-555-5555" value="<?= $phoneNumber ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">Password:</label>
                    <input name="password" type="password" class="form-control" id="password" placeholder="Enter Password">
                </div>
                <div class="form-group col-md-6">
                    <label for="rePassword">Retype Password:</label>
                    <input name="rePassword" type="password" class="form-control" id="rePassword" placeholder="Retype Password">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Role:</label>
                </div>
            </div>
            <div class="form-row">
                <!-- TODO: Generate these by querying the database for roles? -->
                <div class="form-group col-md-3">
                    <label class="checkbox-inline"><input type="checkbox" name="roles[]" value="Parent">Parent</label>
                </div>
                <div class="form-group col-md-3">
                    <label class="checkbox-inline"><input type="checkbox" name="roles[]" value="Assistant">Assistant</label>
                </div>
                <div class="form-group col-md-3">
                    <label class="checkbox-inline"><input type="checkbox" name="roles[]" value="Teacher">Teacher</label>
                </div>
                <div class="form-group col-md-3">
                    <label class="checkbox-inline"><input type="checkbox" name="roles[]" value="Principal">Principal</label>
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
    <?php 
    } else if (!isset($firstName)) {
        ?>
        <div class="p-3">
        <form action="" method="post" name="formSignup" id="formSignup">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="firstName">First Name:</label>
                    <input name="firstName" type="text" class="form-control" id="firstName" placeholder="John">
                </div>
                <div class="form-group col-md-6">
                    <label for="lastName">Last Name:</label>
                    <input name="lastName" type="text" class="form-control" id="lastName" placeholder="Doe">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="address">Address:</label>
                    <input name="address" type="text" class="form-control" id="address" placeholder="1234 Main Street">
                </div>
                <div class="form-group col-md-3">
                    <label for="city">City:</label>
                    <input name="city" type="text" class="form-control" id="city" placeholder="Cityville">
                </div>
                <div class="form-group col-md-3">
                    <label for="state">State:</label>
                    <select class="form-control" id="state" name="state">
                        <option selected value="">State</option>
                        <?php foreach ($states as &$state) : ?>
                            <option value="<?= $state ?>"><?= $state ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="zip">Zip code:</label>
                    <input name="zip" type="text" class="form-control" id="zip" placeholder="12345">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="userEmail">Email address:</label>
                    <input name="userEmail" type="email" class="form-control" id="userEmail" placeholder="john.doe@email.com">
                </div>
                <div class="form-group col-md-6">
                    <label for="phoneNumber">Phone Number:</label>
                    <input name="phoneNumber" type="tel" class="form-control" id="phoneNumber" placeholder="555-555-5555">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">Password:</label>
                    <input name="password" type="password" class="form-control" id="password" placeholder="Enter Password">
                </div>
                <div class="form-group col-md-6">
                    <label for="rePassword">Retype Password:</label>
                    <input name="rePassword" type="password" class="form-control" id="rePassword" placeholder="Retype Password">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Role:</label>
                </div>
            </div>
            <div class="form-row">
                <!-- TODO: Generate these by querying the database for roles? -->
                <div class="form-group col-md-3">
                    <label class="checkbox-inline"><input type="checkbox" name="roles[]" value="Parent">Parent</label>
                </div>
                <div class="form-group col-md-3">
                    <label class="checkbox-inline"><input type="checkbox" name="roles[]" value="Assistant">Assistant</label>
                </div>
                <div class="form-group col-md-3">
                    <label class="checkbox-inline"><input type="checkbox" name="roles[]" value="Teacher">Teacher</label>
                </div>
                <div class="form-group col-md-3">
                    <label class="checkbox-inline"><input type="checkbox" name="roles[]" value="Principal">Principal</label>
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
    <?php 
    }
}   ?>