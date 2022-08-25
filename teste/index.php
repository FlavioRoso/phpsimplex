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

$simplex = new Simplex(
    [2, 6],
    [
        [4, 3, "<=", 12],
        [2, 1, "<=", 8],
    ]
);


$simplex->formaPadrao();
