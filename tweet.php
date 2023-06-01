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

// Get the logged-in user's username from the session
$username = $_SESSION["username"];

// Get the content of the tweet from the form data
$content = $_POST["content"];

// Prepare a SQL statement to insert the tweet into the Tweets table
$insertQuery = "INSERT INTO Tweets (user_id, content) VALUES ((SELECT user_id FROM Users WHERE username = ?), ?)";

// Create a prepared statement
$stmt = mysqli_prepare($conn, $insertQuery);

// Bind the parameters
mysqli_stmt_bind_param($stmt, "ss", $username, $content);

// Execute the statement
if (mysqli_stmt_execute($stmt)) {
    // Redirect the user to the profile page after successfully posting the tweet
    header("Location: profile.php");
    exit();
} else {
    // Display an error message if the tweet couldn't be inserted
    echo "Error: " . mysqli_error($conn);
}

// Close the statement
mysqli_stmt_close($stmt);

// Close the database connection
mysqli_close($conn);
?>
