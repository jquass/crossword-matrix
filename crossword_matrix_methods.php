<?php

// First tries to find a template in the rows, then the columns
function findTemplate($puzzle)
{
    $success = findTemplateInScope($puzzle, 'row');
    if (!$success) {
        $transposedPuzzle = transposePuzzle($puzzle);
        findTemplateInScope($transposedPuzzle, 'column');
    }
}

// Tries to find a template in scope (row/column)
function findTemplateInScope($puzzle, $scope): bool
{
    foreach ($puzzle as $id => $values) {
        $match = findMatch($values);
        if ($match) {
            print "Found template in " . $scope . " " . $id . ": '" . $match . "'" . PHP_EOL;
            print_r(extractTemplate($values));
            return true;
        }
    }
    return false;
}

function extractTemplate($values): array
{
    $start = 0;
    $end = 0;
    $foundField = false;
    foreach ($values as $key => $value) {

        // Letter or field, extend end of template
        if (preg_match('/[.;,a-zA-Z]/', $value)) {
            $end = $key;
        }
        // Once we've found a field, we mark $foundField as true
        if (preg_match('/[.;,]/', $value)) {
            $foundField = true;
        }

        // Check for non-pattern fields
        if ($value == BLANK || preg_match('/[*]/', $value)) {
            // If we've already found a field, we can return the pattern
            if ($foundField) {
                break;
            }
            // Otherwise, reset the $start and $end and keep looking
            $start = $end = $key + 1;
        }
    }
    return $end == $start ? [] : array_slice($values, $start, $end - $start + 1);
}

// Checks the values for a template
function findMatch($values)
{
    $valuesString = implode($values);
    preg_match(TEMPLATE_REGEX, $valuesString, $matches);
    return $matches ? $matches[0] : null;
}

// Transposes the puzzle, turning the rows to columns
function transposePuzzle($puzzle): array
{
    $transposedPuzzle = [];
    foreach ($puzzle as $row) {
        foreach ($row as $id => $value) {
            $transposedPuzzle[$id][] = $value;
        }
    }
    return $transposedPuzzle;
}