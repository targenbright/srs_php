<?php
session_start();
require "../includes/authentication.php";
include "../includes/misc-variables.php";
include "../includes/helper-functions.php";

if (isset($_POST["cancel"])) {
    header("Location: my-children.php");
    exit;
}

if (isset($_POST["firstName"]) && isset($_POST["lastName"])) {

    $connection = new mysqli($server, $sqlUsername, $sqlPassword, $databaseName);

    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $grade = $_POST["grade"];

    // Check whether child has already been added to the database.
    $childExistsQuery = "SELECT * FROM CHILD WHERE FirstName='$firstName' AND LastName='$lastName' AND DateOfBirth='$dob' AND Gender='$gender' AND GradeLevel='$grade'";
    $existsQuery = $connection->query($childExistsQuery);

    if (empty($firstName) || empty($lastName)) {
        $errorMessage = "First Name and Last Name fields must not be blank.";
    } else if ($existsQuery->num_rows > 0) {
        $errorMessage = "Child already exists.";
    } else {
        // Create the child entry.
        $query = "INSERT INTO CHILD(FirstName, LastName, DateOfBirth, Gender, GradeLevel) VALUES('$firstName', '$lastName', '$dob', '$gender', '$grade')";
        $connection->query($query);

        $lookupChildQuery = $connection->query($childExistsQuery);
        $childId = $lookupChildQuery->fetch_object()->ChildID;
        $adultId = $_SESSION["adultId"];
        // Link the child to the parent.
        $addRelationQuery = "INSERT INTO PAR_CH_REL VALUES($adultId, $childId)";
        $connection->query($addRelationQuery);

        // Check whether the adult is already identified as a parent.
        $parentQuery = "SELECT * FROM ROLES AS r, ROLE_REL AS rr, ADULT AS a WHERE rr.AdultID = $adultId AND rr.AdultID = a.AdultID AND rr.RoleID = r.RoleID AND r.Role = 'Parent'";
        $parentResult = $connection->query($parentQuery);
        if ($parentResult->num_rows == 0) {
            $parentRoleIDQuery = "SELECT * FROM ROLES WHERE Role = 'Parent'";
            $parentRoleResult = $connection->query($parentRoleIDQuery);
            $roleId = $parentRoleResult->fetch_object()->RoleID;
            $addRoleQuery = "INSERT INTO ROLE_REL VALUES ($adultId, $roleId)";
            $connection->query($addRoleQuery);
        }
    }
    $connection->close();

    if (isset($errorMessage) || $connection->connect_errno) : ?>
        <div class="alert alert-danger">
            <Strong>Error updating information: </Strong> <?= $errorMessage; ?>
        </div>
    <?php
    else :
    ?>

        <div class="alert alert-success">
            <Strong>Success!</Strong> Your child's information has been added.
        </div>
<?php
    endif;
} 

include('../includes/head-tag-contents.php');

?>

<div class="p-3">
    <form action="" method="post" name="formAddChild" id="formAddChild">
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
            <div class="form-group col-md-4">
                <label for="dob">Date of Birth:</label>
                <input name="dob" type="date" class="form-control" id="dob">
            </div>
            <div class="form-group col-md-4">
                <label for="gender">Gender:</label>
                <select name="gender" type="text" class="form-control" id="gender">
                    <option selected value="">Gender</option>
                    <?php foreach ($genders as $k => $v) : ?>
                        <option value="<?= $k; ?>"> <?= $v; ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="grade">Grade:</label>
                <select name="grade" type="number" class="form-control" id="grade">
                    <option selected value="">Select grade</option>
                    <?php foreach ($gradeLevels as $g) : ?>
                        <option value="<?= $g ?>"><?= $g ?> </option>
                    <?php endforeach; ?>
                </select>
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