<?php 
class Usuario {
	public $Email;
	public $Nome;
	public $Senha;
	public $Tipo;

	// Construtor
	public function __construct($email = null, $nome = null, $senha = null, $tipo = null) {
		$this->Email = $email;
		$this->Nome = $nome;
		$this->Senha = $senha;
		$this->Tipo = $tipo;
	}}
?>