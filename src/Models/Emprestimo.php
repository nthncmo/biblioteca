<?php
declare(strict_types=1);
namespace App\Models;
use App\Enums\EmprestimoStatus;
use DateTimeImmutable;
class Emprestimo {
    public EmprestimoStatus $status = EmprestimoStatus::ATIVO;
    public ?DateTimeImmutable $dataRealDevolucao = null;
    public float $multa = 0.0;
    public function __construct(public readonly string $codigo, public readonly Usuario $usuario,
        public readonly Material $material, public readonly DateTimeImmutable $dataEmprestimo,
        public readonly DateTimeImmutable $dataPrevistaDevolucao) {}
    public function marcarDevolvido(DateTimeImmutable $data, float $multa): void {
        $this->dataRealDevolucao = $data; $this->multa = $multa; $this->status = EmprestimoStatus::DEVOLVIDO;
    }
    public function marcarCancelado(): void { $this->status = EmprestimoStatus::CANCELADO; }
}
