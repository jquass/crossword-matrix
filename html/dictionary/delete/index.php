<?php

require_once '../../../app/db/users_db.php';
include '../../../app/includes/login.php';

require_once '../../../app/db/dictionary_db.php';

$wordId = null;
$savedWord = null;
$result = null;

if ('GET' === $_SERVER['REQUEST_METHOD']) {

    if (array_key_exists('id', $_GET)) {
        $wordId = $_GET['id'];
    }

    $savedWord = getDictionaryEntry($wordId);

} else if ('POST' === $_SERVER['REQUEST_METHOD']) {

    switch ($_POST['form_type']) {
        case 'delete':
            if (array_key_exists('id', $_POST)) {
                $wordId = $_POST['id'];
            }

            $result = deleteSavedWord($wordId);
            break;
        default:
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
    }

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


if (!$wordId || !$savedWord) {
    die('No valid ID or dictionary entry found');
}
?>

<h1>
    <?= "Delete dictionary entry {$savedWord['word']}?" ?>
</h1>

<form name="delete_dictionary_entry" method="post" id="delete_dictionary_entry">

    <input type="hidden" name="form_type" value="delete">

    <input type="hidden" name="id" value="<?= $wordId ?>">

    <input type="submit" value="Delete" name="btnSubmit">

</form>

</html>

