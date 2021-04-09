<?php

require '../../db/users_db.php';

const EMAIL = '';
const PASSWORD = '';

$userId = saveUser(EMAIL, PASSWORD);

echo $userId . PHP_EOL;
