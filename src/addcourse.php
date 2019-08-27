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


  $subcode = array_key_exists('formsubcode', $_POST) ? $_POST['formsubcode'] : null;
  $coursenum = array_key_exists('formcoursenum', $_POST) ? $_POST['formcoursenum'] : null;

  $message = '';
  $success = 'Successfully added course';
  $fail = 'There was an error. You have already added that course or it does not exist';

  if(!empty($subcode) && !empty($coursenum)):
    mysql_connect("localhost","root","");
    mysql_select_db("fsuenrollmentdb");
      $currfsuid = $user['fsuid'];
      //$data = mysql_query("INSERT INTO hastaken (fsuid, subjectcode, coursenumber)
      //                        VALUES($currfsuid, $subcode, $coursenum);");
    if(mysql_query("INSERT INTO hastaken (fsuid, subjectcode, coursenumber)
                      VALUES ('$currfsuid', '$subcode', '$coursenum');")) {
      $message = $success;
    } else {
      $message = $fail . mysql_error();
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

         <p>Enter a course you have taken below
         </p>

         <?php if(!empty($message)): ?>
           <p><?= $message ?></p>
         <?php endif; ?>

         <form action="addcourse.php" method="post">

           <?php
            mysql_connect("localhost","root","");
            mysql_select_db("fsuenrollmentdb");
            $currfsuid = $user['fsuid'];
            // need a query of all courses the user has not taken
            // courses - courses taken
            $data = mysql_query("SELECT course.subjectcode, course.coursenumber, course.coursename, course.credits
                            FROM course");

            while($row = mysql_fetch_array($data)) {
              echo '<input type="submit" value="' . $row['course.subjectcode'] . $row['course.coursenumber'] . '">';
            }
            /*
              SELECT course.subjectcode, course.coursenumber, course.coursename, course.credits
              FROM course
              MINUS
              SELECT course.subjectcode, course.coursenumber, course.coursename, course.credits
              FROM course, hastaken
              WHERE hastaken.fsuid = '$currfsuid' AND course.subjectcode = hastkaen.subjectcode AND
                    course.coursenumber = hastaken.coursenumber;
            */
           ?>
        </form>


          <form action="addcourse.php" method="post">
            <input type="text" placeholder="Subject code" name="formsubcode">
            <input type="text" placeholder="Course number" name="formcoursenum">
            <input type="submit" name="submit" text="Add course">
          </form>





       </div>
     </div>
   </body>
 </html>
