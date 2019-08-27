<?php
  session_start();

  require 'database.php';

  if( isset($_SESSION['user_id']) ){
    $records = $conn->prepare('SELECT fsuid,fname,lname,dob FROM student WHERE fsuid = :fsuid');
    $records->bindParam(':fsuid', $_SESSION['user_id']);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);

    $user = NULL;

    if(count($results) > 0){
      $user = $results;
    }
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?= $user['fsuid']; ?></title>
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

      <?php if( !empty($user) ): ?>
	  <?php if( $user['fsuid']=="admin" ){
				header("Location: /adminhome.php");
	  } else { ?>
      <br /> welcome <?= $user['fname']; ?> <?= $user['lname']; ?>

      <h1>Choose from the following menu</h1>

    <form action="/usercourses.php">
      <input type="submit" value="View or update your courses to date">
    </form>

<!-- depracated by usercourses.php
    <form action="/pastclasses.php">
      <input type="submit" value="See what you have taken" />
    </form>

    <form action="/addcourse.php">
      <input type="submit" value="Update your progress" />
    </form>
-->

    <form action="/neededclasses.php">
      <input type="submit" value="See what you still need" />
    </form>

    <form action="/updateaccount.php">
      <input type="submit" value="View account information or change password" />
    </form>

    <form action="/logout.php">
      <input type="submit" value="Logout" />
    </form>
      <p>

      </p>
	  <?php } else:  ?>
      <?php header("Location: /login.php"); ?>
    <?php endif; ?>
  </div>
</div>
  </body>
</html>
