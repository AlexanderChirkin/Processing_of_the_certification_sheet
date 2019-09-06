<?php 
  require_once 'login.php';
  require_once 'encrypt.php';
  require_once 'functions.php';
  $connection = new mysqli($hostname, $username, $password, $database);

  if ($connection->connect_error) die("Fatal Error");

  query_mysql($connection, "SET NAMES utf8");

  $email = $password = '';
  
  if (isset($_POST['email']) && isset($_POST['password']))
  {
	echo($_POST['email']."<br>");
	echo($_POST['password']."<br>");
    $email_temp = mysql_entities_fix_string($connection, $_POST['email']);
    $password_temp = mysql_entities_fix_string($connection, $_POST['password']);
    $query   = "SELECT * FROM user WHERE email='$email_temp'";
    $result  = $connection->query($query);
	
    if (!$result) die("User not found");
    elseif ($result->num_rows)
    {
      $row = $result->fetch_array(MYSQLI_ASSOC);

      $result->close();
	  echo (new_token($password_temp)."<br>");
	  echo ($row['surname']."<br>");
      if (new_token($password_temp) == $row['password'])#(password_verify($password_temp, $row[1]))
      {
        session_start();
        $_SESSION["user_email"] = $email_temp;
        $_SESSION['role'] = $row['role'];
        $_SESSION['surname'] = $row['surname'];
        $_SESSION['name'] = $row['name'];
		#echo ("<br> $_SESSION['username']");
		header("Location: index.php");
		
        //die ("<p><a href='continue.php'>Click here to continue</a></p>");
      }
      else die("Invalid username/password combination<br>");
    }
    else die("Invalid username/password combination a lot<br>");
  }
  else
  {
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    die ("Please enter your username and password");
  }

  $connection->close();

  function mysql_entities_fix_string($connection, $string)
  {
    return htmlentities(mysql_fix_string($connection, $string));
  }	

  function mysql_fix_string($connection, $string)
  {
    if (get_magic_quotes_gpc()) $string = stripslashes($string);
    return $connection->real_escape_string($string);
  }
?>
