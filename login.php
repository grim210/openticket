<?php

require '_secret.php';
session_start();

/*
* First, let's keep track of how many times the user has attempted
* to log in with this browser session.  To slow down brute-force.
*/
if (!isset($_SESSION['login_attempts'])) {
	$_SESSION['login_attempts'] = 0;
}

function login_attempt($username, $password) {
	$connstring = get_connection_string();

	$dbconn = pg_connect($connstring) or die ("$connstring");
	$query = "select user_hash from ot_users "
		. "where user_name='$username'";

	$result = pg_query($dbconn, $query);
	if ($result == false) {
		$err = pg_last_error($dbconn);
		echo "<html><body><p>$err</p></body></html>";
		exit(1);
	}

	$line = pg_fetch_array($result, 0, PGSQL_NUM);
	if ($line == false) {
		$_SESSION['authenticated'] = false;
		$_SESSION['reason'] = "Username not found.";
		return false;
	}

	$hash = $line[0];
	if (password_verify($password, $hash) == false) {
		$_SESSION['authenticated'] = false;
		$_SESSION['reason'] = "Invalid credentials.";
		return false;
	}

	$_SESSION['authenticated'] = true;
	$_SESSION['username'] = $username;
	return true;
}

function write_login($attempt, $msg) {
	
	/*
	* If this is your second failed login, you're going to start
	* waiting a second between attempts.  This will hopefully
	* prevent attempting to brute-force or DoS the application.
	*/
	if ($attempt > 1) {
		sleep(1);
	}

echo <<<_LOGIN
<!doctype html>
<html>
  <head>
    <title>Login</title>
  </head>
  <body>
    <h1>Login</h1>
_LOGIN;
	/* If a message was passed, ensure we display it. */
	if (isset($msg)) {
		echo "    <p>$msg</p>\n";
	}
echo <<<_LOGIN2
    <form action="login.php" method="post">
      Username:<br>
      <input type="text" name="username"><br>
      Password:<br>
      <input type="password" name="password"><br>
      <input type="submit" value="Login">
    </form>
    <p><a href="/register.php">Register</a></p>
  </body>
</html>
_LOGIN2;
}

/*
* First, let's check to see that the user isn't already
* authenticated. If they are, redirect back to the index page.
*/
if (isset($_SESSION)) {
	if (isset($_SESSION['authenticated']) and
		($_SESSION['authenticated'] == true)) {

		echo "<html><body><h1>Authenticated</h1>"
			. "<p>Redirecting...</p></body></html>";

		/* Set this back to zero, just to be safe. */
		$_SESSION['login_attempts'] = 0;

		/*
		* Give them time to read that they're already
		* authenticated. Then redirect to the homepage.
		*/
		sleep(1);
		header('Location: /index.php');
	}
}

/* If there's post data, try to authenticate */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$un = $_POST['username'];
	$pw = $_POST['password'];

	$ret = login_attempt($un, $pw);
	if ($ret == true) {
		$_SESSION['login_attempts'] = 0;
		header('Location: /dump.php');
	} else {
		$_SESSION['login_attempts'] += 1;
	}
}

write_login($_SESSION['login_attempts'], $_SESSION['reason']);

?>
