<?php
declare(strict_types=1);
namespace App\Models;
class Professor extends Usuario {
    public function __construct(string $matricula, string $nome, string $email, string $telefone = '') {
        parent::__construct($matricula, $nome, $email, $telefone, true, 5, 1.00);
    }
    public function diasAdicionaisPrazo(): int { return 7; }
}
