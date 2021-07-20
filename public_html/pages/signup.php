<?php
session_start();

require "../includes/authentication.php";
include "../includes/misc-variables.php";

$errorMessage = "";

include('../includes/head-tag-contents.php');

if (
    isset($_POST["userEmail"]) && isset($_POST["password"]) && isset($_POST["rePassword"])
) {
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

    // $fullAddress = $address . " " . $city . ", " . $state . " " . $zip;

    if ($password == $rePassword) {
        $conn = new mysqli($server, $sqlUsername, $sqlPassword, $databaseName);

        /** Check whether the provided email is
         * associated with a primary account user.
         */
        $existingAccountQuery = "SELECT a.Email, c.AccountID FROM ADULT AS a, ACCOUNT AS c WHERE c.PrimaryUserID = a.AdultID AND a.Email = '$email'";
        $existingUserQuery = "SELECT AdultID FROM ADULT WHERE Email = '$email'";
        $addAdultQuery = "INSERT INTO ADULT VALUES (NULL, '$lastName', '$firstName', '$address', '$city', '$state', '$zip', '$phoneNumber', '$email')";

        $existingAccountSearchResult = $conn->query($existingAccountQuery);
        $existingUserSearchResult = $conn->query($existingUserQuery);


        if ($existingAccountSearchResult->num_rows > 0) {
            $errorMessage = "An account already exists with a primary user associated with the provided email address and phone number.";
        } else {
            if ($existingUserSearchResult->num_rows == 0) {
                // No entry exists for the adult. Must add and then query again.
                $result = $conn->query($addAdultQuery) or die("Error adding ADULT data into database.");
                // Search again for the newly created ADULT entry.
                $existingUserSearchResult = $conn->query($existingUserQuery);
            }

            // Get the AdultID to use in the PrimaryUserID field of ACCOUNT table.
            $adultId = $existingUserSearchResult->fetch_object()->AdultID;
            // Hash the password.
            $md5Password = md5($password);
            $addAccountQuery = "INSERT INTO ACCOUNT (PrimaryUserID, Password) VALUES ($adultId, '$md5Password')";
            $result = $conn->query($addAccountQuery) or die("Error creating account.");

            foreach ($roles as &$role) {
                // Search for the RoleID associated with the current role.
                $query = "SELECT RoleID FROM ROLES WHERE Role = '$role'";
                $roleSearchResult = $conn->query($query);
                $roleId = $roleSearchResult->fetch_object()->RoleID;

                // Add the ROLE_REL into the database.
                $insertQuery = "INSERT INTO ROLE_REL VALUES($adultId, $roleId)";
                $conn->query($insertQuery) or die("Error inserting into ROLE_REL table.");
            }

?>
            <div class="alert alert-success">
                <Strong>Success!</Strong> Your account was created successfully. Click <a href="login.php">here</a> to log in.
            </div>
    <?php
        }
    } else {
        $errorMessage = "Passwords do not match";
    }
}
if ($errorMessage) :
    ?>
    <div class="alert alert-danger">
        <Strong>Error creating account: </Strong> <?= $errorMessage ?>
    </div>
<?php endif; ?>
<div class="container-fluid">
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
                    <input name="phoneNumber" type="tel" class="form-control" id="phoneNumber" placeholder=555-555-5555>
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
                <div class="form-group col-md-12">
                    <button type="submit" class="btn btn-success btn-block">Sign up</button>
                </div>
            </div>
        </form>
    </div>
</div>