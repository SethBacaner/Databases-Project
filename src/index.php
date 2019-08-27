<?php

// always need this to start a session
session_start();

// go to users page if there is an active login
if( isset($_SESSION['user_id']) ){
  header("Location: /home.php");
}

require 'database.php';
?>
<!DOCTYPE html>
<html>
<head>
  <title>FSU Online Advisor</title>
  <link rel="stylesheet" type="text/css" href="http://localhost/assets/my_style.css">
  <link href="https://fonts.googleapis.com/css?family=Adobe-" rel="stylesheet">
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

  <div class="main-area">
    <div class="motto">
      <h2>
        Advise yourself.
      </h2>
      <h3>
        Sign in with FSU Blackboard ID.
      </h3>
    </div>
    <div class="user one">
    </div>
     <!-- <h1>Please Login or Register</h1> -->
     <div>
       <!-- <a href="login.php">login</a> or
       <a href="register.php">register</a> -->
     </div>
	<br />
	<p>Enter all of your past and present coursework into this application and a schedule for next semester will be generated based on your preferences and needed courses.</p>

	<br />
	<form class="form-login" action="login.php">
		<input type="submit" value="Continue to login">
	</form>

	<form class="form-login" action="register.php">
		<input type="submit" value="Or register">
	</form>

  </div>

    <!-- Footer -->
  </div>
</div>
</body>
</html>
