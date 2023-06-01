<?php
// Include the database connection file
require_once "connection.php";

// Start the session
session_start();

// Check if the user is already logged in
if (isset($_SESSION["username"])) {
    // Redirect the user to the home page
    header("Location: homepage.php");
    exit();
}

// Check if the login form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Create a SQL query to check the username and password
    $query = "SELECT * FROM Users WHERE username = '$username' AND password = '$password'";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if the login is successful
    if (mysqli_num_rows($result) == 1) {
        // Fetch the user data
        $user = mysqli_fetch_assoc($result);

        // Store the username and user ID in the session
        $_SESSION["username"] = $user["username"];
        $_SESSION["user_id"] = $user["user_id"];

        // Redirect the user to the home page
        header("Location: homepage.php");
        exit();
    } else {
        // Invalid credentials
        $errorMessage = "Invalid username or password.";
    }

    // Free the result set
    mysqli_free_result($result);
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Login</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="container">
      <div class="card">
        <h1>Login</h1>
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
          <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required />
          </div>
          <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required />
          </div>
          <?php if (isset($errorMessage)) { ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
          <?php } ?>
          <div class="form-group">
            <input type="submit" value="Login" />
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
