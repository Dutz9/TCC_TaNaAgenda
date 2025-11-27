<?php 
class Curso {
    public $cd_curso;
    public $nm_curso;
    public $ic_periodo;

    public function __construct($nm_curso = null, $ic_periodo = null) {
        $this->nm_curso = $nm_curso;
        $this->ic_periodo = $ic_periodo;
    }
}
?>