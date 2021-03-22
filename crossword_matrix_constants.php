<?php

const SOLID = '*';
const WILD = '.';
const CONSONANT = ',';
const VOWEL = ';';
const BLANK = '';

// Keeps the code D.R.Y. (Don't Repeat Yourself)
const EMPTY_PUZZLE_ROW = [BLANK, BLANK, BLANK, BLANK, BLANK];

// Template can start with one or more letters, or pattern fields
const START_REGEX = '[.;,a-zA-Z]+?';

// Template middle must have one or more pattern fields
const MIDDLE_REGEX = '[.;,]+';

// Template must end with one or more pattern fields, optionally can be surrounded my letters
const END_REGEX = '[a-zA-Z]?[.;,]+[a-zA-Z]?';

const TEMPLATE_REGEX = '/' . START_REGEX . MIDDLE_REGEX . END_REGEX . '/';