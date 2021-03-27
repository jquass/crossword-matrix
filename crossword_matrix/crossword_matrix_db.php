<?php

const CONNECTION_STRING = 'host=localhost dbname=crossword_puzzles user=crossword_user';

/**
 * @param string $name
 * @param array $puzzle
 * @return int|null
 */
function savePuzzle(string $name, array $puzzle): int|null
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return null;
    }

    $escapedPuzzle = pg_escape_string(serialize($puzzle));
    $insertResult = pg_query($dbh,
        "INSERT INTO puzzles (puzzle_name, puzzle) VALUES('{$name}', '{$escapedPuzzle}')"
    );
    if (!$insertResult) {
        print pg_last_error($dbh) . PHP_EOL;
        return null;
    }
    pg_free_result($insertResult);

    $selectResult = pg_query($dbh,
    "SELECT currval('puzzles_id_seq')"
    );
    if (!$selectResult) {
        print pg_last_error($dbh) . PHP_EOL;
        return null;
    }
    $row = pg_fetch_array($selectResult);

    pg_close($dbh);

    return $row ? $row[0] : null;
}

/**
 * @param int $id
 * @return array|null
 */
function getSavedPuzzle(int $id): array|null
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return null;
    }

    $result = pg_query($dbh,
        "SELECT * FROM puzzles WHERE id = {$id}"
    );
    if (!$result) {
        return [];
    }

    $row = pg_fetch_assoc($result);

    pg_free_result($result);
    pg_close($dbh);

    return $row ?: [];
}

/**
 * @param int $id
 * @param string $name
 * @param array $puzzle
 * @return array|null
 */
function updateSavedPuzzle(int $id, string $name, array $puzzle): array|null
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return null;
    }

    $result = pg_update($dbh,
        'puzzles',
        [
            'puzzle_name' => $name,
            'puzzle' => pg_escape_string(serialize($puzzle))
        ],
        ['id' => $id]
    );

    pg_close($dbh);

    return $result ? getSavedPuzzle($id) : null;
}


