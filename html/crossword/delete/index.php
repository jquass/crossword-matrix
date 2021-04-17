<?php

require_once '../../../app/db/users_db.php';
include '../../../app/includes/login.php';

require_once '../../../app/db/crossword_db.php';

$puzzleId = null;
$savedPuzzle = null;
$result = null;

if ('GET' === $_SERVER['REQUEST_METHOD']) {

    if (array_key_exists('id', $_GET)) {
        $puzzleId = $_GET['id'];
    }

    $savedPuzzle = getSavedPuzzle($puzzleId);

} else if ('POST' === $_SERVER['REQUEST_METHOD']) {
    if ('delete' !== $_POST['form_type']) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }

    if (array_key_exists('id', $_POST)) {
        $puzzleId = $_POST['id'];
    }

    $result = deleteSavedPuzzle($puzzleId);

} else {
    die('invalid request method : ' . $_SERVER['REQUEST_METHOD']);
}

?>

<html>
<head>
    <link href="delete.css" rel="stylesheet">
</head>


<header>
    <div class="clearfix">
        <div class="header_div">
            <a href="#" onclick="window.location='../../index.php';">
                Puzzle Index
            </a>
        </div>

        <div class="header_div">
            <a href="#" onclick="window.location='../../dictionary/index.php';">
                Manage Dictionary
            </a>
        </div>
    </div>
</header>

<?php
if ($result) {
    die ('Deletion result: ' . $result);
}


if (!$puzzleId || !$savedPuzzle) {
    die('No valid ID or puzzle found');
}
?>

<h1>
    <?= "Delete puzzle {$savedPuzzle['puzzle_name']}?" ?>
</h1>

<form name="delete_puzzle" method="post" id="delete_puzzle">

    <input type="hidden" name="form_type" value="delete">

    <input type="hidden" name="id" value="<?= $puzzleId ?>">

    <input type="submit" value="Delete" name="btnSubmit">

</form>

</html>

