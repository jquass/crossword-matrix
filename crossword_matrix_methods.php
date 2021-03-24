<?php

use JetBrains\PhpStorm\Pure;

require 'crossword_matrix_constants.php';

/**
 * First looks in the puzzle rows then columns to find a matching pattern.
 * Returns true if a template is found.
 * @param array[] $puzzle
 * @return bool
 */
function findTemplate(array $puzzle): bool
{
    return findTemplateInScope($puzzle, 'row')
        || findTemplateInScope(transposePuzzle($puzzle), 'column');
}

/**
 * Tries to find a template in scope (row/column)
 * @param array[] $puzzle
 * @param $scope
 * @return bool
 */
function findTemplateInScope(array $puzzle, string $scope): bool
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

/**
 * @param string $template
 * @return bool
 */
#[Pure] function validateTemplate(string $template): bool
{
    return $template
        && !str_contains($template, BLANK)
        && (substr_count($template, WILD) +
            substr_count($template, CONSONANT) +
            substr_count($template, VOWEL) >= 2);
}

/**
 * @param string[] $values
 * @return array
 */
function extractTemplate(array $values): array
{
    $startPos = 0;
    $endPos = 0;
    $foundFieldCount = 0;
    foreach ($values as $key => $value) {
        if ($value == SOLID) {
            // If we've already found at least two template fields, we can return the pattern
            if ($foundFieldCount >= 2) {
                break;
            }
            $startPos = $endPos = $key + 1;
            $foundFieldCount = 0;
            continue;
        } else if ($value == BLANK) {
            $startPos = $endPos = $key + 1;
            $foundFieldCount = 0;
            continue;
        } else if (preg_match('/[.;,]/', $value)) {
            $foundFieldCount++;
            $endPos = $key;
            continue;
        } else if (preg_match('/[a-zA-Z]/', $value)) {
            $endPos = $key;
            continue;
        }

        // If we're here, it's an unexpected character, so let's reset and continue
        $startPos = $endPos = $key + 1;
        $foundFieldCount = 0;
    }
    return $endPos == $startPos
        ? []
        : array_slice($values, $startPos, $endPos - $startPos + 1);
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