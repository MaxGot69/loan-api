# Loan Scoring API

## Описание

API для кредитного скоринга. Принимает заявку, рассчитывает статус (одобрено/отказано) на основе дохода и суммы кредита. Результаты скоринга кешируются в Redis.

Стек: Symfony 8.4, PostgreSQL 15, Redis 7.4, Docker Compose.

---

## Запуск

### Требования
- Docker
- Docker Compose

### Установка

```bash
git clone https://github.com/ваш-аккаунт/loan-api.git
cd loan-api
docker-compose up -d
docker-compose exec app php bin/console doctrine:migrations:migrate
docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

API доступно по адресу: `http://localhost:8080`

---

## Эндпоинты

| Метод | URL | Описание |
|-------|-----|----------|
| POST | `/loan` | Создание заявки |
| GET | `/loan/{id}` | Получение заявки по ID |
| GET | `/loans` | Список заявок (пагинация) |

---

### POST /loan

Тело запроса (JSON):

```json
{
    "income": 5000,
    "amount": 2000,
    "term": 12
}
```

Ответ (201 Created):

```json
{
    "id": 1,
    "income": 5000,
    "amount": 2000,
    "term": 12,
    "status": "approved"
}
```

---

### GET /loan/{id}

Ответ (200 OK):

```json
{
    "id": 1,
    "income": 5000,
    "amount": 2000,
    "term": 12,
    "status": "approved"
}
```

При отсутствии заявки — 404 Not Found.

---

### GET /loans

Параметры (query string):
- `page` — номер страницы (по умолчанию 1)
- `limit` — записей на странице (по умолчанию 10)

Пример: `GET /loans?page=2&limit=5`

Ответ (200 OK):

```json
[
    {
        "id": 1,
        "income": 5000,
        "amount": 2000,
        "term": 12,
        "status": "approved"
    },
    {
        "id": 2,
        "income": 1000,
        "amount": 2000,
        "term": 12,
        "status": "rejected"
    }
]
```

---

## Логика скоринга

Заявка одобряется, если выполняется условие:

```
income > amount * 2
```

В противном случае заявка отклоняется.

---

## Тестовые данные

После выполнения фикстур в базе создаются 10 тестовых заявок с различными параметрами.

---
