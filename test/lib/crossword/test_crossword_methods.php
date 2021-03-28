<?php

require '../../../app/lib/crossword/crossword_methods.php';

const EMPTY_PUZZLE_ROW = [BLANK, BLANK, BLANK, BLANK, BLANK];

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

testCrosswordMatrix(
    '*** Testing whitespace is stripped (row 3 : P.A,T)',
    [
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        EMPTY_PUZZLE_ROW,
        [' P ', WILD . ' ', ' A', ' ' . CONSONANT, 'T    '],
        EMPTY_PUZZLE_ROW,
    ]
);


testCrosswordMatrix(
    '*** Testing vertical template in puzzle created from $_REQUEST (column 0 : S..)',
    getPuzzleFromRequest(
        [
            'c1' => 'S', 'c2' => BLANK, 'c3' => BLANK, 'c4' => BLANK, 'c5' => BLANK,
            'c6' => WILD, 'c7' => BLANK, 'c8' => BLANK, 'c9' => BLANK, 'c10' => BLANK,
            'c11' => WILD, 'c12' => BLANK, 'c13' => BLANK, 'c14' => BLANK, 'c15' => BLANK,
            'c16' => SOLID, 'c17' => BLANK, 'c18' => BLANK, 'c19' => BLANK, 'c20' => BLANK,
            'c21' => BLANK, 'c22' => BLANK, 'c23' => BLANK, 'c24' => BLANK, 'c25' => BLANK,
        ],
        5
    )
);


print "*** DONE ***" . PHP_EOL;


function testCrosswordMatrix($msg, $matrix)
{
    print $msg . PHP_EOL;
    print_r(findTemplate($matrix));
    print PHP_EOL;
}
