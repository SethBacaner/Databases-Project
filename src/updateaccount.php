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
 
 
	$newpass = array_key_exists('newpass', $_POST) ? $_POST['newpass'] : null;
	$newpassconfirm = array_key_exists('newpassconfirm', $_POST) ? $_POST['newpassconfirm'] : null;
	$oldpass = array_key_exists('oldpass', $_POST) ? $_POST['oldpass'] : null;
	$oldpassconfirm = array_key_exists('oldpassconfirm', $_POST) ? $_POST['oldpassconfirm'] : null;

	// store current fsuid locally
	$currfsuid = $user['fsuid'];
	
	$message = '';
	$success = 'Password changed';
	$newandoldsame = 'New password and old password were the same';
	$newdoesntmatch = 'New password and confirm new password must be the same';
	$olddoesntmatch = 'Old password and confirm old password must be the same';
	$wrongoldpass = 'That was not your current password';
	$sqlfailure = 'Unable to update password: ';
	
	if(!empty($newpass) && !empty($newpassconfirm) && !empty($oldpass) && !empty($oldpassconfirm) ):
		if($newpass != $newpassconfirm)
			$message = $newdoesntmatch;
		elseif($oldpass != $oldpassconfirm)
			$message = $olddoesntmatch;
		elseif($newpass == $oldpass)
			$message = $newandoldsame;
		else{
			mysql_connect("localhost","root","");
            mysql_select_db("fsuenrollmentdb");
			// get old password, result is encrypted hash of original input
			$passquery = mysql_query("SELECT student.userpassword FROM student
										WHERE student.fsuid = '$currfsuid' LIMIT 1;");

			$passqueryrow = mysql_fetch_array($passquery);
			$dboldpass = $passqueryrow['userpassword'];

			// check if these are the same	
			if(password_verify($oldpass,$dboldpass)){
				// they are the same, encrypt new pass and do update on student table
				$newpassencrypt = password_hash($newpass, PASSWORD_BCRYPT);
				if(mysql_query("UPDATE student SET student.userpassword = '$newpassencrypt'
								WHERE student.fsuid = '$currfsuid';")){
									$message = $success;
				} else {
					$message = $sqlfailure . mysql_error();
				}
			}
			else
				$message = $wrongoldpass;
			
		}
	endif;

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
              <table align="center" border="1" cellpadding="2" cellspacing="3"
                summary="User account info">
                <?php
                  // get fsuid, name, major, dob
                  mysql_connect("localhost","root","");
                  mysql_select_db("fsuenrollmentdb");
                  $currfsuid = $user['fsuid'];
                  $data = mysql_query("SELECT student.fsuid, student.fname,
                                              student.lname, student.dob, memberof.majorname
                                        FROM student, memberof
                                        WHERE student.fsuid = '$currfsuid'
                                          AND memberof.fsuid = '$currfsuid' LIMIT 1;");
                  $row = mysql_fetch_array($data);

                ?>
                <tr>
                  <th>FSUID</th>
                  <td><?php echo $row['fsuid']; ?></td>
                </tr>
                <tr>
                  <th>Name</th>
                  <td><?php echo $row['fname'] . " " . $row['lname']; ?></td>
                </tr>
                <tr>
                  <th>Major</th>
                  <td><?php echo $row['majorname']; ?></td>
                </tr>
                <tr>
                  <th>Date of Birth</th>
                  <td><?php echo $row['dob']; ?></td>
                </tr>
              </table>
              <?php
                mysql_free_result($data);
                mysql_close();
              ?>
			  
			<?php if(!empty($message)): ?>
				<p><?= $message ?></p>
			<?php endif; ?>
			  
			<p>Use the form below to change your password</p>
			<form action="updateaccount.php" method="post">
			   <input type="password" placeholder="Old password" name="oldpass">
			   <input type="password" placeholder="Confirm old password" name="oldpassconfirm">
			   <input type="password" placeholder="New password" name="newpass">
			   <input type="password" placeholder="Confirm new password" name="newpassconfirm">
			   <input type="submit" name="submit" value="Change password">
			</form>
		  
	  
			<br /><br /><br />
         <form action="/home.php">
           <input type="submit" value="Go back to your homepage" />
         </form>

         <form action="/logout.php">
           <input type="submit" value="Logout" />
         </form>

        <?php else:  ?>
          <?php header("Location: /login.php"); ?>
        <?php endif; ?>
		
      </div>
    </div>
  </body>
</html>
