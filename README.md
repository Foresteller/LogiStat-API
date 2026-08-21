![CI Pipeline](https://github.com/foresteller/LogiStat-API/actions/workflows/ci.yml/badge.svg)

# Logistat API - B2B Logistics Management Service

High performance RESTful API for B2B logistics and warehouse stock management built with Laravel 11, PostgreSQL, Redis
and Docker.

## Tech Stack

| Компонент              | Технологии                                                                                                |
|:-----------------------|:----------------------------------------------------------------------------------------------------------|
| **Backend:**           | PHP 8.3, Laravel 11(Sanctum, Queues, Form Request)                                                        |
| **Database:**          | Postgres 15(транзакции, кастомные индексы, миграции и сиды)                                               |
| **Cache & Queue**      | Redis(Асинхронный воркер обработки заказов)                                                               |
| **Auth & Security**    | Laravel Sanctum(Выдача и ревокация Bearer-токенов, Role-Based контроль доступа)                           |
| **Containerization**   | Docker, Docker Compose(Multi-stage сборка для prod, OPcache)                                              |
| **Testing:**           | Pest/PHPUnit(Feature тесты с фейковыми очередями и данными в бд)                                          |
| **CI:**                | GitHub Actions(Автоматический прогон тестов и хелсчеков для Postgres и Redis при pr и push, линтинг(pint) |
| **API Spec:**          | OpenAPI/Swagger(документация эндпоинтов)                                                                  |

### Установка

git clone https://github.com/foresteller/LogiStat-API
cd LogiStat-API
cp .env.example .env
docker compose up -d --build
