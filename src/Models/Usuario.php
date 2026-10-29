<?php
declare(strict_types=1);
namespace App\Models;
abstract class Usuario {
    protected int $emprestimosAtivos = 0;
    public function __construct(
        public readonly string $matricula, public readonly string $nome,
        public readonly string $email, public readonly string $telefone = '',
        protected bool $ativo = true, public readonly int $limiteEmprestimos = 3,
        public readonly float $taxaMultaDiaria = 2.00
    ) {}
    public function podeEmprestar(): bool { return $this->ativo && $this->emprestimosAtivos < $this->limiteEmprestimos; }
    public function registrarEmprestimo(): void {
        if (!$this->podeEmprestar()) throw new \App\Exceptions\LimiteEmprestimoException('Usuário não pode realizar novo empréstimo.');
        $this->emprestimosAtivos++;
    }
    public function registrarDevolucao(): void { if ($this->emprestimosAtivos > 0) $this->emprestimosAtivos--; }
    public function isAtivo(): bool { return $this->ativo; }
    public function bloquear(): void { $this->ativo = false; }
    public function desbloquear(): void { $this->ativo = true; }
    public function getEmprestimosAtivos(): int { return $this->emprestimosAtivos; }
    public function diasAdicionaisPrazo(): int { return 0; }
    public function consultarDados(): string { return "{$this->nome} ({$this->matricula}) — {$this->email}"; }
}
