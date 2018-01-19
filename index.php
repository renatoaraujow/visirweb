<?php
header('Content-Type: text/html; charset=utf-8');

//error_reporting(0);
$ponteiro = fopen ("visir.txt", "r");

$linhas = [];

while (!feof ($ponteiro))
    $linhas[] = fgets($ponteiro, 4096);

$registro = "";
foreach ($linhas as $numeroLinha => $linha) {
    //pega data e hora
    if(strpos($linha, "proto_http") !== false && strpos($linhas[$numeroLinha-1], "proto_http") === false)
        $registro = substr($linha, 1, 19);

    if()

    echo $registro . "<br />";
}

fclose ($ponteiro);
?>
