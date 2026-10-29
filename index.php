<?php
declare(strict_types=1);
require __DIR__ . '/autoload.php';
use App\Models\{Aluno, Professor, Livro, Historico};
use App\Services\EmprestimoService;

$historico = new Historico();
$service = new EmprestimoService($historico);
$livro = new Livro('L001', 'Dom Casmurro', 'Machado de Assis', 1899, '9780000000000', 256, 'Editora Exemplo', 'Romance', 2);
$aluna = new Aluno('A001', 'Ana Souza', 'ana@example.com');
$professor = new Professor('P001', 'Carlos Mendes', 'carlos@example.com');
$dataEmprestimo = new DateTimeImmutable('2026-10-01');
$emprestimo = $service->realizarEmprestimo($aluna, $livro, $dataEmprestimo);
$resultado = $service->registrarDevolucao($emprestimo, new DateTimeImmutable('2026-10-20'));
$emprestimoProfessor = $service->realizarEmprestimo($professor, $livro, $dataEmprestimo);

echo "=== Sistema de Biblioteca ===\n";
echo "Aluna: {$aluna->nome}\nLivro: {$livro->titulo}\nVencimento: " . $emprestimo->dataPrevistaDevolucao->format('d/m/Y') . "\n";
echo "Atraso: {$resultado['diasAtraso']} dia(s)\nMulta: R$ " . number_format($resultado['multa'], 2, ',', '.') . "\n\n";
echo "Professor: {$professor->nome}\nVencimento: " . $emprestimoProfessor->dataPrevistaDevolucao->format('d/m/Y') . "\n";
