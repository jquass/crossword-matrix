<?php

require 'crossword_matrix/crossword_matrix_methods.php';

$name = array_key_exists('name', $_REQUEST)
    ? $_REQUEST['name']
    : 'New Puzzle';
$size = array_key_exists('size', $_REQUEST)
    ? $_REQUEST['size']
    : 15;

$puzzle = getPuzzleFromRequest($_REQUEST, $size);

$template = findTemplate($puzzle);

$twoDimensionalPuzzle = [];
foreach ($puzzle as $puzzlePiece ) {
    $twoDimensionalPuzzle += $puzzlePiece;
}

?>

<form name="<?= $name ?>" method="post">

    <input type="text" name="name" style="position:absolute;top:35px;left:400px;" value="<?= $name ?>">

    <input type="submit" style="position:absolute;top:75px;left:400px;" value="submit" name="submit"><br>

    <?php
    $n = 1;
    while ($n <= $size * $size) {
        $name = 'c' . $n;

        $value = array_key_exists($name, $twoDimensionalPuzzle) ? $twoDimensionalPuzzle[$name] : BLANK;

        if (array_key_exists($name, $template)) {
            $backgroundColor = 'yellow';
        } else if (str_replace(' ', '', $value) == SOLID) {
            $backgroundColor = 'black';
        } else {
            $backgroundColor = 'white';
        }

        echo "<input type=\"text\"
                    style=\"background-color:{$backgroundColor};text-align:center;font-weight:bold;font-family:courier;\" 
                    size=2
                    name=\"{$name}\" 
                    value=\"{$value}\">";

        echo $n % $size == 0 ? '<br/>' : '';

        $n++;
    }
    ?>
</form>

<h1>** DEBUG **</h1>

<h2>Template?</h2>
<pre>
    <?= print_r($template) ?>
</pre>

<h2>Puzzle</h2>
<pre>
    <?= print_r($puzzle) ?>
</pre>

<h2>2D Puzzle</h2>
<pre>
    <?= print_r($twoDimensionalPuzzle) ?>
</pre>

