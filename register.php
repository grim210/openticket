<?php

/*
* Contains $ot_connstring.
*/
require '_secret.php';

function password_error() {
echo <<<_password
<!doctype html>
<html>
  <head>
    <title>Register</title>
  </head>
  <body>
    <h1>Register</h1>
    <p>Try again.  Your passwords did not match.</p>
    <form action="/register.php" method="post">
      Username:<br>
      <input type="text" name="username">
      <br>
      Password:<br>
      <input type="password" name="password">
      <br>
      Repeat Password:<br>
      <input type="password" name="repeat">
      <br>
      <input type="submit" value="Register">
    </form>
  </body>
</html>
_password;
}

function register_user($username, $pwhash) {
	$connstring = get_connection_string();

	$dbconn = pg_connect($connstring) or die ("$connstring");
	$query = "insert into ot_users "
		. "(user_name, user_hash, user_created) "
		. "values"
		. "('$username', '$pwhash', NOW())";

	$result = pg_query($dbconn, $query);
	if ($result == FALSE) {
		$err = pg_last_error($dbconn);
		echo "<html><body><p>$err</p></body></html>";
	} else {
		echo '<html><body><p>Success! <a href="/index.php">'
			. 'Home</a></body?</html>';
	}

	$pg_close($dbconn);
}

/* Presents a form to the user if there is no post data available. */
function no_post() {
echo <<<_nopost
<!doctype html>
<html>
  <head>
    <title>Register</title>
  </head>
  <body>
    <h1>Register</h1>
    <form action="/register.php" method="post">
      Username:<br>
      <input type="text" name="username">
      <br>
      Password:<br>
      <input type="password" name="password">
      <br>
      Repeat Password:<br>
      <input type="password" name="repeat">
      <br>
      <input type="submit" value="Register">
    </form>
  </body>
</html>
_nopost;
}

/* Check if there's post data; dispatch accordingly */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_POST['username'];
	$pwhash = password_hash($_POST['password'], PASSWORD_DEFAULT);

	if ($_POST['password'] != $_POST['repeat']) {
		password_error();
	} else {
		register_user($username, $pwhash);
	}
} else {
	no_post();
}
?>
