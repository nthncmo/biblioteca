<?php
declare(strict_types=1);
namespace App\Models;
class Livro extends Material {
    public function __construct(string $codigo, string $titulo, string $autor, int $anoPublicacao,
        public readonly string $isbn = '', public readonly int $numeroPaginas = 0,
        public readonly string $editora = '', public readonly string $genero = '', int $quantidadeExemplares = 1) {
        parent::__construct($codigo, $titulo, $autor, $anoPublicacao, $quantidadeExemplares);
    }
    protected function prazoDias(): int { return 14; }
}
