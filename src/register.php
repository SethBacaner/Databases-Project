<?php

session_start();

if( isset($_SESSION['user_id']) ){
  header("Location: /home.php");
}

require 'database.php';

//Variables
$fsuid = array_key_exists('fsuid', $_POST) ? $_POST['fsuid'] : null;
$password = array_key_exists('password', $_POST) ? $_POST['password'] : null;
$confirm_password = array_key_exists('confirm_password', $_POST) ? $_POST['confirm_password'] : null;
$fname = array_key_exists('fname', $_POST) ? $_POST['fname'] : null;
$lname = array_key_exists('lname', $_POST) ? $_POST['lname'] : null;
$dob = array_key_exists('dob', $_POST) ? $_POST['dob'] : null;

$message = '';
$success = 'Successfully created new user.';
$fail = 'Sorry, there was an issue creating your account.';

if(!empty($fsuid) && !empty($password)):

  //Enter the new user into database
  $sql = "INSERT INTO student (fsuid, fname, lname, dob, userpassword)
                VALUES (:fsuid, :fname, :lname, :dob, :password)";
  $stmt = $conn->prepare($sql);

  $stmt->bindParam(':fsuid', $_POST['fsuid']);
  $stmt->bindParam(':fname', $_POST['fname']);
  $stmt->bindParam(':lname', $_POST['lname']);
  $stmt->bindParam(':dob', $_POST['dob']);
  $stmt->bindParam(':password', password_hash($_POST['password'], PASSWORD_BCRYPT));

 if ( $stmt->execute() ){
   $message = $success;
 }else
  $message = $fail;
endif;

?>

<!DOCTYPE html>
<html>
<head>
  <title>Register Below</title>
  <link rel="stylesheet" type="text/css" href="/assets/my_style.css">
  <link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">
</head>
<body>

<div id="wrap">
  <div id="main">
    <!-- Global Header Area-->
    <div class="global">
      <div class="container-global">
         <div id="home">
          <p class="fsu"><a href="http://www.fsu.edu">Florida State University</a></p>
         </div>
      </div>
    </div>

    <!-- Header -->
    <div class="header">
      <div class="container-header">
        <div class="twentyfour">
          <div id="fsu-seal">
            <a href="/">
              <img src="/assets/images/fsu_logo.png" alt="Florida State University Seal">
            </a>
          </div>
          <p class="header-title">University Online Advisor</p>
        </div>
      </div>
    </div>

    <div class="separator">
      <div class="container-plain">
        <div class="nav">
          <ul class="nav-main">

          </ul>
        </div>
      </div>
    </div>

    <!-- Main Area -->
    <h1>Register</h1>
    <span>or <a href="http://localhost/login.php">login</a></span>

    <?php if(!empty($message)): ?>
      <p><?= $message ?></p>
    <?php endif; ?>


    <form action="register.php" method="POST">

      <input type="text" placeholder="Enter your FSUID" name="fsuid">
      <input type="password" placeholder="Enter you password" name="password">
      <input type="password" placeholder="Confirm Password" name="confirm_password">
      <input type="text" placeholder="Enter your first name" name="fname">
      <input type="text" placeholder="Enter your last name" name="lname">
      <input type="text" placeholder="Enter your date of birth in YYYY-MM-DD format" name="dob">
      <input type="submit" name="submit">

    </form>

    <!-- Footer -->
  </div>
</div>


</body>
</html>
