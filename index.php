<?php
//header('Content-Type: text/html; charset=utf-8');

//error_reporting(0);

//echo "Começando leitura do arquivo...<br><br>";

$xml = simplexml_load_file('visir.xml');

print_r($xml);




/*
//ob_start();
$ponteiro = fopen ("visir.txt", "r");

while (!feof ($ponteiro)) {
	$linha = fgets($ponteiro, 4096);
	echo nl2br($linha);
}

fclose ($ponteiro);
//$contents = ob_get_contents();
//ob_end_clean();

//echo ($contents);

/*
function limpaString($string) {
	
	$string = preg_replace("/[^0-9a-zA-Z\.]+/", '', $string);
	$string = str_replace(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], '', $string);
	$string = str_replace(['/','.','-',' ',',','_','<br>'], '', $string);
	
	return $string;
}

$contents = str_replace("\n", '\n', $contents);
$contents = str_replace("\r", '\r', $contents);
//$contents = str_replace('\r\n\r\n', "[2br]", $contents);
//$contents = str_replace('\r\n', "<br>", $contents);
$contents = str_replace('\r\n', "[1br]", $contents);

//$titles = explode('[2br]', $contents);

$linhas = explode("[1br]", $contents);
$texto_completo = "";
foreach($linhas as $key => $linha) {
	if (strpos(substr($linha, 0, 10), ' - ') !== false && is_numeric(trim(substr($linha, 0, 3))) && strpos(substr($linha, 0, 5), '/') === false && ctype_upper(limpaString($linha))) {
		$texto_completo .= "[musica]" . $linha . "<br>";
	} else {
		$texto_completo .= $linha . "<br>";
	}
}

$musicas = explode("[musica]", $texto_completo);

echo "<pre>";
array_shift($musicas);


//print_r($musicas);

foreach($musicas as $musica) {
	$nome_musica = explode("<br>", $musica)[0];
	
	$name = "musicas/" . $nome_musica . ".txt";
	
	$text = str_replace("<br>", "\r\n", $musica);
	
	$file = fopen($name, 'a');
	
	fwrite($file, $text);
	
	fclose($file);
}
*/
?>