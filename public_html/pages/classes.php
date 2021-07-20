<?php
session_start();
require "../includes/authentication.php";
include "../includes/helper-functions.php";

$adultId = $_SESSION["adultId"];
$connection = createNewConnection();

if (isset($_POST["addCourses"])) {
    $_SESSION["addCourseChildId"] = $_POST["addCourses"];
    header("Location: add-class.php");
}

if (isset($_POST["removeCourses"]) && isset($_POST["courseId"])) {
    $imploded = implode(", ", $_POST["courseId"]);
    $childId = $_POST["removeCourses"];
    $query = "DELETE FROM ENROLLMENT WHERE ActivityID IN ($imploded) AND ChildID = $childId";
    $connection->query($query);
}

$childQuery = "SELECT c.ChildID, c.FirstName, c.LastName FROM CHILD AS c, PAR_CH_REL AS r WHERE r.AdultID = $adultId AND r.ChildID = c.ChildID";
$childResult = $connection->query($childQuery);

include('../includes/head-tag-contents.php');
?>

<body>
    <div class="p-3">
        <?php
        if ($childResult->num_rows == 0) {
            echo "<h3> No children are associated with this account</h3>";
        }
        foreach ($childResult as $c) {
            $childId = $c["ChildID"];
            $firstName = $c["FirstName"];
            $lastName = $c["LastName"];
            $enrolledClassQuery = "SELECT a.Name, a.Room, a.BeginTime, a.EndTime, REPLACE(REPLACE(a.RegularClass, 0, 'After School'), 1, 'Standard') AS CourseType, a.Cost, a.ActivityID FROM ACTIVITY AS a, ENROLLMENT AS e WHERE e.ChildID=$childId AND e.ActivityID = a.ActivityID";
        ?>
            <h3> <?= $firstName . " " . $lastName ?></h3><br>
            <?php
            $result = $connection->query($enrolledClassQuery);
            // Need to create a queryToTable with delete option tied to EnrollmentID entry.
            // queryToSelectionTable($result);
            ?>
            <!-- Tie button submit/value to ChildID -->
            <form action="" method="post">
                <?php queryToSelectionTable($result); ?>
                <button type="submit" class="btn btn-success" name="addCourses" value="<?= $childId ?>">Add Courses</button>
                <button type="submit" class="btn btn-danger" name="removeCourses" value="<?= $childId ?>">Remove Courses </button>
            </form>
        <?php
        } ?>
        <!-- View courses by child and provide an Add Classes button -->
    </div>
</body>

<?php

$connection->close();
?>