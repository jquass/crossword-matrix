<?php

require_once '../app/db/crossword_db.php';
require_once '../app/db/users_db.php';

include '../app/includes/login.php';

const PUZZLE_URL = 'crossword/index.php?id=';

$savedPuzzleId = array_key_exists('id', $_GET)
    ? $_GET['id']
    : null;

$savedPuzzleUrl = PUZZLE_URL . $savedPuzzleId;

$savedPuzzle = $savedPuzzleId
    ? getSavedPuzzle($savedPuzzleId)
    : null;

$savedPuzzles = getSavedPuzzles();

?>

<html>

<h1>Crossword Index</h1>

<?php
if ($savedPuzzle) {
    echo "<button onclick=\"window.location='{$savedPuzzleUrl}'\"> Back to \"{$savedPuzzle['puzzle_name']}\" </button>";
}
?>

<button onclick="window.location='crossword/index.php';"> Create New Puzzle </button>
<button onclick="window.location='dictionary/index.php';"> Manage Dictionary </button>

<ul>
    <?php
    foreach ($savedPuzzles as $savedPuzzle) {
        $puzzleUrl = PUZZLE_URL . $savedPuzzle['id'];
        echo "<li>
                <button onclick=\"window.location='{$puzzleUrl}';\">{$savedPuzzle['puzzle_name']}</button>
            </li>";
    }
    ?>
</ul>


<footer>
    <script>

    </script>
</footer>

</html>

