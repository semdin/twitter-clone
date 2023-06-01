<?php
// Include the database connection file
require_once "connection.php";

// Start the session
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["username"])) {
    // Redirect the user to the login page
    header("Location: login.php");
    exit();
}

// Check if the follower ID and following ID are provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["follower_id"]) && isset($_POST["following_id"])) {
    // Retrieve the follower ID and following ID
    $followerID = $_POST["follower_id"];
    $followingID = $_POST["following_id"];

    // Create a SQL query to check if the user is already following
    $checkQuery = "SELECT * FROM Follows WHERE follower_id = '$followerID' AND following_id = '$followingID'";

    // Execute the query
    $checkResult = mysqli_query($conn, $checkQuery);

    // Check if the user is already following
    if (mysqli_num_rows($checkResult) > 0) {
        // User is already following, display an error message and redirect to HomePage.php
        $errorMessage = "You are already following this user.";
        header("Location: homepage.php?error=" . urlencode($errorMessage));
        exit();
    } else {
        // Create a SQL query to insert the new follow record
        $insertQuery = "INSERT INTO Follows (follower_id, following_id) VALUES ('$followerID', '$followingID')";

        // Execute the query
        $insertResult = mysqli_query($conn, $insertQuery);

        // Check if the follow record was inserted successfully
        if ($insertResult) {
            // Follow record inserted successfully, you can update the Users table if needed

            // Redirect the user to HomePage.php
            header("Location: homepage.php");
            exit();
        } else {
            // Follow record insertion failed, display an error message and redirect to HomePage.php
            $errorMessage = "Follow operation failed. Please try again.";
            header("Location: homepage.php?error=" . urlencode($errorMessage));
            exit();
        }
    }

    // Free the result set
    mysqli_free_result($checkResult);
}

// Close the database connection
mysqli_close($conn);
?>
