<?php
declare(strict_types=1);
require dirname(__DIR__) . '/autoload.php';
use App\Models\{Aluno, Professor, Livro, EBook, Historico};
use App\Services\EmprestimoService;
use App\Exceptions\{MaterialIndisponivelException, LimiteEmprestimoException, UsuarioBloqueadoException, EmprestimoInvalidoException};
use App\Enums\EmprestimoStatus;

$ok = 0; $fail = 0;
function check(bool $condition, string $name): void {
    global $ok, $fail;
    if ($condition) { echo "[OK] {$name}\n"; $ok++; } else { echo "[FALHOU] {$name}\n"; $fail++; }
}
function throws(string $class, callable $fn): bool { try { $fn(); } catch (Throwable $e) { return $e instanceof $class; } return false; }

$h = new Historico(); $s = new EmprestimoService($h);
$aluno = new Aluno('A1','Ana Souza','ana@example.com');
$livro = new Livro('L1','Livro teste','Autor',2020,'',100,'','',1);
$e = $s->realizarEmprestimo($aluno,$livro,new DateTimeImmutable('2026-10-01'));
check(throws(MaterialIndisponivelException::class, fn() => $s->realizarEmprestimo(new Aluno('A2','Bia','b@example.com'),$livro)), 'Material indisponível');
$s->registrarDevolucao($e,new DateTimeImmutable('2026-10-02'));
$alunoLimite = new Aluno('A3','Teste','t@example.com');
$l2 = new Livro('L2','L2','Autor',2020,'',10,'','',4);
for($i=0;$i<3;$i++) $s->realizarEmprestimo($alunoLimite,$l2,new DateTimeImmutable('2026-10-01'));
check(throws(LimiteEmprestimoException::class, fn() => $s->realizarEmprestimo($alunoLimite,$l2)), 'Limite de três empréstimos');
$bloqueado = new Aluno('A4','Bloqueado','b@example.com'); $bloqueado->bloquear();
check(throws(UsuarioBloqueadoException::class, fn() => $s->realizarEmprestimo($bloqueado,$l2)), 'Usuário bloqueado');
$e2=$s->realizarEmprestimo(new Aluno('A5','Caio','c@example.com'),$l2,new DateTimeImmutable('2026-10-01'));
$s->registrarDevolucao($e2,new DateTimeImmutable('2026-10-15'));
check(throws(EmprestimoInvalidoException::class, fn() => $s->registrarDevolucao($e2,new DateTimeImmutable('2026-10-16'))), 'Devolução duplicada');
$ebook = new EBook('E1','Digital','Autor',2024,'PDF',2.5,'',1);
$ee=$s->realizarEmprestimo(new Aluno('A6','Duda','d@example.com'),$ebook);
check(throws(MaterialIndisponivelException::class, fn() => $s->realizarEmprestimo(new Aluno('A7','Eva','e@example.com'),$ebook)), 'Limite de acessos do e-book');
$alunoMulta = new Aluno('A8','Multa','m@example.com');
$lm = new Livro('L3','Multa','Autor',2020,'',20,'','',1);
$em=$s->realizarEmprestimo($alunoMulta,$lm,new DateTimeImmutable('2026-10-01'));
$r=$s->registrarDevolucao($em,new DateTimeImmutable('2026-10-20'));
check($r['diasAtraso']===5 && $r['multa']===10.0, 'Atraso de cinco dias e multa de R$ 10,00');
$prof = new Professor('P1','Professor','p@example.com');
$lp = new Livro('L4','Prazo','Autor',2020,'',20,'','',1);
$ep=$s->realizarEmprestimo($prof,$lp,new DateTimeImmutable('2026-10-01'));
check($ep->dataPrevistaDevolucao->format('Y-m-d')==='2026-10-22', 'Prazo adicional de professor');
check($prof->taxaMultaDiaria===1.0, 'Multa diária de professor é R$ 1,00');
$s->cancelarEmprestimo($ep);
check($ep->status===EmprestimoStatus::CANCELADO && throws(EmprestimoInvalidoException::class, fn()=>$s->registrarDevolucao($ep)), 'Empréstimo cancelado não pode ser devolvido');
echo "\nResultado: {$ok} aprovados; {$fail} falharam.\n";
exit($fail > 0 ? 1 : 0);
