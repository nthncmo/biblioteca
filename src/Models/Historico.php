<?php
declare(strict_types=1);
namespace App\Models;
class Historico {
    private array $registros = [];
    public function registrar(string $acao, Usuario $usuario, Material $material, string $observacao = ''): void {
        $this->registros[] = ['data' => date('d/m/Y H:i:s'), 'acao' => $acao, 'usuario' => $usuario->nome,
            'material' => $material->titulo, 'observacao' => $observacao];
    }
    public function listarRegistros(): array { return $this->registros; }
    public function exibir(): string {
        return implode(PHP_EOL, array_map(fn($r) => "{$r['data']} | {$r['acao']} | {$r['usuario']} | {$r['material']} | {$r['observacao']}", $this->registros));
    }
}
