<?php

include("../vendor/autoload.php");

use Flavio\Phpsimplex\Simplex;

echo '<pre>';

// $simplex = new Simplex(
//     [5, 2],
//     [
//         [1, 0, "<=", 3],
//         [0, 1, "<=", 4],
//         [1, 2, "<=", 9],
//     ]
// );

// $simplex = new Simplex(
//     [2, 6],
//     [
//         [4, 3, "<=", 12],
//         [2, 1, "<=", 8],
//     ]
// );

$simplex = new Simplex(
    [30, 12, 15],
    [
        [9, 3, 5, "<=", 500],
        [5, 4, 0, "<=", 350],
        [3, 0, 2, "<=", 150],
    ]
);


$simplex->formaPadrao();
