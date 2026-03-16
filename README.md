# BeTalent - API de Pagamentos Multi-Gateway - Eduardo Novais

> [!IMPORTANT]
> **Compatibilidade de Sistema:** Este projeto utiliza Docker e Laravel Sail. Ele pode ser executado em Windows, Linux ou macOS, desde que o Docker esteja instalado. Para usuários **Windows**, é obrigatorio o uso do **WSL2** (Windows Subsystem for Linux) para garantir a compatibilidade dos containers e a performance correta do sistema de arquivos.

Esta é uma API RESTful desenvolvida para o gerenciamento de pagamentos multi-gateway. O sistema implementa um fluxo de checkout resiliente: caso o primeiro gateway da fila de prioridade retorne erro, o sistema realiza automaticamente a tentativa no segundo gateway. Se qualquer um deles retornar sucesso, a transação é confirmada sem exibir erros ao usuário final.

Este projeto atende aos requisitos do Nível 3 (Pleno/Sênior) do teste prático da BeTalent Tech.

---

## Tecnologias e Ferramentas
- Framework: Laravel 12.x
- Banco de Dados: MySQL 8.0
- Ambiente: Docker (Laravel Sail)
- Testes: Pest PHP 4.x (TDD)
- Análise Estática: Larastan / PHPStan 2.x (Level 5)
- Documentação: IDE Helper

---

## Como Instalar e Rodar

### Pré-requisitos
- Docker e Docker Compose instalados.
- WSL2 configurado (caso esteja no Windows).

### 1. Clonar o Repositório
```bash
git clone <url-do-seu-repositorio>
cd multi-gateways
```

### 2. Instalar Dependências (via Docker)
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. Configurar Variáveis de Ambiente
```bash
cp .env.example .env
```

### 4. Subir Containers
Este comando iniciará a aplicação, o banco de dados MySQL e os Mocks dos Gateways:
```bash
./vendor/bin/sail up -d
```

### 5. Preparar o Banco e Autenticação
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

---

## Credenciais de Acesso (Seed)
```text
ADMIN
email: admin@betalent.tech
senha: 12345678
```

---

## Rotas da API e Permissões (Roles)

O sistema utiliza o prefixo /api para todas as rotas. Abaixo, a matriz de permissões conforme exigido no nível 3 do desafio.

### Rotas Públicas
- **POST** `/login` - Realizar login e obter token JWT.
- **POST** `/transactions` - Realizar checkout informando múltiplos produtos e dados do cartão.

### Rotas Privadas (Requer Autenticação Bearer)

| Método | Rota | Descrição | Roles Permitidas |
| :--- | :--- | :--- | :--- |
| **PATCH** | `/gateways/{id}` | Ativar/desativar ou alterar prioridade. | ADMIN |
| **GET** | `/gateways` | Listar todos os gateways. | ADMIN |
| **POST** | `/transactions/{id}/refund` | Realizar reembolso junto ao gateway. | ADMIN, FINANCE |
| **GET** | `/clients` | Listar todos os clientes. | ADMIN, USER |
| **GET** | `/clients/{id}` | Detalhe do cliente e suas compras. | ADMIN, USER |
| **GET** | `/transactions` | Listar todas as compras. | ADMIN, USER |
| **GET** | `/transactions/{id}` | Detalhes de uma compra específica. | ADMIN, USER |
| **CRUD** | `/products` | Gerenciar produtos (Criar/Editar/Excluir). | ADMIN, MANAGER, FINANCE |
| **CRUD** | `/users` | Gerenciar usuários do sistema. | ADMIN, MANAGER |

---

## Detalhamento de Endpoints Principais

### Checkout de Múltiplos Produtos
**POST** `/api/transactions`
O valor total da compra é calculado no servidor buscando os preços atuais na tabela de produtos.

#### Exemplo de Payload
```json
{
  "client_name": "Eduardo Novais",
  "client_email": "eduardo@betalent.tech",
  "products": [
    { "id": 1, "quantity": 2 },
    { "id": 2, "quantity": 1 }
  ],
  "card_number": "5569000000006063",
  "cvv": "010"
}
```

---

## Qualidade e Arquitetura

### Lógica Multi-Gateway
A API foi estruturada para ser modular. A lógica de pagamento percorre os gateways ativos ordenados pela coluna priority. Se o Gateway 1 falhar, o sistema captura a exceção e tenta o Gateway 2 imediatamente.

### Testes Automatizados (TDD)
Implementação completa de testes de integração e unitários utilizando Pest PHP.
```bash
./vendor/bin/sail artisan test
```

### Análise Estática
Uso de PHPStan para garantir a segurança de tipos e evitar erros de propriedade indefinida.
```bash
./vendor/bin/sail php vendor/bin/phpstan analyse
```

### Diferenciais Técnicos
- Uso de DB Transactions para garantir que produtos só sejam vinculados se o pagamento for aprovado.
- Docker Compose configurado para subir a aplicação, banco de dados e os mocks externos simultaneamente.
- Validação rigorosa de roles via Middleware customizado (EnsureUserHasRole).

---
Desenvolvido por Eduardo Novais como parte do desafio técnico BeTalent.
