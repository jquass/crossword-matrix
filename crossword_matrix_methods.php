<?php

use JetBrains\PhpStorm\Pure;

require 'crossword_matrix_constants.php';

// First tries to find a template in the rows, then the columns
function findTemplate($puzzle): bool
{
    return findTemplateInScope($puzzle, 'row') || findTemplateInScope(transposePuzzle($puzzle), 'column');
}

// Tries to find a template in scope (row/column)
function findTemplateInScope($puzzle, $scope): bool
{
    foreach ($puzzle as $id => $values) {
        foreach (explode(SOLID, implode($values)) as $template) {
            if (validateTemplate($template)) {
                print "Found template in " . $scope . " " . $id . ": '" . $template . "'" . PHP_EOL;
            print_r(extractTemplate($values));
            return true;
            }
        }
    }
    return false;
}

function extractTemplate($values): array
{
    $start = 0;
    $end = 0;
    $foundField = 0;
    foreach ($values as $key => $value) {
        if ($value == SOLID) {
            // If we've already found at least two template fields, we can return the pattern
            if ($foundField >= 2) {
                break;
            }
            $start = $end = $key + 1;
            $foundField = 0;
            continue;
        } else if ($value == BLANK) {
            $start = $end = $key + 1;
            $foundField = 0;
            continue;
        } else if (preg_match('/[.;,]/', $value)) {
            $foundField++;
            $end = $key;
            continue;
        } else if (preg_match('/[a-zA-Z]/', $value)) {
            $end = $key;
            continue;
        }

        // If we're here, it's an unexpected character, so let's reset and continue
        $start = $end = $key + 1;
        $foundField = 0;
    }
    return $end == $start ? [] : array_slice($values, $start, $end - $start + 1);
}

#[Pure] function validateTemplate($template): bool
{
    return $template
        && !str_contains($template, BLANK)
        && (substr_count($template, WILD) +
            substr_count($template, CONSONANT) +
            substr_count($template, VOWEL) >= 2);
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