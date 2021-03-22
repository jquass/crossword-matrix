
<?php

$p = strlen($puzzle);

//echo "puzz len=".$p."<BR>";

// echo "puzzle=<BR>".$puzzle."<BR>";

$e = 0;

$thisLoc = 0;

//start 1

while ($e < 114) {


    //start 2

    if ($puzzle{$e} == "*") {


        $thisLoc = $thisLoc + 1;

        $otherLocation = 226 - $e;


        $locations[$thisLoc] = $otherLocation;


        //stop 2

    }


    $e = $e + 1;


    //stop 1

}


$d = 1;


//start 3

while ($locations[$d] != "" and $d < 114) {


    $otherLocation = $locations[$d];


    $firstpuzz = substr($puzzle, 0, $otherLocation - 2);


    $secondpuzz = substr($puzzle, $otherLocation - 1, 300);


    $puzzle = $firstpuzz . "*" . $secondpuzz;


    $d = $d + 1;

    //stop 3

}

