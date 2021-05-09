<?php

require_once '../../app/db/users_db.php';
include '../../app/includes/login.php';

require_once '../../app/db/crossword_db.php';
require_once '../../app/db/dictionary_db.php';
require_once '../../app/lib/crossword/crossword_methods.php';

const DEFAULT_PUZZLE_NAME = 'New Puzzle';
const PUZZLE_SIZE = 15;
const DICTIONARY_MATCHES = 1000;

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
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
    }

    // After POST, reload page for fresh state
    if ($puzzleId) {

        $url = $_SERVER['REQUEST_URI'];
        $parsedUrl = parse_url($url);

        if (array_key_exists('query', $parsedUrl) && $parsedUrl['query']) {
            $query = $parsedUrl['query'];
            foreach( explode('&', $query) as $param) {
                $parsedParam = explode('=', $param);
                if ($parsedParam and $parsedParam[0] == 'id') {
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit();
                }
            }
        }

        header('Location: ' . $_SERVER['REQUEST_URI'] . '?id=' . $puzzleId);
        exit();
    }

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit();

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



<div id="main" class="clearfix">

    <div id="puzzle">
        <form name="puzzle" method="post">

            <input type="hidden" name="form_type" value="puzzle">

            <input type="hidden" name="id" value="<?= $puzzleId ?>">


            <?php
            for ($n = 1; $n <= PUZZLE_SIZE * PUZZLE_SIZE; $n++) {
                $cellName = 'c' . $n;

                $value = array_key_exists($cellName, $oneDimensionalPuzzle) ? $oneDimensionalPuzzle[$cellName] : BLANK;

                if (array_key_exists($cellName, $template)) {
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
                    name=\"{$cellName}\"
                    id=\"{$cellName}\"
                    value=\"{$value}\">";

                echo $n % PUZZLE_SIZE == 0 ? '<br/>' : '';
            }
            ?>

            <div class="header_div">
                <label for="name">Name: </label>
                <input type="text" name="name" value="<?= $name ?>">
                <br><br>
                <input type="submit" value="<?= $savedPuzzle ? 'Update' : 'Save' ?>" name="btnSubmit"><br>
            </div>
        </form>
    </div>

    <div id="dictionary_matches">
        <?php
        if ($matchingDictionaryEntries) {
            $sortedDictionaryEntries = [];

            foreach ($matchingDictionaryEntries as $matchingDictionaryEntry) {
                $sortedDictionaryEntries[$matchingDictionaryEntry['id']] = $matchingDictionaryEntry['word'];
            }
            asort($sortedDictionaryEntries);

            echo '<form name="dictionary_match" method="post">';
            echo '<div class="scroll_box">';
            foreach (array_chunk($sortedDictionaryEntries, sizeof($sortedDictionaryEntries) / 2 + 1, true) as $sortedDictionaryColumn) {
                echo '<div class="left">';
                foreach ($sortedDictionaryColumn as $id => $word) {

                    $wordLen = strlen($word);
                    if ($wordLen <= 2) {
                        $markedWord = $word;
                    } else {

                        if ($wordLen % 2 == 0) {
                            $wordStart = substr($word, 0, $wordLen / 2 - 1);
                            $wordMiddle = substr($word, $wordLen / 2 - 1, 2);
                            $wordEnd = substr($word, $wordLen / 2 + 1);
                        } else {
                            $wordStart = substr($word, 0, $wordLen / 2 );
                            $wordMiddle = substr($word, $wordLen / 2 , 1);
                            $wordEnd = substr($word, $wordLen / 2 + 1);
                        }

                        $markedWord = "{$wordStart}<mark>{$wordMiddle}</mark>{$wordEnd}";
                    }

                    echo "<div class='dictionary_match_input'>
                        <input id='{$id}' type='radio' name='dictionary_id' value='{$id}'>
                        <a href=\"#\" onclick=\"window.location='../dictionary/delete/index.php?id={$id}';\">X</a>
                        <label for='{$id}'>{$markedWord}</label>
                    </div>";
                }
                echo '</div>';
            }
            echo '</div><nr><br>';
            echo '<input type="submit" value="Fill Template">';
            echo "<input type='hidden' name='form_type' value='puzzle_dictionary_match'>
                    <input type='hidden' name='puzzle_id' value='{$puzzleId}'>
                    <input type='hidden' name='template' value='{$serializedTemplate}'>
                </form>";
        }
        ?>
    </div>
</div>

<div id="main_nav">
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

        <div class="header_div">
            <form method="post" id="logout">
                <input type="submit" value="Logout" name="btnSubmit">
            </form>
        </div>

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
