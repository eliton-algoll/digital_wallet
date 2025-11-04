# API de Carteira Digital

API RESTful para gerenciar uma carteira digital com cadastro, autenticação, consulta de saldo, depósito, saque, transferência e histórico de transações

## Tecnologias
- PHP 8.2+
- Laravel 12
- MySQL
- Autenticação: Laravel Sanctum (Personal Access Tokens)
- Testes: PHPUnit
- Docker

## Requisitos
- PHP 8.2+
- composer
- Docker

## Configuração
1. Clonar e instalar dependências
```bash
git clone git@github.com:eliton-algoll/digital_wallet.git
cd digital_wallet
composer install 
```
2. Ajustar variáveis de ambiente
```bash
cp .env.example .env
```
Se for usar o Docker, você pode manter os valores padrão e apenas ajustar conforme necessário.
3. Gerar key da aplicação
```bash
php artisan key:generate
```
4. Gerando os containers
```bash
docker compose up -d --build
```
5. Rodando migrations
```bash
docker compose exec app php artisan migrate  
```
6. A api estará disponível em: http://localhost:8080/api

## Documentação
Dentro da pasta Docs tem uma collection Postman com exemplo de requisições.
### Endpoints
- Cadastro de usuário
    - POST api/users
    - Payload
  ```json
    {
        "name": "Usuário Teste",
        "email": "teste@teste.com",
        "password": "123456"
    }
    ```
    - Respose
  ```json
    {
        "id": "1e28281d-0a8d-431d-adeb-83ad87687f0c",
        "name": "Usuário Teste",
        "email": "teste@teste.com",
        "balance": 0,
        "daily_withdrawal_limit": 1000,
        "daily_deposit_limit": 10000,
        "created_at": "2025-11-04T19:43:08.000000Z"
    }
  ```
- Login
    - POST /api/login
    - Payload
  ```json
    {
      "email": "teste@teste.com",
      "password": "123456"
    }
  ```
    - Response
  ```json
    {
        "user": {
        "id": "105d521e-2f4f-4b1e-9a53-3846e8856497",
        "name": "Usuário Teste",
        "email": "teste@teste.com"
        },
        "token": "13|i4rgxOAdM147XFpswj4BfQJdb42PMZ5wOUKBX6fY5385fbb2",
        "expires_at": "2025-11-04T21:47:07.000000Z"
    }
  ```
    - O token gerado deverá ser enviado nas próximas requisições em um Header Authorization
    - O token expira em 2H
- Consulta de Saldo
    - GET /api/wallet/balance
    - Não precisa de parametros, o usuário será identificado pelo token enviado
    - Response
  ```json
  {
    "name": "Usuário Teste",
    "email": "teste@teste.com",
    "balance": 11242.7
  }
  ```
- Depósito
    - POST /api/wallet/deposit
    - Payload
  ```json
    {
        "amount": 200.50
    }
  ```
    - Response
  ```json
    {
        "id": "f8d1acf3-a5f6-4852-bb41-d995b0915d14",
        "amount": 200.5,
        "type": "DEPOSIT",
        "wallet": {
            "balance": 13442.5
        }
    }
  ```
- Saque
    - POST /api/wallet/deposit
    - Payload
  ```json
    {
        "amount": 155
    }
  ```
    - Response
  ```json
    {
        "id": "f8d1acf3-a5f6-4852-bb41-d995b0915d14",
        "amount": 155,
        "type": "WITHDRAWAL",
        "wallet": {
            "balance": 13287.5
        }
    }
  ```
- Transferência
    - POST /api/wallet/deposit
    - Payload
  ```json
    {
        "recipient":"elaine@algoll.com.br",
        "amount": 200
    }
  ```
    - Response
  ```json
    {
        "id": "20538f76-2052-4ce1-8ba6-6b61609ce7cf",
        "amount": 200,
        "type": "TRANSFER_OUT",
        "wallet": {
            "balance": 13087.5
        },
        "recipient": {
            "name": "Elaine",
            "email": "elaine@algoll.com.br"
        }
    }
  ```
- Histórico de Transações
    - GET /api/wallet/transactions
    - parâmetros opcionais
        - per_page - Quantidade de registros por página (o padrão é 10 itens por página)
        - sort_by - Campos para ordenar
        - direction - asc ou desc
        - type - Tipo de transação, usar os valores do Enum TransactionType
        - created_at - Data da transação
    - Response
  ```json
    {
        "data": [
            {
                "id": "20538f76-2052-4ce1-8ba6-6b61609ce7cf",
                "amount": 200,
                "type": "TRANSFER_OUT",
                "created_at": "2025-11-04T20:14:17.000000Z",
                "recipient": {
                    "name": "Elaine",
                    "email": "elaine@algoll.com.br"
                }
            },
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 20,
            "total": 1
        }
    }
  ```
- Cadastro de Webhook
    - POST
    - Payload
  ```json
    {
        "url": "https://webhook.com.br/api",
        "headers": "{}", // Opcional
        "secret": "hash_secret"  // Opcional
    }
  ```
    - Response
  ```json
    {
        "id": "51627504-f987-4daf-a167-98ca788dd99c",
        "url": "https://webhook.com.br/api",
        "headers": "{}",
        "secret": "hash_secret",
        "user": {
            "name": "Usuário Teste",
            "email": "teste@teste.com"
        }
    }
  ```
### Testes
Para rodar os testes execute:
```bash
php artisan test
```
