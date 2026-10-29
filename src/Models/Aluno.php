<?php
declare(strict_types=1);
namespace App\Models;
class Aluno extends Usuario {
    public function __construct(string $matricula, string $nome, string $email, string $telefone = '') {
        parent::__construct($matricula, $nome, $email, $telefone, true, 3, 2.00);
    }
}
