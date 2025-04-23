# Telegram Bot for Chat Moderation and Keyword Responses

This repository hosts a Telegram bot developed using **Laravel 12.1.1** and **PHP 8.3.11**, powered by the `telegram/telegram-bot-sdk` package. The bot is designed to moderate chat messages by detecting spam (e.g., banned words) and providing automated responses to specific keywords. It operates as a backend-only application with no database or frontend, making it lightweight and efficient.

## Features

-   **Spam Detection**: Identifies and blocks messages with banned words, responding with a polite warning.
-   **Keyword Responses**: Automatically replies to predefined keywords (e.g., "hello" → "Hi! How can I help you?").
-   **No Database**: Runs without a database, using file-based or in-memory storage.
-   **Docker Support**: Includes Docker configuration for easy local testing and deployment.

## Technologies

-   **Framework**: Laravel 12.1.1
-   **Language**: PHP 8.3.11
-   **Telegram SDK**: telegram/telegram-bot-sdk
-   **Containerization**: Docker, Docker Compose
-   **Web Server**: Nginx (via Docker)
-   **API`s**: Wikipedia, Openweathermap, Central Bank of Russia

---

# Телеграм-бот для модерации чата и ответов на ключевые слова

Этот репозиторий содержит телеграм-бот, разработанный с использованием **Laravel 12.1.1** и **PHP 8.3.11**, с использованием пакета `telegram/telegram-bot-sdk`. Бот предназначен для модерации сообщений в чате, выявления спама (например, запрещенных слов) и автоматических ответов на определенные ключевые слова. Приложение работает только как бэкенд, без базы данных и фронтенда, что делает его легким и эффективным.

## Возможности

-   **Обнаружение спама**: Определяет и блокирует сообщения с запрещенными словами, отвечая вежливым предупреждением.
-   **Ответы на ключевые слова**: Автоматически отвечает на заданные ключевые слова (например, "привет" → "Привет! Чем могу помочь?").
-   **Без базы данных**: Работает без базы данных, используя файловое или оперативное хранилище.
-   **Поддержка Docker**: Включает конфигурацию Docker для простого локального тестирования и развертывания.

## Технологии

-   **Фреймворк**: Laravel 12.1.1
-   **Язык**: PHP 8.3.11
-   **SDK Телеграм**: telegram/telegram-bot-sdk
-   **Контейнеризация**: Docker, Docker Compose
-   **Веб-сервер**: Nginx (через Docker)
-   **API`s**: Wikipedia, Openweathermap, Центральный Банк России (ЦБ РФ)
