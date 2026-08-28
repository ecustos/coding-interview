# Mini eCustos

API Laravel simplificada para administrar orcamentos compostos por etapas, composicoes e insumos.

O projeto foi criado como base para entrevista tecnica. Ele prioriza uma implementacao pequena, executavel localmente e facil de explorar por API.

## Requisitos

- PHP 8.3+
- Composer
- SQLite

## Instalacao

```bash
composer install
cp .env.example .env
php artisan key:generate
```

O projeto usa SQLite por padrao. Se o arquivo ainda nao existir:

```bash
touch database/database.sqlite
```

## Banco de Dados

Execute as migrations:

```bash
php artisan migrate
```

Para recriar o banco com dados de exemplo:

```bash
php artisan migrate:fresh --seed
```

O seed cria o orcamento `Reforma` com duas etapas principais, sub-etapas, composicoes e insumos com valores simples para conferencia manual.

## Executando

```bash
php artisan serve
```

A API ficara disponivel em `http://127.0.0.1:8000/api`.

## Testes

```bash
php artisan test
```

## Arquitetura

O projeto segue uma organizacao inspirada no backend principal:

```text
app/
|-- Domains/Core/Domain/
|   |-- Application/
|   |   `-- {Contexto}/{Acao}/{Command,Handler}.php
|   |-- Contracts/
|   |-- Infra/Eloquent/
|   |-- Services/
|   |-- Budget.php
|   |-- BudgetComponent.php
|   |-- Stage.php
|   |-- Composition.php
|   `-- Input.php
|-- Http/Controllers/
|   `-- {Contexto}/{Acao}Controller.php
`-- Http/Resources/
```

Controllers recebem requests, validam payloads simples e montam Commands. Handlers executam os fluxos de aplicacao. Repositories isolam o acesso Eloquent atras de contratos pequenos. Models ficam dentro do dominio e expõem alguns getters/setters para aproximar o estilo do projeto real.

## Modelo de Dados

### budgets

- `id`
- `description`
- `total`

### stages

- `id`
- `description`
- `total`

### compositions

- `id`
- `description`
- `total`

### inputs

- `id`
- `description`
- `total`

### budget_components

- `id`
- `description`
- `type`: `stage`, `composition` ou `input`
- `budget_id`
- `composition_id`
- `input_id`
- `parent_stage_id`
- `total`

`BudgetComponent` representa a arvore do orcamento. Stages formam a hierarquia principal. Compositions e Inputs existem apenas dentro de uma Stage.

## Regras de Hierarquia

- Uma Stage pode ser raiz do Budget.
- Uma Stage pode possuir sub-stages, compositions e inputs.
- Composition pertence a uma Stage.
- Input pertence a uma Stage.
- Composition e Input nao possuem filhos.
- Composition e Input nao devem ser raiz do Budget.

Exemplo:

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

## Estrategia de Calculo

O total de uma Stage e a soma dos totais dos elementos diretamente pertencentes a ela:

- sub-stages;
- compositions;
- inputs.

Ao recalcular uma Stage, o total atualizado deve ser propagado para suas Stages ancestrais. O total do Budget deve ser a soma das Stages principais, ou seja, das Stages sem `parent_stage_id`.

Compositions e Inputs usam seu proprio campo `total`, pois esta versao nao modela quantidade, unidade ou preco unitario.

## Endpoints

### Budgets

```text
GET    /api/budgets
POST   /api/budgets
GET    /api/budgets/{budget}
PUT    /api/budgets/{budget}
DELETE /api/budgets/{budget}
```

Payload de criacao/edicao:

```json
{
  "description": "Reforma"
}
```

### Stages

```text
GET    /api/budgets/{budget}/stages
POST   /api/budgets/{budget}/stages
PUT    /api/stages/{stage}
DELETE /api/stages/{stage}
```

Payload de criacao:

```json
{
  "description": "Stage 1",
  "parent_stage_id": null
}
```

Para criar uma sub-stage, informe `parent_stage_id`.

### Compositions

```text
GET    /api/stages/{stage}/compositions
POST   /api/stages/{stage}/compositions
POST   /api/budgets/{budget}/compositions
PUT    /api/compositions/{composition}
DELETE /api/compositions/{composition}
```

Payload:

```json
{
  "description": "Concreto",
  "total": 500
}
```

Na rota por Budget, informe tambem `parent_stage_id`.

### Inputs

```text
GET    /api/stages/{stage}/inputs
POST   /api/stages/{stage}/inputs
POST   /api/budgets/{budget}/inputs
PUT    /api/inputs/{input}
DELETE /api/inputs/{input}
```

Payload:

```json
{
  "description": "Cimento",
  "total": 100
}
```

Na rota por Budget, informe tambem `parent_stage_id`.
