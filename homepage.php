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

// Retrieve the username from the session
$username = $_SESSION["username"];

// Process the search query
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the searched username
    $searchedUsername = $_POST["searchedUsername"];

    // Create a SQL query to search for the user
    $searchQuery = "SELECT * FROM Users WHERE username = '$searchedUsername'";

    // Execute the query
    $searchResult = mysqli_query($conn, $searchQuery);

    // Check if the user is found
    if (mysqli_num_rows($searchResult) == 1) {
        // User found
        $foundUser = mysqli_fetch_assoc($searchResult);
    } else {
        // User not found
        $notFoundMessage = "User not found.";
    }

    // Free the search result set
    mysqli_free_result($searchResult);
}

// Create a SQL query to retrieve the tweets of the followed users
$tweetsQuery = "SELECT T.content, T.created_at, U.username
                FROM Tweets T
                INNER JOIN Users U ON T.user_id = U.user_id
                WHERE T.user_id IN (
                    SELECT following_id
                    FROM Follows
                    WHERE follower_id = (SELECT user_id FROM Users WHERE username = '$username')
                )
                ORDER BY T.created_at DESC";


// Execute the query
$tweetsResult = mysqli_query($conn, $tweetsQuery);

// Fetch all the tweets as an associative array
$tweets = mysqli_fetch_all($tweetsResult, MYSQLI_ASSOC);

// Free the tweets result set
mysqli_free_result($tweetsResult);

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <h1>Welcome, <?php echo $username; ?>!</h1>
    <?php
    // Retrieve the error message from the query parameter, if it exists
    $error = isset($_GET["error"]) ? $_GET["error"] : "";

    // Display the error message, if available
    if (!empty($error)) {
        echo "<p class='error-message'>$error</p>";
    }
    ?>
    <div class="container">
        <h2>Search Users</h2>
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <div class="form-group">
                <label for="searchedUsername">Username:</label>
                <input type="text" id="searchedUsername" name="searchedUsername" required />
            </div>
            <div class="form-group">
                <input type="submit" value="Search" />
            </div>
        </form>

        <?php if (isset($foundUser)) { ?>
            <div class="user-info">
                <p><b>Username:</b> <?php echo $foundUser["username"]; ?></p>
                <p><b>Full name:</b> <?php echo $foundUser["name"]; ?></p>
                <!-- Add more user information here -->

                <!-- Add follow button here -->
                <form method="post" action="follow.php">
                    <input type="hidden" name="follower_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="following_id" value="<?php echo $foundUser["user_id"]; ?>">
                    <input type="submit" value="Follow">
                </form>
            </div>
        <?php } elseif (isset($notFoundMessage)) { ?>
            <p><?php echo $notFoundMessage; ?></p>
        <?php } ?>

        <h2>Recent Tweets</h2>
        <?php if (!empty($tweets)) { ?>
            <?php foreach ($tweets as $tweet) { ?>
                <p><strong>Username: </strong><?php echo $tweet["username"]; ?></p>
                <p><strong>Date: </strong><?php echo $tweet["created_at"]; ?></p>
                <p><strong>Content: </strong><?php echo $tweet["content"]; ?></p>
                <hr>
            <?php } ?>
        <?php } else { ?>
            <p>No tweets found.</p>
        <?php } ?>
    </div>
    <button><a href="profile.php">Profile</a></button>
    <button><a href="logout.php">Logout</a></button>
</body>
</html>
