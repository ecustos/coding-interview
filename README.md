# Mini eCustos

API Laravel simplificada para administrar orçamentos compostos por etapas, composições e insumos.

O projeto foi criado como base para entrevista técnica. Ele prioriza uma implementação pequena, executável localmente e fácil de explorar por API.

## Desafio Técnico

Este repositório é uma versão mini do módulo de orçamentos do eCustos. A API já possui uma estrutura inicial de `budgets` e `budget_components`, mas ela foi mantida propositalmente simples e incompleta para avaliação técnica.

O candidato deve resolver dois desafios principais:

- Montar a árvore do orçamento a partir da lista plana de `budget_components`.
- Implementar o recálculo de etapas e orçamento quando composições ou insumos forem adicionados, alterados ou removidos.

A implementação inicial ainda não possui uma forma completa de representar hierarquia entre componentes. O candidato deve evoluir o modelo de dados, validações, payloads, resources, seeders e testes conforme necessário para que cada componente possa ser posicionado dentro da árvore.

Uma solução simples e esperada é usar um campo como `level` para indicar a posição hierárquica de cada componente. Valores como:

```text
1
1.1
1.1.1
2
2.1
```

representam diferentes níveis dentro da estrutura do orçamento.

A base atual não monta `children`, não calcula ancestrais e não propaga totais; esses pontos fazem parte do desafio.

## Entrega Esperada

Ao final do desafio, espera-se que:

- a API continue executando localmente pelos comandos descritos neste README;
- os endpoints de consulta de orçamento/componentes consigam retornar a estrutura hierárquica com `children`;
- a criação, edição e remoção de componentes mantenham a árvore e os totais consistentes;
- o seed ou os testes possuam dados suficientes para validar cenários com stages, sub-stages, compositions e inputs;
- a suíte de testes cubra a nova feature e continue validando os fluxos de CRUD já existentes;
- as decisões relevantes de implementação sejam simples de entender pelo código ou por pequenos comentários quando necessário.

## Fora de Escopo

Não é necessário implementar autenticação, autorização, frontend, filas, cache, integrações externas ou CRUD para `inputs` e `compositions`. O foco do teste é a evolução do domínio de orçamento, incluindo hierarquia, recálculo, persistência dos dados necessários e testes.

## Requisitos

- PHP 8.3+
- Extensão PHP `pdo_sqlite`
- Composer
- SQLite
- Node.js 20+ e npm
- Make, para usar os comandos `make` em Linux/macOS

## Executando o Projeto

Para instalar as dependências do sistema e do projeto, use o script adequado para o seu ambiente.

Linux/macOS:

```sh
./scripts/install-dependencies.sh
```

Windows PowerShell:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\install-dependencies.ps1
```

Os scripts verificam ou instalam PHP 8.3+, Composer, SQLite, Node.js/npm e as dependências do projeto (`composer install` e `npm install`). Eles também criam `.env`, `database/database.sqlite` e `APP_KEY` quando necessário.

Caso prefira instalar manualmente as ferramentas do sistema, execute os scripts com `SKIP_SYSTEM_INSTALL=1` no Linux/macOS ou `-SkipSystemInstall` no Windows.

Depois disso, use o Makefile para preparar e executar o projeto:

Linux/macOS:

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

O seed cria os cadastros-base de `inputs` e `compositions`, além do orçamento `Reforma` com componentes em lista plana. A estrutura inicial é propositalmente incompleta: ela possui stages, compositions e inputs, mas ainda não possui a árvore pronta nem todos os dados necessários para inferi-la automaticamente.

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

A transformação dessa lista em uma árvore hierárquica faz parte do desafio técnico. Caso a solução adicione campos como `level`, `parent_id` ou outro mecanismo equivalente, atualize também migration, model, factories, seeders, validações, resources e testes para refletir a nova estrutura.

## Regras de Hierarquia

A API entrega os componentes do orçamento como uma lista plana de registros em `budget_components`. Parte do desafio é refatorar essa representação para que o orçamento possa ser interpretado e retornado como uma estrutura de árvore, com componentes filhos dentro de seus respectivos pais.

Essa árvore deve ser construída a partir da hierarquia dos componentes do orçamento e usada como base para navegação, exibição e recálculo. A implementação deve respeitar o desenho atual da aplicação: controllers devem continuar simples, Handlers devem coordenar o caso de uso, repositories devem concentrar acesso a dados e qualquer regra de montagem da árvore deve ficar em uma camada coerente com o domínio.

Antes de montar a árvore, a solução precisa definir como a posição de cada componente será persistida.

As regras abaixo descrevem o comportamento esperado após a implementação:

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

A estratégia de cálculo ainda deve ser implementada pelo candidato. A tarefa é criar um mecanismo de recálculo recursivo para manter os totais das etapas e do orçamento sempre coerentes com os componentes cadastrados.

A base atual persiste os componentes em uma lista plana na tabela `budget_components` e diferencia cada registro pelo campo `type`, usando as classes concretas `StageBudgetComponent`, `CompositionBudgetComponent` e `InputBudgetComponent`. O mecanismo implementado deve partir dessa estrutura, montar ou percorrer a hierarquia conforme as regras descritas neste README e recalcular os totais sem descaracterizar a arquitetura existente do projeto.

O comportamento esperado é:

- o total de uma Stage deve ser a soma dos totais dos elementos diretamente pertencentes a ela: sub-stages, compositions e inputs;
- ao recalcular uma Stage, o novo total deve ser propagado recursivamente para suas Stages ancestrais;
- o total do Budget deve ser a soma das Stages principais identificadas na raiz da hierarquia;
- quando um componente for criado, atualizado ou removido, o recálculo deve ser executado para o trecho afetado da árvore e refletir no Budget;
- Composition e Input continuam sendo cadastros de referência; o valor considerado no orçamento deve ser o valor persistido em `budget_components.total`, não o total das tabelas `compositions` ou `inputs`.

A implementação deve seguir a organização do projeto o mais fielmente possível. Controllers devem permanecer responsáveis por request/validação e montagem de Commands; Handlers devem orquestrar o caso de uso; repositories devem continuar isolando o acesso Eloquent; e novas regras de domínio ou serviços devem ser posicionados em locais compatíveis com a estrutura de `app/Domains/Core/Domain`.

Também é esperado que a solução aplique conceitos SOLID: responsabilidades bem separadas, baixo acoplamento entre cálculo e infraestrutura, dependências preferencialmente orientadas por contratos quando fizer sentido e código aberto a extensão sem espalhar regras de cálculo pelos controllers.

A feature deve ser coberta por testes. Inclua cenários que validem o recálculo em criação, edição e remoção de componentes, propagação para etapas ancestrais e atualização do total do Budget. Ajuste ou reexecute os testes existentes sempre que necessário para garantir que o CRUD atual continue funcionando junto com o novo comportamento.

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
