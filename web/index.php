<?php

require_once '../app/db/crossword_db.php';
require_once '../app/db/users_db.php';

include '../app/includes/login.php';

$puzzleId = array_key_exists('id', $_GET)
    ? $_GET['id']
    : null;

$savedPuzzle = $puzzleId
    ? getSavedPuzzle($puzzleId)
    : null;

$savedPuzzles = getSavedPuzzles();

?>

<html>

<h1>Crossword Index</h1>

<?php
if ($savedPuzzle) {
    echo "<button onclick=\"window.location=removeParam('id', 'crossword/index.php'+window.location.search)+'&id={$savedPuzzle['id']}';\"> Back to \"{$savedPuzzle['puzzle_name']}\" </button>";
}
?>

<button onclick="window.location=removeParam('id', 'crossword/index.php'+window.location.search);">Create New Puzzle
</button>
<button onclick="window.location=removeParam('id', 'dictionary/index.php'+window.location.search);">Manage Dictionary
</button>

<ul>
    <?php
    foreach ($savedPuzzles as $savedPuzzle) {
        echo "<li><button 
                    onclick=\"window.location=removeParam('id', 'crossword/index.php'+window.location.search)+'&id={$savedPuzzle['id']}';\"
              >
              {$savedPuzzle['puzzle_name']}
              </button></li>";
    }
    ?>
</ul>


<footer>
    <script>
        function removeParam(key, sourceURL) {
            let rtn = sourceURL.split("?")[0],
                param,
                params_arr = [],
                queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
            if (queryString !== "") {
                params_arr = queryString.split("&");
                for (let i = params_arr.length - 1; i >= 0; i -= 1) {
                    param = params_arr[i].split("=")[0];
                    if (param === key) {
                        params_arr.splice(i, 1);
                    }
                }
                if (params_arr.length) rtn = rtn + "?" + params_arr.join("&");
            }
            return rtn;
        }
    </script>
</footer>

</html>

