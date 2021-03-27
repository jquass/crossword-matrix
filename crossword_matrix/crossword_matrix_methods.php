<?php

use JetBrains\PhpStorm\Pure;

require 'crossword_matrix_constants.php';

/**
 * First looks in the puzzle rows then columns to find a matching pattern.
 * Returns true if a template is found.
 * @param array[] $puzzle
 * @return array
 */
function findTemplate(array $puzzle): array
{
    $template = findTemplateInScope($puzzle, 'row');
    return $template ?: findTemplateInScope(transposePuzzle($puzzle), 'column');
}

/**
 * Tries to find a template in scope (row/column)
 * @param array[] $puzzle
 * @param $scope
 * @return array
 */
function findTemplateInScope(array $puzzle, string $scope): array
{
    foreach ($puzzle as $rowId => $row) {
        $cleanedValues = [];
        foreach ($row as $id => $value) {
            $cleanedValues[] = str_replace(' ', '', $value) ?: BLANK;
        }
        foreach (explode(SOLID, implode($cleanedValues)) as $template) {
            if (validateTemplate($template)) {
                return extractTemplate($row);
            }
        }
    }
    return [];
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
    $pos = -1;
    $templateStartPos = 0;
    $templateEndPos = 0;
    $foundFieldCount = 0;
    foreach ($values as $value) {
        $pos++;
        if (preg_match('/[*]/', $value)) {
            // If we've already found at least two template fields, we can return the pattern
            if ($foundFieldCount >= 2) {
                break;
            }
            $templateStartPos = $templateEndPos = $pos + 1;
            $foundFieldCount = 0;
            continue;
        } else if (preg_match('/[.;,]/', $value)) {
            $foundFieldCount++;
            $templateEndPos = $pos;
            continue;
        } else if (preg_match('/[a-zA-Z]/', $value)) {
            $templateEndPos = $pos;
            continue;
        } else if (preg_match('/\s/', $value)) {
            $templateStartPos = $templateEndPos = $pos + 1;
            $foundFieldCount = 0;
            continue;
        }

        // If we're here, it's an unexpected character, so let's reset and continue
        $templateStartPos = $templateEndPos = $pos + 1;
        $foundFieldCount = 0;
    }
    return array_slice($values, $templateStartPos, $templateEndPos - $templateStartPos + 1);
}

/**
 * Transposes the puzzle, turning the rows to columns
 * @param array[] $puzzle
 * @return array
 */
function transposePuzzle(array $puzzle): array
{
    $transposedPuzzle = [];
    foreach ($puzzle as $row) {
        $rowId = 0;
        foreach ($row as $id => $value) {
            $transposedPuzzle[$rowId][$id] = $value;
            $rowId++;
        }
    }
    return $transposedPuzzle;
}

/**
 * @param array $request
 * @Param int $size
 * @return array[] $puzzle
 */
#[Pure] function getPuzzleFromRequest(array $request, int $size): array
{
    $puzzle = [];
    $cell = 1;
    $row = 0;
    while ($cell <= $size * $size) {
        while ($cell <= $size * ($row + 1)) {
            $name = 'c' . $cell;
            $value = array_key_exists($name, $request) ? $request[$name] : BLANK;
            $cleanedValue = str_replace(' ', '', $value) ?: BLANK;
            $puzzle[$row][$name] = $cleanedValue;
            $cell++;
        }
        $row++;
    }
    return $puzzle;
}

/**
 * @param array[] $puzzle
 * @return array
 */
function convertPuzzleToOneDimension(array $puzzle): array
{
    $oneDimensionalPuzzle = [];
    foreach ($puzzle as $puzzlePiece) {
        $oneDimensionalPuzzle += $puzzlePiece;
    }
    return $oneDimensionalPuzzle;
}