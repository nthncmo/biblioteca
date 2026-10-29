<?php
declare(strict_types=1);
namespace App\Services;
use App\Enums\EmprestimoStatus;
use App\Exceptions\{EmprestimoInvalidoException, LimiteEmprestimoException, MaterialIndisponivelException, UsuarioBloqueadoException};
use App\Models\{Emprestimo, Historico, Material, Usuario};
use DateTimeImmutable;

class EmprestimoService {
    private int $sequencialCodigo = 1;
    public function __construct(private readonly Historico $historico) {}
    public function realizarEmprestimo(Usuario $usuario, Material $material, ?DateTimeImmutable $dataEmprestimo = null): Emprestimo {
        $this->validarElegibilidade($usuario, $material);
        $dataEmprestimo ??= new DateTimeImmutable('today');
        $prazo = $material->prazoMaximoEmprestimo() + $usuario->diasAdicionaisPrazo();
        $dataPrevista = $dataEmprestimo->modify("+{$prazo} days");
        $material->emprestarExemplar();
        try { $usuario->registrarEmprestimo(); } catch (\Throwable $e) { $material->devolverExemplar(); throw $e; }
        $codigo = sprintf('EMP%04d', $this->sequencialCodigo++);
        $emprestimo = new Emprestimo($codigo, $usuario, $material, $dataEmprestimo, $dataPrevista);
        $this->historico->registrar('EMPRESTIMO', $usuario, $material, "Código: {$codigo}");
        return $emprestimo;
    }
    public function registrarDevolucao(Emprestimo $emprestimo, ?DateTimeImmutable $dataDevolucao = null): array {
        if ($emprestimo->status !== EmprestimoStatus::ATIVO && $emprestimo->status !== EmprestimoStatus::ATRASADO) {
            throw new EmprestimoInvalidoException("O empréstimo não está ativo (Status: {$emprestimo->status->value}).");
        }
        $dataDevolucao ??= new DateTimeImmutable('today');
        $dias = max(0, (int)$emprestimo->dataPrevistaDevolucao->diff($dataDevolucao)->format('%r%a'));
        $multa = $dias * $emprestimo->usuario->taxaMultaDiaria;
        $emprestimo->material->devolverExemplar();
        $emprestimo->usuario->registrarDevolucao();
        $emprestimo->marcarDevolvido($dataDevolucao, $multa);
        if ($dias > 0) $this->historico->registrar('ATRASO', $emprestimo->usuario, $emprestimo->material, "{$dias} dia(s) de atraso - Multa: R$ " . number_format($multa, 2, ',', '.'));
        $this->historico->registrar('DEVOLUCAO', $emprestimo->usuario, $emprestimo->material, "Código: {$emprestimo->codigo}");
        return ['diasAtraso' => $dias, 'multa' => $multa, 'status' => $emprestimo->status];
    }
    public function cancelarEmprestimo(Emprestimo $emprestimo): void {
        if ($emprestimo->status !== EmprestimoStatus::ATIVO) throw new EmprestimoInvalidoException('Somente empréstimos ativos podem ser cancelados.');
        $emprestimo->material->devolverExemplar();
        $emprestimo->usuario->registrarDevolucao();
        $emprestimo->marcarCancelado();
        $this->historico->registrar('CANCELAMENTO', $emprestimo->usuario, $emprestimo->material, "Código: {$emprestimo->codigo}");
    }
    private function validarElegibilidade(Usuario $usuario, Material $material): void {
        if (!$usuario->isAtivo()) throw new UsuarioBloqueadoException("Usuário '{$usuario->nome}' está bloqueado.");
        if (!$usuario->podeEmprestar()) throw new LimiteEmprestimoException("Usuário '{$usuario->nome}' atingiu o limite de empréstimos.");
        if (!$material->consultarDisponibilidade()) throw new MaterialIndisponivelException("Material '{$material->titulo}' não está disponível.");
    }
}
