<?php

require_once 'db_constants.php';

const CONSONANT_REGEX = '[BCDFGHJKLMNPQRSTVWXYZ]';
const VOWEL_REGEX = '[AEIOUY]';

/**
 * @param int $limit
 * @return array
 */
function getDictionaryEntries(int $limit): array
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return [];
    }

    $result = pg_query_params($dbh,
        'SELECT * FROM dictionary ORDER BY id DESC LIMIT $1',
        [$limit]
    );
    if (!$result) {
        return [];
    }

    $return = [];
    while ($row = pg_fetch_assoc($result)) {
        $return[] = $row;
    }

    pg_free_result($result);
    pg_close($dbh);

    return $return;
}

/**
 * @param int $id
 * @return array|null
 */
function getDictionaryEntry(int $id): array|null
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return null;
    }

    $result = pg_query_params($dbh,
        'SELECT * FROM dictionary WHERE id = $1',
        [$id]
    );
    if (!$result) {
        return null;
    }

    $row = pg_fetch_assoc($result);

    pg_free_result($result);
    pg_close($dbh);

    return $row ?: null;
}

/**
 * @param string $words
 * @return bool
 */
function insertWords(string $words): bool
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return false;
    }

    foreach (preg_split('/\s+/', $words) as $word) {
        $cleanedWord = strtoupper(trim($word));
        if (preg_match('/[^a-zA-Z]+/', $cleanedWord)) {
            print "Skipping word with invalid characters {$cleanedWord}" . PHP_EOL;
            continue;
        }
        if (!$cleanedWord) {
            continue;
        }

        $insertResult = pg_query_params($dbh,
            'INSERT INTO dictionary (word) VALUES ($1) ON CONFLICT DO NOTHING',
            [$cleanedWord]
        );
        if (!$insertResult) {
            print pg_last_error($dbh) . PHP_EOL;
            continue;
        }
        pg_free_result($insertResult);
    }
    pg_close($dbh);

    return true;
}


/**
 * @param array $template
 * @param int $limit
 * @return array
 */
function getMatchingDictionaryEntries(array $template, int $limit): array
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return [];
    }

    $regex = '^';
    foreach ($template as $value) {
        if (preg_match('/[.]/', $value)) {
            $regex .= $value;
        } else if (preg_match('/[;]/', $value)) {
            $regex .= CONSONANT_REGEX;
        } else if (preg_match('/[,]/', $value)) {
            $regex .= VOWEL_REGEX;
        } else {
            $regex .= '[' . $value . ']';
        }
    }
    $regex .= '$';

    $result = pg_query_params($dbh,
        'SELECT * FROM dictionary WHERE word ~* $1 LIMIT $2',
        [$regex, $limit]
    );
    if (!$result) {
        return [];
    }

    $return = [];
    while ($row = pg_fetch_assoc($result)) {
        $return[] = $row;
    }

    pg_free_result($result);
    pg_close($dbh);

    return $return;
}

