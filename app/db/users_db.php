<?php

require_once 'db_constants.php';

/**
 * @param string $email
 * @param string $password
 * @return int|null
 */
function saveUser(string $email, string $password): ?int
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return null;
    }

    $passwordHash = password_hash(
        $password,
        PASSWORD_BCRYPT,
        [
            'cost' => 12,
        ]
    );

    $insertResult = pg_query_params($dbh,
        'INSERT INTO users (email, password) VALUES ($1, $2)',
        [
            $email, $passwordHash
        ]
    );
    if (!$insertResult) {
        print pg_last_error($dbh) . PHP_EOL;
        return null;
    }
    pg_free_result($insertResult);

    $selectResult = pg_query($dbh,
        'SELECT currval(\'users_id_seq\')'
    );
    if (!$selectResult) {
        print pg_last_error($dbh) . PHP_EOL;
        return null;
    }
    $row = pg_fetch_array($selectResult);

    pg_free_result($selectResult);
    pg_close($dbh);

    return $row ? $row[0] : null;
}


/**
 * @param string $email
 * @param string $password
 * @return bool
 */
function authorizeUser(string $email, string $password): bool
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return false;
    }

    $result = pg_select($dbh, 'users', ['email' => $email]);

    if (!$result || !array_key_exists('password', $result[0] )) {
        return false;
    }

    return password_verify($password, $result[0]['password']);
}