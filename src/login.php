<?php

// always need this to start a session
session_start();

if( isset($_SESSION['user_id']) ){
  header("Location: /home.php");
}

require 'database.php';

if(!empty($_POST['fsuid']) && !empty($_POST['password'])):

  $records = $conn->prepare('SELECT fsuid, userpassword FROM student WHERE fsuid = :fsuid');
  $records->bindParam(':fsuid', $_POST['fsuid']);
  $records->execute();
  $results = $records->fetch(PDO::FETCH_ASSOC);

  $message = '';

  if(count($results) > 0 && password_verify($_POST['password'], $results['userpassword']) ){
    $_SESSION['user_id'] = $results['fsuid'];
    header("Location: /home.php");
  } else {
    $message = "Invalid fsuid and/or password";
  }

endif;

?>

<!DOCTYPE html>
<html>
<head>
  <title>Login Below</title>
  <link rel="stylesheet" type="text/css" href="http://localhost/assets/my_style.css">
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
              <img src="http://localhost/assets/images/fsu_logo.png" alt="Florida State University Seal">
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
    <h1>Login</h1>
    <span>or <a href="http://localhost/register.php">register</a></span>

    <?php if(!empty($message)): ?>
      <p><?= $message ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">

      <input type="text" placeholder="Enter your FSUID" name="fsuid">
      <input type="password" placeholder="Enter you password" name="password">

      <input type="submit" name="submit">

    </form>

    <!-- Footer -->
  </div>
</div>

</body>
</html>
