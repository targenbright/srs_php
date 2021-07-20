<?php

// include "development-credentials.php"; // For development purposes
include "production-credentials.php";

// $conn = new mysqli($server, $sqlUsername, $sqlPassword, $databaseName);

function authenticateUser($connection, $username, $password)
{
    if (!isset($username) || !isset($password))
        return false;

    $pa = md5($password);
    $sql = "SELECT * FROM ACCOUNT AS a, ADULT AS d WHERE a.PrimaryUserID = d.AdultID AND d.Email = '$username' AND a.Password = '$pa'";

    $query_result = $connection->query($sql);

    if (!$query_result)
        echo "Invalid query: " . $sql;

    // Found one profile that matches the userId and password.
    return $query_result->num_rows == 1;
}
