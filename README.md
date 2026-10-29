# Gestão de Biblioteca em PHP

Sistema didático para gerenciar materiais, usuários, empréstimos, devoluções, prazos e multas usando Programação Orientada a Objetos.

## Requisitos
- PHP 8.1 ou superior
- Composer é opcional

## Executar
```bash
php index.php
```

## Testes automatizados
```bash
php tests/run.php
```

Se tiver Composer instalado:
```bash
composer dump-autoload
composer test
composer demo
```

## Regras principais
- Aluno: até 3 empréstimos ativos e multa de R$ 2,00 por dia de atraso.
- Professor: até 5 empréstimos ativos, mais 7 dias no prazo e multa de R$ 1,00 por dia.
- Prazo base: livro 14 dias, revista 7 dias e e-book 21 dias.
- O sistema impede empréstimos sem disponibilidade e devoluções duplicadas.
- Os dados ficam em memória; não há banco de dados neste projeto.

## Estrutura
- `src/Models`: materiais, usuários, empréstimos e histórico.
- `src/Services`: regras de empréstimo e devolução.
- `src/Enums`: status do empréstimo.
- `src/Exceptions`: exceções específicas do domínio.
- `tests/run.php`: testes automatizados.
