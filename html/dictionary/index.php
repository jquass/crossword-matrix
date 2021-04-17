<?php

require '../../app/db/dictionary_db.php';

require_once '../../app/db/users_db.php';
include '../../app/includes/login.php';

const LIMIT = 100;

if ('POST' === $_SERVER['REQUEST_METHOD']) {

    switch ($_POST['form_type']) {
        case 'create_dictionary_entries':
            insertWords($_POST['words']);
            break;
        default:
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
    }

} else if ('GET' !== $_SERVER['REQUEST_METHOD']) {
    die('invalid request method : ' . $_SERVER['REQUEST_METHOD']);
}

$dictionaryEntries = getDictionaryEntries(LIMIT);

?>

<html>
<head>
    <link href="dictionary.css" rel="stylesheet">
</head>
<body>
<header>

    <header>
        <div class="clearfix">
            <div class="header_div">
                <a href="#" onclick="window.location='../index.php';">
                    Puzzle Index
                </a>
            </div>

            <div class="header_div">
                <a href="#" onclick="window.location='../dictionary/index.php';">
                    Manage Dictionary
                </a>
            </div>
        </div>
    </header>

</header>
<h1>Add Words</h1>
<form method="post" id="words">
    <input type="hidden" name="form_type" value="create_dictionary_entries">

    <label>
        <textarea name="words" rows="5" cols="50"></textarea>
    </label>
    <br>
    <input type="submit" value="Save" name="btnSubmit">
</form>

<h1>Last <?= LIMIT ?> Dictionary Entries</h1>

<div class="clearfix">
<?php
foreach ($dictionaryEntries as $entry) {
    echo "<div class=\"dictionary_entry\">
            <a href=\"#\" onclick=\"window.location='../dictionary/delete/index.php?id={$entry['id']}';\">X</a>
             {$entry['word']}
        </div><br>";
}
?>
</div>

</body>
</html>
