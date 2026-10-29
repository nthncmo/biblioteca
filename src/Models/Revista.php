<?php
declare(strict_types=1);
namespace App\Models;
class Revista extends Material {
    public function __construct(string $codigo, string $titulo, string $autor, int $anoPublicacao,
        public readonly int $edicao = 1, public readonly int $volume = 1,
        public readonly string $mesPublicacao = '', int $quantidadeExemplares = 1) {
        parent::__construct($codigo, $titulo, $autor, $anoPublicacao, $quantidadeExemplares);
    }
    protected function prazoDias(): int { return 7; }
}
