<?php

require 'crossword_matrix_methods.php';

print '*** STARTING TESTING ***' . PHP_EOL;

testCrosswordMatrix(
    '*** Testing empty puzzle (no match expected)',
    [
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
    ]
);

testCrosswordMatrix(
    '*** Testing full puzzle (no match expected)',
    [
        [SOLID, SOLID, 'T', SOLID, SOLID],
        ['O', SOLID, 'A', SOLID, 'I'],
        ['P', 'A', 'P', 'E', 'R'],
        ['T', SOLID, 'E', SOLID, 'E'],
        [SOLID, SOLID, 'R', SOLID, SOLID],
    ]
);

testCrosswordMatrix(
    '*** Testing empty puzzle grid (no match expected)',
    [
        [SOLID, SOLID, BLANK, SOLID, SOLID],
        [BLANK, SOLID, BLANK, SOLID, BLANK],
        [BLANK, BLANK, BLANK, BLANK, BLANK],
        [BLANK, SOLID, BLANK, SOLID, BLANK],
        [SOLID, SOLID, BLANK, SOLID, SOLID],
    ]
);

testCrosswordMatrix(
    '*** Testing vertical puzzle template (column 2: .;,..)',
    [
        [SOLID, SOLID, WILD, SOLID, SOLID],
        ['O', SOLID, VOWEL, SOLID, 'I'],
        ['P', 'A', CONSONANT, 'E', 'R'],
        ['T', SOLID, WILD, SOLID, 'E'],
        [SOLID, SOLID, WILD, SOLID, SOLID],
    ]
);

testCrosswordMatrix(
    '*** Testing horizontal puzzle template (row 2: .;,..)',
    [
        [SOLID, SOLID, 'T', SOLID, SOLID],
        ['O', SOLID, 'A', SOLID, 'I'],
        [WILD, VOWEL, CONSONANT, WILD, WILD],
        ['T', SOLID, 'E', SOLID, 'E'],
        [SOLID, SOLID, 'R', SOLID, SOLID],
    ]


);

testCrosswordMatrix(
    '*** Testing standalone vertical spaced template (column 3 : S,.A,)',
    [
        [BLANK, BLANK, BLANK, 'S', BLANK],
        [BLANK, BLANK, BLANK, CONSONANT, BLANK],
        [BLANK, BLANK, BLANK, WILD, BLANK],
        [BLANK, BLANK, BLANK, 'A', BLANK],
        [BLANK, BLANK, BLANK, CONSONANT, BLANK],
    ]
);

testCrosswordMatrix(
    '*** Testing horizontal duplicate letters puzzle (row 4 : SR.A,T)',
    [
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        ['SR', WILD, 'A', CONSONANT, 'T'],
    ]
);

testCrosswordMatrix(
    '*** Testing horizontal pattern with solid ends (row 1 : .;,)',
    [
        EMPTY_PUZZLE_ROW,
        [SOLID, WILD, VOWEL, CONSONANT, SOLID],
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
    ]
);

testCrosswordMatrix(
    '*** Testing horizontal pattern with solid end (row 0 : R.;,)',
    [
        ['R', WILD, VOWEL, CONSONANT, SOLID],
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
    ]
);

testCrosswordMatrix(
    '*** Testing horizontal pattern with solid start (row 0 : .;RS)',
    [
        [SOLID, WILD, VOWEL, 'R', 'S'],
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
    ]
);


testCrosswordMatrix(
    '*** Testing horizontal pattern with blank ends (no match expected)',
    [
        EMPTY_PUZZLE_ROW,
        [BLANK, WILD, VOWEL, CONSONANT, BLANK],
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
    ]
);

testCrosswordMatrix(
    '*** Testing horizontal pattern with blank end (no match expected)',
    [
        EMPTY_PUZZLE_ROW,
        [CONSONANT, WILD, VOWEL, CONSONANT, BLANK],
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
    ]
);

testCrosswordMatrix(
    '*** Testing horizontal pattern with blank start (no match expected)',
    [
        EMPTY_PUZZLE_ROW,
        [BLANK, WILD, VOWEL, CONSONANT, CONSONANT],
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
    ]
);

print "*** DONE ***" . PHP_EOL;


function testCrosswordMatrix($msg, $matrix)
{
    print $msg . PHP_EOL;
    findTemplate($matrix);
    print PHP_EOL;
}
