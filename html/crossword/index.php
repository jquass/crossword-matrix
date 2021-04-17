<?php

require_once '../../app/db/users_db.php';
include '../../app/includes/login.php';

require_once '../../app/db/crossword_db.php';
require_once '../../app/db/dictionary_db.php';
require_once '../../app/lib/crossword/crossword_methods.php';

const DEFAULT_PUZZLE_NAME = 'New Puzzle';
const PUZZLE_SIZE = 15;
const DICTIONARY_MATCHES = 100;

$puzzle = [];
$savedPuzzle = null;
$puzzleId = null;
$name = DEFAULT_PUZZLE_NAME;

if ('GET' === $_SERVER['REQUEST_METHOD']) {

    if (array_key_exists('id', $_GET)) {
        $puzzleId = $_GET['id'];
    }

    if ($puzzleId) {
        $savedPuzzle = getSavedPuzzle($puzzleId);
    }

    if ($savedPuzzle) {
        $name = $savedPuzzle['puzzle_name'];
        $puzzle = unserialize($savedPuzzle['puzzle']);
    }

} else if ('POST' === $_SERVER['REQUEST_METHOD']) {

    if (!array_key_exists('form_type', $_POST)) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }

    switch ($_POST['form_type']) {
        case 'puzzle':
            $name = array_key_exists('name', $_POST)
                ? $_POST['name']
                : DEFAULT_PUZZLE_NAME;
            $puzzleId = array_key_exists('id', $_POST)
                ? $_POST['id']
                : null;

            $puzzle = getPuzzleFromRequest($_POST, PUZZLE_SIZE);

            $savedPuzzle = $puzzleId
                ? getSavedPuzzle($puzzleId)
                : null;

            if (!$savedPuzzle) {
                if ($puzzle) {
                    $puzzleId = savePuzzle($name, $puzzle);
                }
                $savedPuzzle = getSavedPuzzle($puzzleId);
            } else {
                if ($puzzle) {
                    $savedPuzzle = updateSavedPuzzle($puzzleId, $puzzle, $name);
                } else {
                    $puzzle = $savedPuzzle;
                }
            }

            break;

        case 'puzzle_dictionary_match':

            $puzzleId = array_key_exists('puzzle_id', $_POST)
                ? $_POST['puzzle_id']
                : null;

            $dictionaryId = array_key_exists('dictionary_id', $_POST)
                ? $_POST['dictionary_id']
                : null;

            $template = array_key_exists('template', $_POST)
                ? unserialize($_POST['template'])
                : null;

            $savedPuzzle = getSavedPuzzle($puzzleId);
            $puzzle = unserialize($savedPuzzle['puzzle']);

            $oneDimensionalPuzzle = convertPuzzleToOneDimension($puzzle);

            $dictionaryEntry = getDictionaryEntry($dictionaryId);
            $letter = 0;
            foreach ($template as $cellId => $value) {
                $oneDimensionalPuzzle[$cellId] = $dictionaryEntry['word'][$letter];
                $letter++;
            }

            $updatedPuzzle = convertPuzzleToTwoDimensions($oneDimensionalPuzzle);
            updateSavedPuzzle($puzzleId, $updatedPuzzle);
            break;

        default:
            break;
    }

    // After POST, reload page for fresh state
    if ($savedPuzzle) {

        $url = $_SERVER['REQUEST_URI'];
        $parsedUrl = parse_url($url);

        if (array_key_exists('query', $parsedUrl) && $parsedUrl['query']) {
            $newUrl = $url . '&id=' . $savedPuzzle['id'];
        } else {
            $newUrl = $newUrl = $parsedUrl['path'] . '?id=' . $savedPuzzle['id'];
        }

        header("Location:" . $newUrl);
        exit();
    } else {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }

} else {
    die('invalid request method : ' . $_SERVER['REQUEST_METHOD']);
}

$template = findTemplate($puzzle);
$serializedTemplate = serialize($template);
$matchingDictionaryEntries = getMatchingDictionaryEntries($template, DICTIONARY_MATCHES);
$oneDimensionalPuzzle = convertPuzzleToOneDimension($puzzle);

//
// HTML
//
?>

<html>
<head>
    <link href="crossword.css" rel="stylesheet">
</head>

<body>

<header>
    <div class="clearfix">
        <div class="header_div">
            <a href="#" onclick="window.location='../index.php'+window.location.search;">
                Puzzle Index
            </a>
        </div>

        <div class="header_div">
            <a href="#" onclick="window.location='../dictionary/index.php'+window.location.search;">
                Manage Dictionary
            </a>
        </div>

        <?php
        if ($savedPuzzle) {
            echo
        '<div class="header_div">
            <a href="#" onclick="window.location=\'../crossword/delete/index.php\'+window.location.search;">
                Delete Puzzle
            </a>
        </div>';
        }
        ?>

    </div>
</header>

<div class="main">

    <form name="puzzle" method="post" id="puzzle">

        <input type="hidden" name="form_type" value="puzzle">

        <input type="hidden" name="id" value="<?= $puzzleId ?>">

        <div class="header_div">
            <input type="text" name="name" value="<?= $name ?>">

            <input type="submit" value="<?= $savedPuzzle ? 'Update' : 'Save' ?>" name="btnSubmit"><br>
        </div>

        <br>
        <br>
        <br>

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
                    class=\"cell_input\"
                    onchange=\"cellValueChange(this.id, this.value)\"
                    style=\"background-color:{$backgroundColor};\" 
                    size=2
                    name=\"{$name}\"
                    id=\"{$name}\"
                    value=\"{$value}\">";

            echo $n % PUZZLE_SIZE == 0 ? '<br/>' : '';
        }
        ?>
    </form>

    <div class="clearfix">
        <?php
        if ($matchingDictionaryEntries) {
            echo '<form name="puzzle_dictionary_match" method="post" id="puzzle_dictionary_match">
                    <input type="submit" value="Fill Template" class="puzzle_dictionary_match_input"><br><br>';
            foreach ($matchingDictionaryEntries as $dictionaryEntry) {
                echo "<div class='puzzle_dictionary_match_input'>
                        <input type='radio' name='dictionary_id' value='{$dictionaryEntry['id']}'>
                        <label for='{$dictionaryEntry['id']}'> {$dictionaryEntry['word']} </label>
                    </div>";
            }
            echo "<input type='hidden' name='form_type' value='puzzle_dictionary_match'>
                    <input type='hidden' name='puzzle_id' value='{$puzzleId}'>
                    <input type='hidden' name='template' value='{$serializedTemplate}'>
                </form>";
        }
        ?>
    </div>
</div>


<footer>
    <br><br>
    <h1>DEBUG</h1>

    <h2>Template?</h2>
    <pre>
        <?= print_r($template) ?>
    </pre>

    <h2>Matches</h2>
    <pre>
        <?= print_r($matchingDictionaryEntries) ?>
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

    <script src="crossword.js"></script>
</footer>
</body>
</html>
