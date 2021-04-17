<?php

require_once 'db_constants.php';

/**
 * @param string $name
 * @param array $puzzle
 * @return int|null
 */
function savePuzzle(string $name, array $puzzle): ?int
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return null;
    }

    $insertResult = pg_query_params($dbh,
        'INSERT INTO puzzles (puzzle_name, puzzle) VALUES ($1, $2)',
        [$name, serialize($puzzle)]
    );
    if (!$insertResult) {
        print pg_last_error($dbh) . PHP_EOL;
        return null;
    }
    pg_free_result($insertResult);

    $selectResult = pg_query($dbh,
        'SELECT currval(\'puzzles_id_seq\')'
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
 * @param int $id
 * @return array|null
 */
function getSavedPuzzle(int $id): ?array
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return null;
    }

    $result = pg_query_params($dbh,
        'SELECT * FROM puzzles WHERE id = $1',
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
 * @return array|null
 */
function getSavedPuzzles(): ?array
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return null;
    }

    $result = pg_query($dbh,
        'SELECT * FROM puzzles ORDER BY id DESC LIMIT 20'
    );
    if (!$result) {
        return null;
    }

    $rows = [];
    while ($row = pg_fetch_assoc($result)) {
        $rows[] = $row;
    }

    pg_free_result($result);
    pg_close($dbh);

    return $rows;
}

/**
 * @param int $id
 * @param array $puzzle
 * @param string|null $name
 * @return array|null
 */
function updateSavedPuzzle(int $id, array $puzzle, string $name = null): ?array
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return null;
    }

    $params = ['puzzle' => serialize($puzzle)];
    if ($name) {
        $params['puzzle_name'] = $name;
    }

    $result = pg_update($dbh, 'puzzles', $params, ['id' => $id]);

    pg_close($dbh);

    return $result ? getSavedPuzzle($id) : null;
}

/**
 * @param int $id
 * @return bool|string|null
 */
function deleteSavedPuzzle(int $id)
{
    $dbh = pg_connect(CONNECTION_STRING);
    if (!$dbh) {
        return null;
    }

    return pg_delete($dbh, 'puzzles', ['id' => $id]);
}