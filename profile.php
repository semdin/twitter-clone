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

// Create a SQL query to retrieve the user's information
$userQuery = "SELECT * FROM Users WHERE username = '$username'";

// Execute the query
$userResult = mysqli_query($conn, $userQuery);

// Check if the user exists
if (mysqli_num_rows($userResult) > 0) {
    // Fetch the user's information
    $userInfo = mysqli_fetch_assoc($userResult);
    
    // Get the user's ID
    $userID = $userInfo["user_id"];
    
    // Create a SQL query to retrieve the number of followers
    $followersQuery = "SELECT COUNT(*) AS num_followers FROM Follows WHERE following_id = '$userID'";
    
    // Execute the query
    $followersResult = mysqli_query($conn, $followersQuery);
    
    // Fetch the number of followers
    $numFollowers = mysqli_fetch_assoc($followersResult)["num_followers"];
    
    // Create a SQL query to retrieve the number of following
    $followingQuery = "SELECT COUNT(*) AS num_following FROM Follows WHERE follower_id = '$userID'";
    
    // Execute the query
    $followingResult = mysqli_query($conn, $followingQuery);
    
    // Fetch the number of following
    $numFollowing = mysqli_fetch_assoc($followingResult)["num_following"];

    // Create a SQL query to retrieve the total number of tweets for the user
    $totalTweetsQuery = "SELECT COUNT(*) AS total_tweets FROM Tweets WHERE user_id = '$userID'";
    
    // Execute the query
    $totalTweetsResult = mysqli_query($conn, $totalTweetsQuery);
    
    // Fetch the total number of tweets
    $totalTweets = mysqli_fetch_assoc($totalTweetsResult)["total_tweets"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile Page</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <h1>Welcome, <?php echo $userInfo["name"]; ?>!</h1>
    <p><b>Username:</b> <?php echo $username; ?></p>
    <p><b>Tweets:</b> <?php echo $totalTweets; ?></p>
    <p><b>Creation Date:</b> <?php echo $userInfo["created_at"]; ?></p>
    <p><b>Followers:</b> <?php echo $numFollowers; ?></p>
    <p><b>Following:</b> <?php echo $numFollowing; ?></p>
    
    <h2>Write a Tweet</h2>
    <form method="post" action="tweet.php">
        <textarea name="content" rows="4" cols="50" required></textarea>
        <br>
        <input type="submit" value="Post Tweet">
    </form>
    
    <h2>Your Tweets</h2>
    <?php
    // Create a SQL query to retrieve the user's tweets in descending order
    $tweetsQuery = "SELECT * FROM Tweets WHERE user_id = '$userID' ORDER BY created_at DESC";
    
    // Execute the query
    $tweetsResult = mysqli_query($conn, $tweetsQuery);
    
    // Check if the user has any tweets
    if (mysqli_num_rows($tweetsResult) > 0) {
        while ($tweet = mysqli_fetch_assoc($tweetsResult)) {
            echo "<p><strong>Content: </strong>" . $tweet["content"] . "</p>";
            echo "<p><strong>Date: </strong>" . $tweet["created_at"] . "</p>";
            echo "<hr>";
        }
    } else {
        echo "<p>No tweets found.</p>";
    }
    ?>
    <button><a href="homepage.php">Homepage</a></button>
    <button><a href="logout.php">Logout</a></button>
    
</body>
</html>

<?php
} else {
    // User not found
    echo "User not found.";
}

// Close the database connection
mysqli_close($conn);
?>
