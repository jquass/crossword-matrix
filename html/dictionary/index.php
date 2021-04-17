<?php

require '../../app/db/dictionary_db.php';

require_once '../../app/db/users_db.php';
include '../../app/includes/login.php';

const LIMIT = 100;

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    insertWords($_REQUEST['words']);
} else if ('GET' !== $_SERVER['REQUEST_METHOD']) {
    die('invalid request method : ' . $_SERVER['REQUEST_METHOD']);
}

$dictionaryEntries = getDictionaryEntries(LIMIT);

?>

<html>
<head>

</head>
<body>
<header>
    <a href="#" onclick="window.location='../index.php'+window.location.search;"><<< Puzzle Index</a>
</header>
<h1>Add Words</h1>
<form method="post" id="words">
    <input type="text" name="words" width="21" height="500">
    <input type="submit" value="Save" name="btnSubmit">
</form>

<h1>Last <?= LIMIT ?> Dictionary Entries</h1>

<?php
foreach ($dictionaryEntries as $entry) {
    echo "<div>
            <a href=\"#\" onclick=\"window.location='../dictionary/delete/index.php?id={$entry['id']}';\">
                X
            </a>
            {$entry['word']}
        </div><br>";
}
?>

</body>
</html>
