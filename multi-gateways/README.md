# BeTalent Multi-Gateway Payment API - Nível 3

API RESTful de alta performance desenvolvida para gerenciar checkouts complexos com múltiplos provedores de pagamento. Este projeto foi construído focando em resiliência, segurança transacional e excelência técnica através de TDD e análise estática.

---

## Tecnologias e Ferramentas

- **PHP 8.3 + Laravel 11**
- **MySQL 8.0**
- **Docker + Laravel Sail**
- **Pest PHP** (Suíte de testes de alta performance)
- **Larastan/PHPStan** (Análise estática de código - Nível 5)
- **IDE Helper** (Documentação automática de Eloquent Models)

---

## Diferenciais de Engenharia

- **Motor de Checkout com Fallback:** Implementação de um algoritmo de "roleta" que percorre os gateways ativos por ordem de prioridade. Em caso de falha na API externa, o sistema executa automaticamente o fallback para o próximo provedor.
- **Segurança e Integridade:** Uso rigoroso de `DB::transaction` para garantir a atomicidade entre a criação da venda, o registro dos produtos e as tentativas de cobrança.
- **TDD (Test Driven Development):** Cobertura de 100% das rotas críticas, validando fluxos de sucesso, falhas controladas, fallbacks e o processo completo de estorno (chargeback).
- **Cálculo de Carrinho no Back-end:** Prevenção de fraudes garantindo que o valor final seja calculado no servidor, ignorando manipulações de preço via client-side.
- **Análise Estática Rigorosa:** Código validado via PHPStan para eliminar erros de tipagem e propriedades indefinidas, garantindo manutenibilidade.

---

## Instalação e Execução

### 1. Clonar o Repositório
```bash
git clone <url-do-seu-repositorio>
cd multi-gateways
```

### 2. Instalar Dependências via Docker
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. Subir Ambiente e Migrations
```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```

---

## Verificações de Qualidade

### Executar Testes Automatizados
```bash
./vendor/bin/sail artisan test
```

### Executar Análise Estática (PHPStan)
```bash
./vendor/bin/sail php vendor/bin/phpstan analyse
```

---

## Integração com Gateways

O sistema está configurado para interagir com os mocks da BeTalent:

| Provedor   | Endpoint         | Autenticação                     |
|------------|------------------|----------------------------------|
| Gateway 1  | `localhost:3001` | Bearer Token                     |
| Gateway 2  | `localhost:3002` | Custom Headers (Token/Secret)    |

---

> **Desenvolvido por Eduardo Novais como parte do desafio técnico BeTalent.**
