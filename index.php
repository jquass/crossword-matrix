<?php

require 'crossword_matrix/crossword_matrix_methods.php';
require 'crossword_matrix/crossword_matrix_db.php';

const DEFAULT_PUZZLE_NAME = 'New Puzzle';
const PUZZLE_SIZE = 15;

//
// SETUP
//

$puzzle = getPuzzleFromRequest($_REQUEST, PUZZLE_SIZE);

$template = findTemplate($puzzle);

$oneDimensionalPuzzle = [];
foreach ($puzzle as $puzzlePiece) {
    $oneDimensionalPuzzle += $puzzlePiece;
}

$name = array_key_exists('name', $_REQUEST)
    ? $_REQUEST['name']
    : DEFAULT_PUZZLE_NAME;

$puzzleId = array_key_exists('id', $_REQUEST)
    ? $_REQUEST['id']
    : null;

$savedPuzzle = $puzzleId
    ? getSavedPuzzle($puzzleId)
    : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($puzzleId) {
        print '<h3>...updating...</h3>';

        $savedPuzzle = updateSavedPuzzle($puzzleId, $name, $puzzle);
        $success = $savedPuzzle ? true : false;

    } else {
        print '<h3>...saving...</h3>';

        $puzzleId = savePuzzle($name, $puzzle);
        $success = $puzzleId ? true : false;
    }

    print $success
        ? '<h4>SUCCESS</h4>'
        : '<h4>ERROR</h4><pre>' . pg_last_error() . '</pre>';

} else if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    die('invalid request method : ' . $_SERVER['REQUEST_METHOD']);
}

//
// HTML
//
?>

<form name="<?= $name ?>" method="post">

    <input type="hidden" name="id" value="<?= $puzzleId ?>">

    <input type="text" name="name" style="position:absolute;top:35px;left:400px;" value="<?= $name ?>">

    <input type="submit" style="position:absolute;top:75px;left:400px;" value="<?= $puzzleId ? 'Update' : 'Save' ?>"
           name="submit"><br>

    <?php
    $n = 1;
    while ($n <= PUZZLE_SIZE * PUZZLE_SIZE) {
        $name = 'c' . $n;

        $value = array_key_exists($name, $oneDimensionalPuzzle) ? $oneDimensionalPuzzle[$name] : BLANK;

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

        echo $n % PUZZLE_SIZE == 0 ? '<br/>' : '';

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

<h2>1D Puzzle</h2>
<pre>
    <?= print_r($oneDimensionalPuzzle) ?>
</pre>

<h2>Saved Puzzle</h2>
<pre>
    <?= print_r($savedPuzzle) ?>
</pre>

