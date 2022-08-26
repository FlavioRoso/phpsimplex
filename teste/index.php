<?php

include("../vendor/autoload.php");

use Flavio\Phpsimplex\Simplex;


$simplex = new Simplex(
    [5, 2],
    [
        [1, 0, "<=", 3],
        [0, 1, "<=", 4],
        [1, 2, "<=", 9],
    ]
);

// $simplex = new Simplex(
//     [2, 6],
//     [
//         [4, 3, "<=", 12],
//         [2, 1, "<=", 8],
//     ]
// );

// $simplex = new Simplex(
//     [30, 12, 15],
//     [
//         [9, 3, 5, "<=", 500],
//         [5, 4, 0, "<=", 350],
//         [3, 0, 2, "<=", 150],
//         [0, 0, 1, "<=", 20],
//     ]
// );

// $simplex = new Simplex(
//     [5, 4, 3],
//     [
//         [2, 3, 1, "<=", 5],
//         [4, 2, 2, "<=", 11],
//         [3, 2, 2, "<=", 8],
//     ]
// );
$contador = 0;

$simplex->formaPadrao();

$historico = $simplex->getHistorico();
$cabecalho = $simplex->getCabecalho();
$variaveisNaoBasicas = $simplex->getVariaveisNaoBasicas();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title></title>
</head>

<body>
    <div class="container">
        <? foreach ($historico as $tabelaDados) {
            $contador++;
            $posicaoColunaSelecionada = $tabelaDados["colunaSelecionada"];
            $posicaoLinhaSelecionada = $tabelaDados["linhaSelecionada"]
        ?>
            <h2>Tabela <?= $contador ?></h2>
            <table class="table mb-5">
                <? if ($posicaoColunaSelecionada) {  ?>
                    <colgroup>
                        <col span="<?= ($posicaoColunaSelecionada + 1) ?>">
                        <col style="background-color:yellow">
                    </colgroup>
                <? }  ?>

                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Variável Basica</th>
                        <? foreach ($cabecalho as $titulo) { ?>
                            <th scope="col"><?= $titulo ?></th>
                        <? } ?>
                        <th scope="col">Constante</th>
                        <th scope="col">Divisão</th>

                    </tr>
                </thead>
                <tbody>

                    <? foreach ($tabelaDados["tabela"] as $titulo => $linhas) {
                        $linhaSelecionada = $posicaoLinhaSelecionada == $titulo;
                        $estilo = "";
                        if ($linhaSelecionada) {
                            $estilo = "background-color:yellow";
                        }

                    ?>
                        <tr style="<?= $estilo ?>">
                            <th scope="col"><?= $titulo ?></th>
                            <? foreach ($linhas as $posicao => $colunas) {
                                $estiloPivo = "";
                                if ($linhaSelecionada && $posicao == $posicaoColunaSelecionada) {
                                    $estiloPivo = "background-color: red";
                                }

                            ?>
                                <td scope="col" style="<?= $estiloPivo ?>"><?= $colunas ?></td>
                            <? } ?>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
        <? } ?>

    </div>
</body>

</html>