<?php
session_start();

date_default_timezone_set('America/Sao_Paulo');

// Define a função de autoload
spl_autoload_register(function ($nomeClasse) {
	// Define o caminho inicial das classes
	$pastaClasses = '../classes/';
		
	// Possíveis caminhos onde as classes podem estar
	$possiveisPastas = [
		$pastaClasses,
		$pastaClasses . 'base/',
		$pastaClasses . 'models/',
		$pastaClasses . 'views/',
		$pastaClasses . 'controllers/'
	];

	// Procurar as classes em todas as pastas
	foreach ($possiveisPastas as $pasta) {
		$nomeCompletoArquivo = $pasta . $nomeClasse . '.php';
			
		if (file_exists($nomeCompletoArquivo)) {
			require_once $nomeCompletoArquivo;
			break;
		}
	}
});
?>