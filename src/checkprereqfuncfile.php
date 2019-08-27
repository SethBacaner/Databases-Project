<?php
    // return true if: course has no prereqs or user has the prereqs
    // return false if: course has prereqs and user doesnt have them
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
                $prereqcheck = "SELECT * FROM hastaken WHERE hastaken.fsuid = '$currfsuid' AND
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
 ?>
