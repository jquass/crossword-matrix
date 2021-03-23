<?php


$frompage = $_REQUEST['frompage'];

if ($_COOKIE['isitme'] != "yes" and $frompage != "leadinpuzz") {
    echo "frompage=" . $frompage;
    return;
}
//LINK UP
#include '/data/14/1/0/133/1652459/user/1781872/cgi-bin/sec27.php';

//get puzzName either from form or leadinpuzz.php
$puzzName = $_REQUEST['puzzname'];

//get form data and construct $puzzle but only if updateYN=yes
$updateYN = $_REQUEST['updateYN'];


$puzzle = "";

$c = 1;

$regexLocation = 0;

while ($c < 226 and $updateYN == "yes") {


    $fieldName = "c" . $c;

    $thisChar = trim(strtoupper($_REQUEST[$fieldName]));


    if ($thisChar == ".") {

        $regexLocation = $regexLocation + 1;

        $locationNo[$regexLocation] = $c;

    }


    if ($thisChar == "") {

        $thisChar = " ";

    }

    $puzzle .=  $thisChar;


    $c = $c + 1;


}


// END

include 'addasterisk.php';

/*

include 'regexp.php';

*/


//save $puzzle only if updateYN=yes

if ($updateYN == "yes") {
    mysql_query("UPDATE mypuzzles SET puzzle='$puzzle' WHERE puzzname='$puzzName'") or die(mysql_error());
}

$result = mysql_query("SELECT * FROM mypuzzles WHERE puzzname='$puzzName'") or die(mysql_error());

while ($row = mysql_fetch_array($result)) {
    $puzzle = $row['puzzle'];
}


?>

<div style="position:absolute;top:25px;left:400px;">

    <h2><?php echo $puzzName ?></h2>

</div>

<div style="position:absolute;top:125px;left:400px;">

    <a href="http://www.quass.com/leadinpuzz.php?frompage=puzz">Back</a></div>

<form name="<?php echo $puzzName ?>" method="post">

    <input type="hidden" name="puzzname" value="<?php echo $puzzName ?>">

    <input type="submit" style="position:absolute;top:75px;left:400px;" value="submit" name="submit">

    <input type="hidden" name="updateYN" value="yes"><BR>


    <?php

    $c = 1;

    $rowNo = 1;

    while ($c < 226) {


        $thisName = "c" . $c;

        $d = $c - 1;

        $thisValue = $puzzle{$d};


        if ($thisValue == "*") {

            ?>

            <input type="text"
                   style="background-color:black;text-align:center;font-weight:bold;font-family:courier;"
                   name="<?php echo $thisName ?>" size=2 value="<?php echo $thisValue ?>">


            <?php

        } else {


            ?>


            <input type="text"
                   style="background-color:white;text-align:center;font-weight:bold;font-family:courier;" size=2
                   name="<?php echo $thisName ?>" value="<?php echo $thisValue ?>">


            <?php


        }


        if ($rowNo == 15) {

            $rowNo = 1;

            echo "<BR>";

        } else {

            $rowNo = $rowNo + 1;

        }


        $c = $c + 1;


    }


    ?>

</form>





