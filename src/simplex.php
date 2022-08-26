<?php

namespace Flavio\Phpsimplex;


class Simplex
{

    /** @var array $variaveis_nao_basicas */
    private $variaveis_nao_basicas = [];

    /** @var array $variaveis_basicas */
    private $variaveis_basicas = [];

    /** @var array $variaveis */
    private $variaveis = [];

    /** @var array $cabecalhos */
    private $cabecalhos = [];

    /** @var array $condicoes */
    private $condicoes = [];

    /** @var array $historico_tabela */
    private $historico_tabela = [];


    public function getHistorico()
    {
        return $this->historico_tabela;
    }


    public function getCabecalho()
    {
        return $this->cabecalhos;
    }


    public function getVariaveisNaoBasicas()
    {
        return $this->variaveis_nao_basicas;
    }

    /**
     * construtor
     * @var array $variaveis
     * @var array $condicoes
     */
    public function __construct(array $variaveis, array $condicoes)
    {
        $this->condicoes = $condicoes;

        $this->adicionarVariavel("Z", 1);

        foreach ($variaveis as $indice => $variavel) {
            $titulo = "X" . ($indice + 1);
            $this->variaveis_nao_basicas[$titulo] = $variavel;
            $this->adicionarVariavel($titulo, -$variavel);
        }
    }

    /**
     * adiciona uma nova variavel no calculo
     * @var string $indice
     * @var array $variavel
     */
    private function adicionarVariavel($indice, $variavel)
    {
        $totalVariaveis = count($this->variaveis);
        $this->variaveis[$indice]  = $variavel;
    }

    /**
     * retorna valor variavel
     * @var string $indice
     * @return integer 
     */
    private function getValorVariavel($indice)
    {
        if (isset($this->variaveis[$indice])) {
            return $this->variaveis[$indice];
        }
        return null;
    }

    private function montarTabelaInicial()
    {
        $tabela = [];
        $quantidadeColunas = count($this->variaveis) + 2;
        $quantidadeVariaveisBasicas = count($this->variaveis_nao_basicas);
        $contadorCondicoes = $quantidadeVariaveisBasicas + 1;

        //montar linha da condicao a ser otimizada "Z"
        foreach ($this->variaveis as $indice => $variavel) {
            $this->cabecalhos[] = $indice;
            $tabela["Z"][] = $variavel;
        }
        $tabela["Z"]["constante"] = 0;
        $tabela["Z"]["divisao"] = null;

        //Linhas das variaveis
        foreach ($this->condicoes as $condicao) {
            $titulo = "X" . ($contadorCondicoes);

            //valor de Z = 0, e valor das variaveis nao basicas para condicao
            $z = [0];
            $valorNaoBasicaCondicoes = array_slice($condicao, 0, $quantidadeVariaveisBasicas);
            foreach ($valorNaoBasicaCondicoes as $valor) {
                array_push($z, $valor);
            }
            $tabela[$titulo] = $z;
            foreach ($this->variaveis_basicas as $tituloVariavel => $variavel) {
                $tabela[$titulo][] = + ($titulo == $tituloVariavel);
            }

            $tabela[$titulo]["constante"] = end($condicao);
            $tabela[$titulo]["divisao"] = null;

            $contadorCondicoes++;
        }

        return $tabela;
    }

    private function inicializarVariaveisBasicas()
    {
        $totalVariaveis = count($this->variaveis);
        $contador = $totalVariaveis;
        foreach ($this->condicoes as $condicao) {
            $this->adicionarVariavel("X" . ($contador), 0);
            $this->variaveis_basicas["X" . ($contador)] =  0;
            $contador++;
        }
    }

    /**
     * seleciona a coluna selecionada para otimização, serve para validar o fim da interação
     */
    private function selecionarColuna($tabela)
    {
        $z = $tabela["Z"];

        $minimo = $z[0];
        $posicao = 0;
        // retirando as colunas de constante e divisao;
        $quantidadeColunas = count($z) - 2;
        for ($i = 1; $i < $quantidadeColunas; $i++) {
            if ($z[$i] < $minimo) {
                $minimo = $z[$i];
                $posicao = $i;
            }
        }

        return $minimo < 0 ?
            [
                "posicao" => $posicao,
                "valor" => $minimo
            ] : null;
    }

    /**
     * seleciona a coluna selecionada para otimização, serve para validar o fim da interação
     */
    private function preencherDivisao($tabela, $colunaSelecionada)
    {
        foreach (array_slice($tabela, 1) as $posicao => $linha) {
            if (!empty($linha[$colunaSelecionada["posicao"]])) {
                $tabela[$posicao]["divisao"] = $tabela[$posicao]["constante"] / $linha[$colunaSelecionada["posicao"]];
            } else {
                $tabela[$posicao]["divisao"] = null;
            }
        }

        return $tabela;
    }

    /**
     * seleciona a linha com maior impacto em relacao a coluna selecionada
     */
    private function selecionarLinha($tabela)
    {
        $arrayFiltrado = array_filter($tabela, function ($linha, $posicao) {
            return $posicao != "Z" && $linha["divisao"] != null && $linha["divisao"] > 0;
        }, ARRAY_FILTER_USE_BOTH);
        $minimo = min(array_column($arrayFiltrado, "divisao"));
        $linhaSelecionada = array_filter($arrayFiltrado, function ($linha) use ($minimo) {
            return $linha["divisao"] == $minimo;
        });

        $posicao = array_keys($linhaSelecionada);
        $posicao = $posicao[0];

        return [
            "posicao" => $posicao,
            "valor" => $linhaSelecionada[$posicao]
        ];
    }

    /**
     * insere novo item a tabela e remove o anterios
     */
    private function novaInsersaoTabela($tabela, $linhaSelecionada, $colunaSelecionada)
    {
        $pivo = $linhaSelecionada["valor"][$colunaSelecionada["posicao"]];
        $posicaoLinhaAnterior = $linhaSelecionada['posicao'];

        foreach ($linhaSelecionada["valor"] as $indice => $colunas) {
            if ($pivo > 0 && $indice != "divisao") {
                $linhaSelecionada["valor"][$indice] = $colunas / $pivo;
            } else {
                $linhaSelecionada["valor"][$indice] = 0;
            }
        };

        $tabela[$this->cabecalhos[$colunaSelecionada["posicao"]]] = $linhaSelecionada["valor"];
        unset($tabela[$posicaoLinhaAnterior]);

        return $tabela;
    }

    /**
     * Recalcula os valores da nova tabela
     */
    private function recalcularLinhasTabela($tabela, $colunaSelecionada)
    {
        $posicaoNovaLinha = $this->cabecalhos[$colunaSelecionada["posicao"]];
        $novaLinha = $tabela[$posicaoNovaLinha];

        foreach (array_slice($tabela, 0, -1) as $posicaoLinha => $linha) {

            foreach ($linha as $posicaoColuna => $coluna) {
                if ($posicaoColuna != "divisao") {
                    $tabela[$posicaoLinha][$posicaoColuna] =
                        $linha[$posicaoColuna] -
                        $linha[$colunaSelecionada["posicao"]] *
                        $novaLinha[$posicaoColuna];
                }
            }
        }

        return $tabela;
    }

    /**
     * executa calculo para forma padrao
     */
    public function formaPadrao()
    {
        $this->inicializarVariaveisBasicas();
        $tabela = $this->montarTabelaInicial();
        $colunaSelecionada = $this->selecionarColuna($tabela);

        while ($colunaSelecionada) {
            $tabela = $this->preencherDivisao($tabela, $colunaSelecionada);
            $linhaSelecionada = $this->selecionarLinha($tabela);
            $this->historico_tabela[] = [
                "tabela" => $tabela,
                "linhaSelecionada" => $linhaSelecionada['posicao'],
                "colunaSelecionada" => $colunaSelecionada["posicao"],
            ];

            $tabela = $this->novaInsersaoTabela($tabela, $linhaSelecionada, $colunaSelecionada);
            $tabela = $this->recalcularLinhasTabela($tabela, $colunaSelecionada);
            $colunaSelecionada = $this->selecionarColuna($tabela);
        }

        $this->historico_tabela[] = [
            "tabela" => $tabela,
            "linhaSelecionada" => null,
            "colunaSelecionada" => null,
        ];
    }
}
