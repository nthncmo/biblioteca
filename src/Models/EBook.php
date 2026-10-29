<?php
declare(strict_types=1);
namespace App\Models;
class EBook extends Material {
    private int $acessosAtivos = 0;
    public function __construct(string $codigo, string $titulo, string $autor, int $anoPublicacao,
        public readonly string $formatoArquivo = 'PDF', public readonly float $tamanhoArquivo = 0,
        public readonly string $linkAcesso = '', private readonly int $maximoAcessos = 1) {
        parent::__construct($codigo, $titulo, $autor, $anoPublicacao, max(1, $maximoAcessos));
    }
    public function consultarDisponibilidade(): bool { return $this->acessosAtivos < $this->maximoAcessos; }
    public function emprestarExemplar(): void {
        if (!$this->consultarDisponibilidade()) throw new \App\Exceptions\MaterialIndisponivelException("Acessos simultâneos esgotados para '{$this->titulo}'.");
        $this->acessosAtivos++;
    }
    public function devolverExemplar(): void { if ($this->acessosAtivos > 0) $this->acessosAtivos--; }
    public function consultarQuantidadeDisponivel(): int { return $this->maximoAcessos - $this->acessosAtivos; }
    public function getMaximoAcessosSimultaneos(): int { return $this->maximoAcessos; }
    protected function prazoDias(): int { return 21; }
}
