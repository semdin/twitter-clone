<?php
// Include the database connection file
require_once "connection.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input from the registration form
    $name = $_POST["name"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Create a SQL query to insert the user into the "Users" table
    $sql = "INSERT INTO Users (name, username, password) VALUES ('$name', '$username', '$password')";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        header("Location: index.html");
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
