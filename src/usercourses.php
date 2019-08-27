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

  $subcodeadd = array_key_exists('formsubcodeadd', $_POST) ? $_POST['formsubcodeadd'] : null;
  $coursenumadd = array_key_exists('formcoursenumadd', $_POST) ? $_POST['formcoursenumadd'] : null;
  $subcodedel = array_key_exists('formsubcodedel', $_POST) ? $_POST['formsubcodedel'] : null;
  $coursenumdel = array_key_exists('formcoursenumdel', $_POST) ? $_POST['formcoursenumdel'] : null;

  $message = '';
  $addsuccess = 'Successfully added course. ';
  $addfail = 'There was an error. You have already added that course or it does not exist. ';
  $delsuccess = 'Successfully deleted course. ';
  $delfail = 'There was an error. That course was not in your class history. ';

  if(!empty($subcodeadd) && !empty($coursenumadd)):
    mysql_connect("localhost","root","");
    mysql_select_db("fsuenrollmentdb");
    $currfsuid = $user['fsuid'];

	// BEGIN NEW ADD CODE
	// query for all prereqs of the desired course
	$prereqquery = mysql_query("SELECT prerequisite.subjectcodereq, prerequisite.coursenumberreq
								FROM prerequisite WHERE prerequisite.subjectcodefor = '$subcodeadd' AND
									prerequisite.coursenumberfor = '$coursenumadd';");

	if(!(mysql_fetch_row($prereqquery))){
		// course had no prereqs, add as normal
		if(mysql_query("INSERT INTO hastaken (fsuid, subjectcode, coursenumber)
						VALUES ('$currfsuid', '$subcodeadd', '$coursenumadd');")) {
			$message = $addsuccess;
		} else {
			$message = $addfail . mysql_error();
		}
	} else {

		// find how many prereqs this course has
		$countquery = mysql_query("SELECT COUNT(*) AS total FROM prerequisite
									WHERE prerequisite.subjectcodefor = '$subcodeadd' AND
									prerequisite.coursenumberfor = '$coursenumadd';");
		$countresult = mysql_fetch_assoc($countquery);
		// counter for how many prereqs the user satisfies
		$counter = 0;

    $prereqquery = mysql_query("SELECT prerequisite.subjectcodereq, prerequisite.coursenumberreq
  								FROM prerequisite WHERE prerequisite.subjectcodefor = '$subcodeadd' AND
  									prerequisite.coursenumberfor = '$coursenumadd';");
		// course has one or more prereqs
		while($row = mysql_fetch_array($prereqquery)){
			// loop through these prereqs

			$currchecksubcode = $row['subjectcodereq'];
			$currcheckcoursenum = $row['coursenumberreq'];

			// execute this query to see if user has prereq $row
			$prereqcheck = "SELECT * FROM hastaken WHERE hastaken.fsuid = '$currfsuid' AND
														hastaken.subjectcode = '$currchecksubcode' AND
														hastaken.coursenumber = '$currcheckcoursenum';";
			$checkifhastaken = mysql_query($prereqcheck);

			if(mysql_num_rows($checkifhastaken) == 0){
				// if not, tell them what they need and break
				$message = $currchecksubcode . $currcheckcoursenum . " is a prerequisite for "
            . $subcodeadd . $coursenumadd
							. ". add that course first";
				break;
			}
			$counter = $counter + 1;
		}
		// if the user has as many prereqs as the count query, add the course
		if($counter == $countresult['total']){
			if(mysql_query("INSERT INTO hastaken (fsuid, subjectcode, coursenumber)
							VALUES ('$currfsuid', '$subcodeadd', '$coursenumadd');")) {
				$message = $addsuccess;
			} else {
				$message = $addfail . mysql_error();
			}
		}

		mysql_free_result($prereqquery);
	}
	// END NEW ADD CODE
  endif;

  if(!empty($subcodedel) && !empty($coursenumdel)):
    mysql_connect("localhost","root","");
    mysql_select_db("fsuenrollmentdb");
    $currfsuid = $user['fsuid'];

  $result = mysql_query("SELECT 1 FROM hastaken
                        WHERE hastaken.fsuid = '$currfsuid'
                        AND hastaken.subjectcode='$subcodedel'
                        AND hastaken.coursenumber='$coursenumdel' LIMIT 1;");

  if(mysql_fetch_row($result)) {
      // if the user has that course
      // now see if that course is REQUIRED BY any courses
      $prereqquery = mysql_query("SELECT prerequisite.subjectcodefor, prerequisite.coursenumberfor
                                FROM prerequisite WHERE prerequisite.subjectcodereq = '$subcodedel'
                                                AND prerequisite.coursenumberreq = '$coursenumdel';");

      if(!(mysql_fetch_row($prereqquery))){
            // deletion attempt is not required by any course, normal delete
            if(mysql_query("DELETE FROM hastaken
                              WHERE hastaken.fsuid = '$currfsuid'
                                AND hastaken.subjectcode='$subcodedel'
                                AND hastaken.coursenumber='$coursenumdel';")) {
              // so the course exists in the database

              $message = $delsuccess;
            } else {
              $message = $delfail . mysql_error();
            }
        } else{
            // is required by some course
            // find how many courses this is a prereq for
            $countquery = mysql_query("SELECT COUNT(*) AS total FROM prerequisite
        									WHERE prerequisite.subjectcodereq = '$subcodedel'
                          AND prerequisite.coursenumberreq = '$coursenumdel';");
        		$countresult = mysql_fetch_assoc($countquery);
            $counter = 0;

          $prereqquery = mysql_query("SELECT prerequisite.subjectcodefor, prerequisite.coursenumberfor
                                  FROM prerequisite WHERE prerequisite.subjectcodereq = '$subcodedel'
                                      AND prerequisite.coursenumberreq = '$coursenumdel';");

            while($row = mysql_fetch_array($prereqquery)){
                $currchecksubcode = $row['subjectcodefor'];
                $currcheckcoursenum = $row['coursenumberfor'];
                $prereqcheck = "SELECT * FROM hastaken WHERE hastaken.fsuid = '$currfsuid' AND
          														hastaken.subjectcode = '$currchecksubcode' AND
          														hastaken.coursenumber = '$currcheckcoursenum';";
                $checkifhastaken = mysql_query($prereqcheck);
                if(mysql_num_rows($checkifhastaken) != 0){
                    $message = $currchecksubcode . $currcheckcoursenum . " requires "
                          . $subcodedel . $coursenumdel . ". delete that course first";
                    break;
                }
                $counter = $counter + 1;
            }

            if($counter == $countresult['total']){
                if(mysql_query("DELETE FROM hastaken
                                  WHERE hastaken.fsuid = '$currfsuid'
                                    AND hastaken.subjectcode='$subcodedel'
                                    AND hastaken.coursenumber='$coursenumdel';")) {
                    $message = $delsuccess;
                } else {
                    $message = $delfail . mysql_error();
                }
            }
        }
  } else {
      // else failure
      $message = $delfail;
  }
	// END NEW DELETE CODE
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

         <!-- main page area -->
         <?php if( !empty($user) ): ?>
           <br /><?= $user['fname']; ?> <?= $user['lname']; ?>'s class history
           <br />
         <table align="center" border="1" cellpadding="2" cellspacing="3"
           summary="User's completed courses">
           <tr>
             <th>Subject Code</th>
             <th>Course Number</th>
             <th>Course Name</th>
             <th>Credits</th>
           </tr>

           <?php
             // @$row['fsuid'] to suppress undefined index error
             mysql_connect("localhost","root","");
             mysql_select_db("fsuenrollmentdb");
             $currfsuid = $user['fsuid'];
             $data = mysql_query(" SELECT course.subjectcode, course.coursenumber,
                                           course.coursename, course.credits
                                   FROM course, hastaken
                                   WHERE hastaken.fsuid = '$currfsuid' AND
                                     course.subjectcode = hastaken.subjectcode AND
                                     course.coursenumber = hastaken.coursenumber;");
             while ($row = mysql_fetch_array($data)) {
               echo "<tr>";
               echo "<td>" . $row['subjectcode'] . "</td>";
               echo "<td>" . $row['coursenumber'] . "</td>";
               echo "<td>" . $row['coursename'] . "</td>";
               echo "<td>" . $row['credits'] . "</td>";
               echo "</tr>";
             }

             mysql_free_result($data);
             mysql_close();
           ?>
         </table>
<!--
		 <table align="center" border="1" cellpadding="2" cellspacing="3"
           summary="User's completed courses">
           <tr>
             <th>Subject Codefor</th>
             <th>Course Numberfor</th>
           </tr>

           <?php
             // @$row['fsuid'] to suppress undefined index error
             mysql_connect("localhost","root","");
             mysql_select_db("fsuenrollmentdb");
             $currfsuid = $user['fsuid'];
             $data = mysql_query("SELECT prerequisite.subjectcodefor, prerequisite.coursenumberfor
								FROM prerequisite WHERE prerequisite.subjectcodereq = '$subcodedel' AND
									prerequisite.coursenumberreq = '$coursenumdel';");
             while ($row = mysql_fetch_array($data)) {
               echo "<tr>";
               echo "<td>" . $row['subjectcodefor'] . "</td>";
               echo "<td>" . $row['coursenumberfor'] . "</td>";
               echo "</tr>";
             }

             mysql_free_result($data);
             mysql_close();
           ?>
         </table>
-->


         <br />
         <?php if(!empty($message)): ?>
           <p><?= $message ?></p>
         <?php endif; ?>
         <br />
         <p>Add a course here
         </p>
         <form action="usercourses.php" method="post">
           <input type="text" placeholder="Subject code" name="formsubcodeadd">
           <input type="text" placeholder="Course number" name="formcoursenumadd">
           <input type="submit" name="submit" value="Add course">
         </form>

         <p>Or delete a course here
         </p>
         <form action="usercourses.php" method="post">
           <input type="text" placeholder="Subject code" name="formsubcodedel">
           <input type="text" placeholder="Course number" name="formcoursenumdel">
           <input type="submit" name="submit" value="Delete course">
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
