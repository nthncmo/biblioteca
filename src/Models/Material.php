<?php
declare(strict_types=1);
namespace App\Models;

abstract class Material {
    protected int $disponiveis;
    public function __construct(
        public readonly string $codigo,
        public readonly string $titulo,
        public readonly string $autor,
        public readonly int $anoPublicacao,
        protected int $quantidadeExemplares = 1
    ) {
        if ($quantidadeExemplares < 0) throw new \InvalidArgumentException('A quantidade não pode ser negativa.');
        $this->disponiveis = $quantidadeExemplares;
    }
    public function consultarDisponibilidade(): bool { return $this->disponiveis > 0; }
    public function emprestarExemplar(): void {
        if (!$this->consultarDisponibilidade()) throw new \App\Exceptions\MaterialIndisponivelException("Material '{$this->titulo}' indisponível.");
        $this->disponiveis--;
    }
    public function devolverExemplar(): void {
        if ($this->disponiveis < $this->quantidadeExemplares) $this->disponiveis++;
    }
    public function consultarQuantidadeDisponivel(): int { return $this->disponiveis; }
    public function prazoMaximoEmprestimo(): int { return $this->prazoDias(); }
    abstract protected function prazoDias(): int;
    public function exibirInformacoes(): string {
        return "{$this->titulo} — {$this->autor} ({$this->anoPublicacao}) | Disponíveis: {$this->disponiveis}";
    }
}
