<?php
declare(strict_types=1);
namespace App\Enums;
enum EmprestimoStatus: string {
    case ATIVO = 'ATIVO';
    case DEVOLVIDO = 'DEVOLVIDO';
    case ATRASADO = 'ATRASADO';
    case CANCELADO = 'CANCELADO';
}
