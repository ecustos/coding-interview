# Mini eCustos

API Laravel simplificada para administrar orçamentos compostos por etapas, composições e insumos.

O projeto foi criado como base para entrevista técnica. Ele prioriza uma implementação pequena, executável localmente e fácil de explorar por API.

## Desafio Técnico

Este repositório é uma versão mini do módulo de orçamentos do eCustos. A API já possui uma estrutura inicial de `budgets` e `budget_components`, mas ela foi mantida propositalmente simples e incompleta para avaliação técnica.

O candidato deve resolver dois desafios principais:

- Montar a árvore do orçamento a partir da lista plana de `budget_components`.
- Implementar o recálculo de etapas e orçamento quando composições ou insumos forem adicionados, alterados ou removidos.

A hierarquia do orçamento deve ser inferida a partir do `level` de cada componente. Valores como:

```text
1
1.1
1.1.1
2
2.1
```

representam diferentes níveis dentro da estrutura do orçamento.

A base atual não monta `children`, não calcula ancestrais e não propaga totais; esses pontos fazem parte do desafio.

## Requisitos

- PHP 8.3+
- Composer
- SQLite
- Make

## Executando o Projeto

Use o Makefile para preparar e executar o projeto:

```sh
make dev
```

Esse comando executa todo o fluxo necessário para iniciar a aplicação localmente:

- instala as dependências com Composer;
- cria o arquivo `.env` a partir de `.env.example`, se ainda não existir;
- gera a `APP_KEY`, se ainda não existir;
- cria o arquivo `database/database.sqlite`, se ainda não existir;
- recria o banco com migrations e seeders;
- inicia o servidor Laravel.

A API ficará disponível em:

```text
http://127.0.0.1:8000/api
```

Para usar outra porta:

```sh
make dev PORT=8010
```

## Comandos Úteis

```sh
make help
```

Lista os comandos disponíveis.

```sh
make fresh
```

Recria o banco SQLite com migrations e seeders.

```sh
make test
```

Prepara a aplicação e executa a suíte de testes.

```sh
make serve
```

Inicia apenas o servidor Laravel, garantindo antes que dependências, `.env`, `APP_KEY` e SQLite existam.

## Banco de Dados

O projeto usa SQLite por padrão. O comando `make dev` cria automaticamente o arquivo:

```text
database/database.sqlite
```

O seed cria os cadastros-base de `inputs` e `compositions`, além do orçamento `Reforma` com componentes em lista plana e diferentes níveis hierárquicos.

Os totais das folhas possuem valores de exemplo, mas as etapas e o orçamento permanecem com total `0.00` de propósito. O candidato deve implementar a propagação correta.

## Arquitetura

O projeto segue uma organização inspirada no backend principal:

```text
app/
|-- Domains/Core/Domain/
|   |-- Application/
|   |   `-- {Contexto}/{Acao}/{Command,Handler}.php
|   |-- Contracts/
|   |-- Infra/Eloquent/
|   |-- Budget.php
|   |-- BudgetComponent.php
|   |-- StageBudgetComponent.php
|   |-- CompositionBudgetComponent.php
|   |-- InputBudgetComponent.php
|   |-- Composition.php
|   `-- Input.php
|-- Http/Controllers/
|   `-- {Contexto}/{Acao}Controller.php
`-- Http/Resources/
```

Controllers recebem requests, validam payloads simples e montam Commands com os IDs recebidos pela rota.

Handlers executam os fluxos de aplicação e consultam repositories quando precisam carregar entidades.

Repositories isolam o acesso Eloquent atrás de contratos pequenos.

Models ficam dentro do domínio e expõem alguns getters/setters para aproximar o estilo do projeto real.

## Modelo de Dados

### `budgets`

- `id`
- `description`
- `total`

### `inputs`

- `id`
- `description`
- `unit_price`

`Input` é um cadastro-base usado apenas para fornecer o ID que será vinculado a um componente do orçamento.

Esta API não possui CRUD para `inputs`.

### `compositions`

- `id`
- `description`
- `total`

`Composition` é um cadastro-base usado apenas para fornecer o ID que será vinculado a um componente do orçamento.

Esta API não possui CRUD para `compositions`.

### `budget_components`

- `id`
- `description`
- `type`: `stage`, `composition` ou `input`
- `budget_id`
- `composition_id`: preenchido apenas em componentes do tipo `composition`
- `input_id`: preenchido apenas em componentes do tipo `input`
- `total`

`BudgetComponent` representa uma lista plana de componentes do orçamento.

`StageBudgetComponent`, `CompositionBudgetComponent` e `InputBudgetComponent` são classes concretas que herdam de `BudgetComponent` e persistem na mesma tabela.

A transformação dessa lista em uma árvore hierárquica faz parte do desafio técnico.

## Regras de Hierarquia

As regras abaixo descrevem o comportamento esperado após a implementação da árvore:

- Uma Stage pode ser raiz do Budget.
- Uma Stage pode possuir sub-stages, compositions e inputs.
- Composition pertence a uma Stage.
- Input pertence a uma Stage.
- Composition e Input não possuem filhos.
- Composition e Input não devem ser raiz do Budget.

A estrutura esperada pode ser representada da seguinte forma:

```text
Budget: Reforma
|-- Stage 1
|   |-- Stage 1.1
|   |   `-- Input
|   |-- Composition 1.1
|   |-- Composition 1.2
|   |-- Input 1.1
|   `-- Input 1.2
`-- Stage 2
    |-- Stage 2.1
    |   `-- Input
    |-- Composition 2.1
    `-- Input 2.1
```

## Estratégia de Cálculo

A estratégia de cálculo ainda deve ser implementada pelo candidato.

O total de uma Stage deve ser a soma dos totais dos elementos diretamente pertencentes a ela:

- sub-stages;
- compositions;
- inputs.

Ao recalcular uma Stage, o total atualizado deve ser propagado para suas Stages ancestrais.

O total do Budget deve ser a soma das Stages principais identificadas pela estrutura hierárquica.

Nesta base, Composition e Input são cadastros de referência.

O valor usado no orçamento fica em `budget_components.total`.

Esta versão inicial não recalcula automaticamente o total da etapa nem do Budget quando um componente é criado, atualizado ou removido.

Não há implementação de recálculo nesta base; esse comportamento faz parte da análise esperada no desafio.

## Endpoints

### Budgets

```text
GET    /api/budgets
POST   /api/budgets
GET    /api/budgets/{budgetId}
PUT    /api/budgets/{budgetId}
DELETE /api/budgets/{budgetId}
```

Payload de criação/edição:

```json
{
  "description": "Reforma"
}
```

### Budget Components

```text
GET    /api/budget/{budgetId}/component
POST   /api/budget/{budgetId}/component
GET    /api/budget/{budgetId}/component/{componentId}
PUT    /api/budget/{budgetId}/component/{componentId}
DELETE /api/budget/{budgetId}/component/{componentId}
```

Payload para criar uma Stage:

```json
{
  "type": "stage",
  "description": "Stage 1"
}
```

Payload para criar uma Composition:

```json
{
  "type": "composition",
  "description": "Concreto",
  "composition_id": 123,
  "total": 500
}
```

Payload para criar um Input:

```json
{
  "type": "input",
  "description": "Cimento",
  "input_id": 456,
  "total": 100
}
```

`type` diferencia qual componente será criado ou atualizado.

Para `composition` e `input`, envie IDs já existentes nas tabelas de referência criadas pelo seed.
