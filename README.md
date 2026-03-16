# BeTalent - API de Pagamentos Multi-Gateway - Eduardo Novais

Esta é uma API RESTful robusta para gerenciamento de pagamentos multi-gateway. O sistema implementa um fluxo de checkout resiliente: caso o primeiro gateway falhe ou esteja indisponível, o sistema realiza o fallback automático para o próximo provedor ativo seguindo a ordem de prioridade, garantindo a conclusão da venda.

Este projeto foi desenvolvido como parte do teste prático para a **BeTalent Tech**.

## 🛠 Tecnologias e Ferramentas
- **Framework:** Laravel 11
- **Banco de Dados:** MySQL 8.0
- **Ambiente:** Docker (Laravel Sail)
- **Testes:** Pest PHP (TDD)
- **Análise Estática:** Larastan / PHPStan (Level 5)
- **Documentação:** IDE Helper

## 🚀 Como Instalar e Rodar

### Pré-requisitos
- Docker e Docker Compose instalados.

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
Este comando iniciará os containers da API, MySQL e os Mocks de Gateway:
```bash
./vendor/bin/sail up -d
```

### 5. Preparar o Banco e Autenticação
Execute as migrations e os seeders para popular os usuários administrativos e os gateways iniciais:
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

## 🔑 Credenciais de Acesso (Seed)
```text
ADMIN
email: admin@betalent.tech
senha: 12345678
```

## 🚦 Rotas da API
Todas as rotas possuem o prefixo `/api`. 
*Obs: Recomenda-se o uso do header `Accept: application/json` em todas as requisições.*

### 🔓 Públicas
* **POST** `/login` - Realiza a autenticação e retorna o Token JWT.
* **POST** `/transactions` - Realiza uma compra (Checkout) informando produtos e dados do cartão.

### 🔒 Privadas (Requer `Authorization: Bearer {token}`)
| Método | Rota | Descrição | Roles Permitidas |
| :--- | :--- | :--- | :--- |
| **GET** | `/gateways` | Lista todos os gateways configurados. | `ADMIN` |
| **PATCH** | `/gateways/{id}` | Altera prioridade ou ativa/desativa um gateway. | `ADMIN` |
| **POST** | `/transactions/{id}/refund` | Realiza o estorno de uma transação no gateway. | `ADMIN`, `FINANCE` |
| **GET** | `/clients` | Lista todos os clientes cadastrados. | `ADMIN`, `USER` |
| **GET** | `/clients/{id}` | Detalhes de um cliente e seu histórico de compras. | `ADMIN`, `USER` |
| **GET** | `/transactions` | Lista todas as transações realizadas. | `ADMIN`, `USER` |
| **GET** | `/transactions/{id}` | Detalhes específicos de uma transação e itens. | `ADMIN`, `USER` |
| **CRUD** | `/products` | Gerenciamento de estoque e preços. | `ADMIN`, `MANAGER` |
| **CRUD** | `/users` | Gerenciamento de usuários do sistema. | `ADMIN` |

---

## 🛣️ Detalhamento de Endpoints Principais

### Realizar uma Compra (Checkout)
**POST** `/api/transactions`
Se o cliente não existir (baseado no e-mail), ele é criado automaticamente durante a venda.

#### Request Body
```json
{
  "client_name": "Eduardo Novais",
  "client_email": "eduardo@betalent.tech",
  "products": [
    { "id": 1, "quantity": 2 },
    { "id": 3, "quantity": 1 }
  ],
  "card_number": "1234123412341234",
  "cvv": "123"
}
```

#### Response (Success)
```json
{
  "id": "uuid-string",
  "client_id": 1,
  "amount": 55000,
  "status": "paid",
  "products": [...]
}
```

---

## 🧪 Qualidade e Testes

### Testes Automatizados (TDD)
O projeto possui cobertura total de testes das rotas críticas utilizando **Pest PHP**:
```bash
./vendor/bin/sail artisan test
```

### Análise Estática
Para garantir a tipagem e evitar bugs de execução, o código foi validado com **PHPStan** (Level 5):
```bash
./vendor/bin/sail php vendor/bin/phpstan analyse
```

## 📈 Diferenciais Implementados
- **Cálculo de Carrinho no Server-side:** Preços unitários são buscados diretamente no banco de dados para evitar manipulação de valores pelo front-end.
- **Resiliência:** Sistema de fallback automático entre gateways em caso de erros `500` ou instabilidade nas APIs externas.
- **Histórico de Preços:** Implementado o salvamento do `unit_price` na tabela pivô para garantir a integridade de consultas históricas e estornos.
- **Middleware de Permissões:** Controle granular de acesso baseado em Roles (ADMIN, FINANCE, MANAGER, USER).

---
**Desenvolvido por Eduardo Novais como parte do desafio técnico BeTalent.**
