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

  mysql_connect("localhost","root","");
  mysql_select_db("fsuenrollmentdb");
  $currfsuid = $user['fsuid'];

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

		<?php
			// get user's majorname
			  $result = mysql_query("SELECT majorname FROM memberof WHERE memberof.fsuid = '$currfsuid';");
			  $value = mysql_fetch_array($result);
			  $usermajor = $value['majorname'];

			  // needed for major: computer science
			  // computer science areas: core, programming lang elective(1), electives(4),
			  //						science(physics or chemistry/biology)
			  $compscicorearr = array("MAC1105","MAC1140","MAC1114","MAC2311","MAD2104","MAD3105",
										"MAC2312","STA4442","COP3014","CIS4250","COP3353","COP3330",
										"CEN4020","COP4710","COP4530","CDA3100","CDA3101","COT4420",
										"COP4531","COP4020","COP4610");
			  $compscielectivearr = array("CEN4021","CEN4681","CNT4504","COP4342",
                    "COP4613");
        $compsciprolangarr = array("COP3252","COP3035");

			  $libquantitativearr = array("MAC1105","MAC1140","STA2171");
			  $libenglishcomparr = array("ENC1101","ENC2134");
			  $libsocialsciarr = array("SYG1000","PSY2012","ANT2000","POS1041"); // eco 2013, eco 2023
			  $libhistoryarr = array("ASH3100","EUH2000","AMH2096","AMH2010","AMH2020");
			  $libculturalarr = array("CLT3370","MUH2019","CHT3391");
			  $libethicsarr = array("PHI2630","PHI2010","PHM2300");
			  $libeseriesarr = array("IFS2015","IFS2096","IFS3132");
			  $libelectivearr = array("AFA1003","DAN2100","HFT2062");

        $langchinesearr = array("CHI1120","CHI1121","CHI2220");
        $langspanisharr = array("SPN1120","SPN1121","SPN2220");

        // master array of possible future courses in "XXXYYYY" format, single string
        $futurecoursearr = array();

        // get array of user's courses with subject code and course number concatenated for array matching purposes
        mysql_connect("localhost","root","");
        mysql_select_db("fsuenrollmentdb");
        $currfsuid = $user['fsuid'];
        $data = mysql_query("SELECT course.subjectcode, course.coursenumber,
                                      course.coursename, course.credits
                              FROM course, hastaken
                              WHERE hastaken.fsuid = '$currfsuid' AND
                                course.subjectcode = hastaken.subjectcode AND
                                course.coursenumber = hastaken.coursenumber;");
        $usercoursearray = array();
        while ($row = mysql_fetch_array($data)) {
            $addcourse = $row['subjectcode'] . $row['coursenumber'];
            array_push($usercoursearray,$addcourse);
        }


        /************************************************************************************/
        // BEGIN LIBERAL STUDIES CHECKS AND UNIONS
        // liberal studies quantitative
        $countlibquantitative = 0;
        $userlibquantitativearr = array();
        // find which of these quantitative classes the user has taken
        foreach($libquantitativearr as $i){
            if( in_array($i,$usercoursearray)){
                $countlibquantitative = $countlibquantitative + 1;
                array_push($userlibquantitativearr,$i);
            }
        }

        if($countlibquantitative<2){ // add the ones they havent taken to their future courses if requirement not met
            echo "<p>Missing " . (2 - $countlibquantitative) * 3 . " Quantitative and Logical Thinking credits</p>";
            $futurecoursearr = array_merge($futurecoursearr,(array_diff($libquantitativearr,$userlibquantitativearr)));
        }else{  // do nothing if requiremnt met, this set of courses is not needed
            echo "<p>Completed all Quantitative and Logical Thinking credits</p>";
        }

        // liberal studies english composition
        $countlibenglishcomp = 0;
        $userlibenglishcomparr = array();
        foreach($libenglishcomparr as $i){
            if( in_array($i,$usercoursearray)){
                $countlibenglishcomp = $countlibenglishcomp + 1;
                array_push($userlibenglishcomparr,$i);
            }
        }

        if($countlibenglishcomp<2){
            echo "<p>Missing " . (2 - $countlibenglishcomp) * 3 . " English Composition credits</p>";
            $futurecoursearr = array_merge($futurecoursearr,(array_diff($libenglishcomparr,$userlibenglishcomparr)));
        }else{
            echo "<p>Completed all English Composition credits</p>";
        }

        // liberal studies social sciences
        $countlibsocialsci = 0;
        $userlibsocialsciarr = array();
        foreach($libsocialsciarr as $i){
            if( in_array($i,$usercoursearray)){
                $countlibsocialsci = $countlibsocialsci + 1;
                array_push($userlibsocialsciarr,$i);
            }
        }

        if($countlibsocialsci < 1){
            echo "<p>Missing " . (1 - $countlibsocialsci) * 3 . " Social Science credits</p>";
            $futurecoursearr = array_merge($futurecoursearr,(array_diff($libsocialsciarr,$userlibsocialsciarr)));
        }else{
            echo "<p>Completed all Social Science credits</p>";
        }

        // liberal studies history
        $countlibhistory = 0;
        $userlibhistoryarr = array();
        foreach($libhistoryarr as $i){
            if(in_array($i,$usercoursearray)){
                $countlibhistory = $countlibhistory + 1;
                array_push($userlibhistoryarr,$i);
            }
        }

        if($countlibhistory < 1){
            echo "<p>Missing " . (1 - $countlibhistory) * 3 . " History credits</p>";
            $futurecoursearr = array_merge($futurecoursearr,(array_diff($libhistoryarr,$userlibhistoryarr)));
        }else{
            echo "<p>Completed all History credits</p>";
        }

        // liberal studies humanities and cultural practice
        $countlibcultural = 0;
        $userlibculturalarr = array();
        foreach($libculturalarr as $i){
            if( in_array($i,$usercoursearray)){
                $countlibcultural = $countlibcultural + 1;
                array_push($userlibculturalarr,$i);
            }
        }

        if($countlibcultural < 1){
            echo "<p>Missing " . (1 - $countlibcultural) * 3 . " Cultural credits</p>";
            $futurecoursearr = array_merge($futurecoursearr,(array_diff($libculturalarr,$userlibculturalarr)));
        }else{
            echo "<p>Completed all Humanities and Cultural Practice credits</p>";
        }

        // liberal studies ethics
        $countlibethics = 0;
        $userlibethicsarr = array();
        foreach($libethicsarr as $i){
            if( in_array($i,$usercoursearray)){
                $countlibethics = $countlibethics + 1;
                array_push($userlibethicsarr,$i);
            }
        }

        if($countlibethics < 1){
            echo "<p>Missing " . (1 - $countlibethics) * 3 . " Ethics credits</p>";
            $futurecoursearr = array_merge($futurecoursearr,(array_diff($libethicsarr,$userlibethicsarr)));
        }else{
            echo "<p>Completed all Ethics credits</p>";
        }

        // liberal studies e-series
        $countlibeseries = 0;
        $userlibeseriesarr = array();
        foreach($libeseriesarr as $i){
            if( in_array($i,$usercoursearray)){
                $countlibeseries = $countlibeseries + 1;
                array_push($userlibeseriesarr,$i);
            }
        }

        if($countlibeseries < 1){
            echo "<p>Missing " . (1 - $countlibeseries) * 3 . " E-series credits</p>";
            $futurecoursearr = array_merge($futurecoursearr,(array_diff($libeseriesarr,$userlibeseriesarr)));
        }else{
            echo "<p>Completed all E-Series credits</p>";
        }

        // liberal studies elective
        $countlibelective = 0;
        $userlibelectivearr = array();
        foreach($libelectivearr as $i){
            if( in_array($i,$usercoursearray)){
                $countlibelective = $countlibelective + 1;
                array_push($userlibelectivearr,$i);
            }
        }

        if($countlibelective < 1){
            echo "<p>Missing " . (1 - $countlibelective) * 3 . " Liberal Studies elective credies</p>";
            $futurecoursearr = array_merge($futurecoursearr,(array_diff($libelectivearr,$userlibelectivearr)));
        }else{
            echo "<p>Completed all Liberal Studies elective requirements</p>";
        }
        // END LIBERAL STUDIES CHECKS AND UNIONS
        /**********************************************************************************/

        if( !in_array("SPN1120",$usercoursearray) && !in_array("CHI1120",$usercoursearray) ){
            array_push($futurecoursearr,"SPN1120");
            array_push($futurecoursearr,"CHI1120");
            echo "<p>Need all foreign language credits</p>";
        }elseif( in_array("SPN1120",$usercoursearray) && !in_array("SPN1121",$usercoursearray) && !in_array("SPN2220",$usercoursearray) ){
            array_push($futurecoursearr,"SPN1121");
            echo "<p>Need two more spanish courses</p>";
        }elseif( in_array("SPN1120",$usercoursearray) && in_array("SPN1121",$usercoursearray) && !in_array("SPN2220",$usercoursearray) ){
            array_push($futurecoursearr,"SPN2220");
            echo "<p>Need one more spanish course</p>";
        }elseif( in_array("SPN1120",$usercoursearray) && in_array("SPN1121",$usercoursearray) && in_array("SPN2220",$usercoursearray) ){
            echo "<p>Completed Foreign Language requirement</p>";
        }elseif( in_array("CHI1120",$usercoursearray) && !in_array("CHI1121",$usercoursearray) && !in_array("CHI2220",$usercoursearray) ){
            array_push($futurecoursearr,"CHI1121");
            echo "<p>Need two more chinese courses</p>";
        }elseif( in_array("CHI1120",$usercoursearray) && in_array("CHI1121",$usercoursearray) && !in_array("CHI2220",$usercoursearray) ){
            array_push($futurecoursearr,"CHI2220");
            echo "<p>Need one more chinese courses</p>";
        }elseif( in_array("CHI1120",$usercoursearray) && in_array("CHI1121",$usercoursearray) && in_array("CHI2220",$usercoursearray) ){
            echo "<p>Completed foreign language requirement</p>";
        }


        // BEGIN COMPUTER SCIENCE MAJOR CHECKS***********************************************
        // check core requirements
        // get all core courses user has not taken
        $notusermajorarr = array();
        foreach($compscicorearr as $i){
            if( !in_array($i,$usercoursearray) ){
                array_push($notusermajorarr,$i);
            }
        }

        if(!empty($notusermajorarr)){
            mysql_connect("localhost","root","");
            mysql_select_db("fsuenrollmentdb");
            // foreach core course the user doesnt have, union it to the
            // future course set if the user has the prereqs
            foreach($notusermajorarr as $i){
                // echo "<p>" . substr($i,0,3) . " -------  " . substr($i,3) . "</p>";
                $checksubcode = substr($i,0,3);
                $checkcoursenum = substr($i,3);

                $prereqquery = mysql_query("SELECT prerequisite.subjectcodereq, prerequisite.coursenumberreq
              								FROM prerequisite WHERE prerequisite.subjectcodefor = '$checksubcode' AND
              									prerequisite.coursenumberfor = '$checkcoursenum';");
                if(mysql_fetch_row($prereqquery)){
                    // if course has prereqs
                    $countquery = mysql_query("SELECT COUNT(*) AS total FROM prerequisite
                									WHERE prerequisite.subjectcodefor = '$checksubcode' AND
                									prerequisite.coursenumberfor = '$checkcoursenum';");
                    $countresult = mysql_fetch_assoc($countquery);
                    $counter = 0;

                    $prereqquery = mysql_query("SELECT prerequisite.subjectcodereq, prerequisite.coursenumberreq
                  								FROM prerequisite WHERE prerequisite.subjectcodefor = '$checksubcode' AND
                  									prerequisite.coursenumberfor = '$checkcoursenum';");

                    while($row = mysql_fetch_array($prereqquery)){
                        $currchecksubcode = $row['subjectcodereq'];
                        $currcheckcoursenum = $row['coursenumberreq'];

                        // execute this query to see if user has prereq $row
                        $prereqcheck = "SELECT * FROM hastaken WHERE hastaken.fsuid = '$currfsuid' AND
                                              hastaken.subjectcode = '$currchecksubcode' AND
                                              hastaken.coursenumber = '$currcheckcoursenum';";
                        $checkifhastaken = mysql_query($prereqcheck);

                        if(mysql_num_rows($checkifhastaken) == 0)
                            break;

                        $counter = $counter + 1;
                    }
                    // user has all prereqs, add to future course array
                    if($counter == $countresult['total']){
                        array_push($futurecoursearr,$i);
                    }
                }
                else {
                    array_push($futurecoursearr,$i);
                }
            }
            echo "<p>Computer Science core requirements not complete</p>";
        }else{
            echo "<p>Computer Science core requirements completed</p>";
        }

        /*****************************************************/
        /*****************************************************/
        /*****************************************************/
        function checkuserprereqfunc($fsuidarg, $subcodearg, $coursenumarg){
            $prereqquery = mysql_query("SELECT prerequisite.subjectcodereq, prerequisite.coursenumberreq
                          FROM prerequisite WHERE prerequisite.subjectcodefor = '$subcodearg' AND
                            prerequisite.coursenumberfor = '$coursenumarg';");
            if(mysql_fetch_row($prereqquery)){
                $countquery = mysql_query("SELECT COUNT(*) AS total FROM prerequisite
                              WHERE prerequisite.subjectcodefor = '$subcodearg' AND
                              prerequisite.coursenumberfor = '$coursenumarg';");
                $countresult = mysql_fetch_assoc($countquery);
                $counter = 0;
                $prereqquery = mysql_query("SELECT prerequisite.subjectcodereq, prerequisite.coursenumberreq
                              FROM prerequisite WHERE prerequisite.subjectcodefor = '$subcodearg' AND
                                prerequisite.coursenumberfor = '$coursenumarg';");
                while($row = mysql_fetch_array($prereqquery)){
                    $currchecksubcode = $row['subjectcodereq'];
                    $currcheckcoursenum = $row['coursenumberreq'];
                    $prereqcheck = "SELECT * FROM hastaken WHERE hastaken.fsuid = '$fsuidarg' AND
                                          hastaken.subjectcode = '$currchecksubcode' AND
                                          hastaken.coursenumber = '$currcheckcoursenum';";
                    $checkifhastaken = mysql_query($prereqcheck);
                    if(mysql_num_rows($checkifhastaken) == 0)
                        break;
                    $counter = $counter + 1;
                }
                if($counter == $countresult['total'])
                    return 1;
                else
                    return 0;
            }
            else
                return 1;
        }
        /*****************************************************/
        /*****************************************************/
        /*****************************************************/

        // check cs electives
        // first get number of cs electives user has
        $cselectivecount = 0;
        $usercselectivearr = array();
        $usernotcselectivearr = array();

        foreach($compscielectivearr as $i){
            if( in_array($i,$usercoursearray) ){
                $cselectivecount = $cselectivecount + 1;
                array_push($usercselectivearr,$i);
            }
            else
                array_push($usernotcselectivearr,$i);
        }

        if($cselectivecount < 3){
            // if they dont have all of them, calculate prereqs
            // abd add the ones they can take to the master list
            echo "<p>Missing " . (3 - $cselectivecount) . " Computer Science electives.</p>";
            // already have a list of electives the user hasnt taken
            foreach($usernotcselectivearr as $i){
                if(checkuserprereqfunc($currfsuid,substr($i,0,3),substr($i,3))){
                    array_push($futurecoursearr,$i);
                }
            }
        }else{
            echo "<p>Computer Science elective requirements complete</p>";
        }

        // check programming language electives
        $csproglangcount = 0;
        $usercsproglangarr = array();
        $usernotcsproglangarr = array();

        foreach($compsciprolangarr as $i){
            if( in_array($i,$usercoursearray) ){
                $csproglangcount = 0;
                $array_push($usercsproglangarr,$i);
            }
            else
                array_push($usernotcsproglangarr,$i);
        }

        if($csproglangcount < 1){
            echo "<p>Missing 1 Computer Science progamming language elective.</p>";

            foreach($usernotcsproglangarr as $i){
                if(checkuserprereqfunc($currfsuid,substr($i,0,3),substr($i,3))){
                    array_push($futurecoursearr,$i);
                }
            }
        }else{
            echo "<p>Computer Science programming language requirements complete</p>";
        }

        // check science requirements
        if(!in_array("PHY2048",$usercoursearray) && !in_array("BSC2009",$usercoursearray) && !in_array("CHM1045",$usercoursearray)){
            array_push($futurecoursearr,"PHY2048");
            array_push($futurecoursearr,"BSC2009");
            array_push($futurecoursearr,"CHM1045");
            echo "<p>Natural Science requirment not started</p>";
        }
        elseif(in_array("PHY2048",$usercoursearray) && !in_array("PHY2049",$usercoursearray)){
            array_push($futurecoursearr,"PHY2049");
            echo "<p>Need to take second physics course, PHY2049</p>";
        }
        elseif(in_array("PHY2048",$usercoursearray) && in_array("PHY2049",$usercoursearray)){
            echo "<p>Natural Science requirement completed with physics</p>";
        }
        elseif(in_array("CHM1045",$usercoursearray) && !in_array("BSC2009",$usercoursearray)){
            array_push($futurecoursearr,"BSC2009");
            echo "<p>Need to take two biology courses, since you already took chemistry</p>";
        }
        elseif(!in_array("CHM1045",$usercoursearray) && in_array("BSC2009",$usercoursearray) && !in_array("BSC2011",$usercoursearray)){
            array_push($futurecoursearr,"CHM1045");
            array_push($futurecoursearr,"BSC2011");
            echo "<p>Need to take chemistry I and Biology II, since you already took Biology I</p>";
        }
        elseif(in_array("CHM1045",$usercoursearray) && in_array("BSC2009",$usercoursearray) && !in_array("BSC2011",$usercoursearray)){
            array_push($futurecoursearr,"BSC2011");
            echo "<p>Need to take Biology II, since you already took Chemistry I and Biology I</p>";
        }
        elseif(!in_array("CHM1045",$usercoursearray) && in_array("BSC2009",$usercoursearray) && in_array("BSC2011",$usercoursearray)){
            array_push($futurecoursearr,"CHM1045");
            echo "<p>Need to take Chemistry I, since you already took Biology I and II</p>";
        }
        elseif(in_array("CHM1045",$usercoursearray) && in_array("BSC2009",$usercoursearray) && in_array("BSC2011",$usercoursearray)){
            echo "<p>Natural Science requirement completed with chemistry and biology</p>";
        }

        // END COMPUTER SCIENCE CHECKS***************************************************************

        foreach($futurecoursearr as $i)
          echo "<p>" . $i . "</p>";

        echo "<p>" . rand(0,sizeof($futurecoursearr)-1) . "</p>";

        $fourclassschedule = array();

        mysql_connect("localhost","root","");
        mysql_select_db("fsuenrollmentdb");

        //debug
        $c = 0;

        $bitschedulearr = array_fill(0,1440,0);

        // run this while loop until

        // $c < 10 only for debug
        while( $c < 10 ){
            $pick = rand(0,sizeof($futurecoursearr)-1);
            $cisubcode = substr($futurecoursearr[$pick],0,3);
            $cicoursenum = substr($futurecoursearr[$pick],3);
            echo "<p>" . $cisubcode . $cicoursenum . "</p>";
            $timequery = mysql_query("SELECT * FROM courseinstance
                                      WHERE courseinstance.subjectcode = '$cisubcode' and
                                          courseinstance.coursenumber = '$cicoursenum' LIMIT 1;");
            $timedata = mysql_fetch_array($timequery);

            echo "<p>" . $timedata['starttime'] . " " . $timedata['endtime'] . "</p>";

            $hourstart = intval(substr($timedata['starttime'],0,2));
            $minutestart = intval(substr($timedata['starttime'],3,2));
            $hourend = intval(substr($timedata['endtime'],0,2));
            $minuteend = intval(substr($timedata['endtime'],3,2));

            echo "<p>" . $hourstart . " : " . $minutestart . "</p>";
            echo "<p>" . $hourend . " : " . $minuteend . "</p>";

            $mbool = $timedata['monday'];
            $tbool = $timedata['tuesday'];
            $wbool = $timedata['wednesday'];
            $rbool = $timedata['thursday'];
            $fbool = $timedata['friday'];

            $course = array_fill(0,1440,0);

            $hourindexstart = $hourstart * 12;
            $minuteindexstart = $minutestart / 5;
            $hourindexend = $hourend * 12;
            $minuteindexend = $minuteend / 5;

            if($mbool){
                $indexstart = $hourindexstart + $minuteindexstart;
                $indexend = $hourindexend + $minuteindexend;
                $i = 0;
                while($i < $indexend){
                    if($i >= $indexstart)
                      $course[$i] = 1;
                    $i = $i + 1;
                }
            }
            if($tbool){
              $indexstart = 288 + $hourindexstart + $minuteindexstart;
              $indexend = 288 + $hourindexend + $minuteindexend;
              $i = 0;
              while($i < $indexend){
                  if($i >= $indexstart)
                    $course[$i] = 1;
                  $i = $i + 1;
              }
            }
            if($wbool){
              $indexstart = (288 * 2) + $hourindexstart + $minuteindexstart;
              $indexend = (288 * 2) + $hourindexend + $minuteindexend;
              $i = 0;
              while($i < $indexend){
                  if($i >= $indexstart)
                    $course[$i] = 1;
                  $i = $i + 1;
              }
            }
            if($rbool){
              $indexstart = (288 * 3) + $hourindexstart + $minuteindexstart;
              $indexend = (288 * 3) + $hourindexend + $minuteindexend;
              $i = 0;
              while($i < $indexend){
                  if($i >= $indexstart)
                    $course[$i] = 1;
                  $i = $i + 1;
              }
            }
            if($fbool){
              $indexstart = (288 * 4) + $hourindexstart + $minuteindexstart;
              $indexend = (288 * 4) + $hourindexend + $minuteindexend;
              $i = 0;
              while($i < $indexend){
                  if($i >= $indexstart)
                    $course[$i] = 1;
                  $i = $i + 1;
              }
            }

            // if 2 is not in pairwise addition of $bitschedulearr and $course
            //    then push $futurecoursearr[$pick] to $fourcoursearr
            //    and remove $pick form $futurecoursearr (may not need to do this since if a course
            //                     is already in the schedule and you try to add it, there will for sure be a 2 in the array)
            //    and set $bitschedulearr  to be pairwise addition of $bitschedulearr and $course

            //foreach($course as $i)
            //    echo $i;

            // debug
            $c = $c + 1;
//            see if it works in the current schedule
//            if it works, union it to the schedule
//            if not, continue


        }

    //    $fiveclassschedule = array();

      //while(/* size of schedule array <=5 */){
//
      //  }

		?>

    <table align="center" border="1" cellpadding="2" cellspacing="3"
      summary="User's completed courses concatenated">
      <tr>
        <th>course</th>
      </tr>

      <?php
        mysql_connect("localhost","root","");
        mysql_select_db("fsuenrollmentdb");
        $currfsuid = $user['fsuid'];
        $data = mysql_query("SELECT course.subjectcode, course.coursenumber,
                                      course.coursename, course.credits
                              FROM course, hastaken
                              WHERE hastaken.fsuid = '$currfsuid' AND
                                course.subjectcode = hastaken.subjectcode AND
                                course.coursenumber = hastaken.coursenumber;");
        $usercoursearray = array();
        while ($row = mysql_fetch_array($data)) {
            $addcourse = $row['subjectcode'] . $row['coursenumber'];
            array_push($usercoursearray,$addcourse);
        }
        foreach ($usercoursearray as $i){
            echo "<tr><td>" . $i . "</td></tr>";
        }

        mysql_free_result($data);
        mysql_close();
      ?>
    </table>


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
