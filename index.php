<?php

require 'crossword_matrix/crossword_matrix_methods.php';
require 'crossword_matrix/crossword_matrix_db.php';

const DEFAULT_PUZZLE_NAME = 'New Puzzle';
const PUZZLE_SIZE = 15;

//
// SETUP
//

$puzzleId = array_key_exists('id', $_REQUEST)
    ? $_REQUEST['id']
    : null;

$savedPuzzle = $puzzleId
    ? getSavedPuzzle($puzzleId)
    : null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $name = $savedPuzzle['puzzle_name'];
    $puzzle = unserialize($savedPuzzle['puzzle']);

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = array_key_exists('name', $_REQUEST)
        ? $_REQUEST['name']
        : DEFAULT_PUZZLE_NAME;
    $puzzle = getPuzzleFromRequest($_REQUEST, PUZZLE_SIZE);

    if (!$puzzleId) {
        print '<h3>...saving...</h3>';

        $puzzleId = savePuzzle($name, $puzzle);
        $success = $puzzleId ? true : false;

    } else {
        print '<h3>...updating...</h3>';

        $savedPuzzle = updateSavedPuzzle($puzzleId, $name, $puzzle);
        $success = $savedPuzzle ? true : false;
    }

    print $success
        ? '<h4>SUCCESS</h4>'
        : '<h4>ERROR</h4><pre>' . pg_last_error() . '</pre>';

} else {
    die('invalid request method : ' . $_SERVER['REQUEST_METHOD']);
}

$template = findTemplate($puzzle);
$oneDimensionalPuzzle = convertPuzzleToOneDimension($puzzle);

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
    for ($n = 1; $n <= PUZZLE_SIZE * PUZZLE_SIZE; $n++) {
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
                    id=\"{$name}\"
                    value=\"{$value}\">";

        echo $n % PUZZLE_SIZE == 0 ? '<br/>' : '';
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


<footer>
    <script>
        const puzzleSize = 15;

        document.onkeydown = function (event) {
            const element = document.activeElement;
            if (element.id.match(/c[0-9]+/)) {
                const idNumber = Number(element.id.substr(1));
                let targetId;
                switch (event.keyCode) {
                    case 37:
                        event.preventDefault();
                        if ((idNumber - 1) % puzzleSize === 0) {
                            return;
                        }
                        targetId = idNumber - 1;
                        break;
                    case 38:
                        event.preventDefault();
                        if (idNumber <= puzzleSize) {
                            return;
                        }
                        targetId = idNumber - puzzleSize;
                        break;
                    case 39:
                        event.preventDefault();
                        if ((idNumber % puzzleSize === 0)) {
                            return;
                        }
                        targetId = idNumber + 1;
                        break;
                    case 40:
                        event.preventDefault();
                        if (idNumber >= puzzleSize * puzzleSize - puzzleSize) {
                            return;
                        }
                        targetId = idNumber + puzzleSize;
                        break;
                }

                if (typeof targetId !== 'undefined') {
                    const targetCell = 'c' + targetId;
                    const target = document.getElementById(targetCell);
                    target.focus();
                }
            }
        };
    </script>
</footer>
