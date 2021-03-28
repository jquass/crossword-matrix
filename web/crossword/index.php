<?php

require '../../app/db/crossword_db.php';
require '../../app/lib/crossword/crossword_methods.php';

const DEFAULT_PUZZLE_NAME = 'New Puzzle';
const PUZZLE_SIZE = 15;

//
// SETUP
//

$puzzle = getPuzzleFromRequest($_REQUEST, PUZZLE_SIZE);

$name = array_key_exists('name', $_REQUEST)
    ? $_REQUEST['name']
    : DEFAULT_PUZZLE_NAME;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $puzzleId = array_key_exists('id', $_GET)
        ? $_GET['id']
        : null;

    $savedPuzzle = $puzzleId
        ? getSavedPuzzle($puzzleId)
        : null;

    if ($savedPuzzle) {
        $name = $savedPuzzle['puzzle_name'];
        $puzzle = unserialize($savedPuzzle['puzzle']);
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $puzzleId = array_key_exists('id', $_POST)
        ? $_POST['id']
        : null;

    $savedPuzzle = $puzzleId
        ? getSavedPuzzle($puzzleId)
        : null;

    if (!$savedPuzzle) {
        $puzzleId = savePuzzle($name, $puzzle);
        $savedPuzzle = getSavedPuzzle($puzzleId);
    } else {
        $savedPuzzle = updateSavedPuzzle($puzzleId, $name, $puzzle);
    }

} else {
    die('invalid request method : ' . $_SERVER['REQUEST_METHOD']);
}

$template = findTemplate($puzzle);
$oneDimensionalPuzzle = convertPuzzleToOneDimension($puzzle);

//
// HTML
//
?>

<a href="#" onclick="window.location='../index.php'+window.location.search;"><<< Puzzle Index</a>

<form name="<?= $name ?>" method="post">

    <input type="hidden" name="id" value="<?= $puzzleId ?>">

    <input type="text" name="name" style="position:absolute;top:35px;left:400px;" value="<?= $name ?>">

    <input type="submit" style="position:absolute;top:75px;left:400px;" value="<?= $savedPuzzle ? 'Update' : 'Save' ?>"
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
                        // Left
                        event.preventDefault();
                        if ((idNumber - 1) % puzzleSize === 0) {
                            return;
                        }
                        targetId = idNumber - 1;
                        break;
                    case 38:
                        // Up
                        event.preventDefault();
                        if (idNumber <= puzzleSize) {
                            return;
                        }
                        targetId = idNumber - puzzleSize;
                        break;
                    case 39:
                        // Right
                        event.preventDefault();
                        if ((idNumber % puzzleSize === 0)) {
                            return;
                        }
                        targetId = idNumber + 1;
                        break;
                    case 40:
                        // Down
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
