# Telegram Bot for Chat Moderation, Weather, Wikipedia & Keyword Responses

This repository hosts a Telegram bot built with **Laravel 12.1.1** and **PHP 8.3.11**, leveraging the `telegram/telegram-bot-sdk` package. It is designed to handle message moderation and respond to predefined commands or keywords, all without a database. Lightweight and efficient, it's ideal for group chat automation.

## ✨ Features

- **Censorship Detection**: Blocks messages containing banned words and sends polite warnings.
- **Greeting Recognition**: Replies to simple greetings (e.g., "hello").
- **Currency Info**: Returns current currency rates from the Central Bank of Russia.
- **Weather Reports**:
  - General weather request (`"погода"`)
  - Weather by city (e.g., `"погода в Moscow"`)
- **Wikipedia Search**: Replies with summaries from Wikipedia (e.g., `"что такое Laravel"`).
- **Keyword-Based Deletion**: Deletes messages containing certain critical keywords.
- **Keyword Responses**: Responds to specific keywords with pre-defined replies.

## 🛠 Technologies

- **Framework**: Laravel 12.1.1  
- **Language**: PHP 8.3.11  
- **Telegram SDK**: `telegram/telegram-bot-sdk`  
- **Containerization**: Docker, Docker Compose  
- **Web Server**: Nginx (via Docker)  
- **APIs Used**:
  - Wikipedia
  - OpenWeatherMap
  - Central Bank of Russia (CBR)
- **Health Check**: via [cron-job.org](https://cron-job.org) using `/api/ping`

---

# Телеграм-бот для модерации, погоды, Википедии и ключевых ответов  

Этот репозиторий содержит телеграм-бот, разработанный на Laravel 12.1.1 и PHP 8.3.11, с использованием `telegram/telegram-bot-sdk`. Бот модерирует чат, отвечает на команды и работает без базы данных, что делает его простым и легким для запуска и развертывания.

## ✨ Возможности

-   **Проверка на цензуру**: Блокирует сообщения с запрещёнными словами и отправляет предупреждение.  
-   **Приветствия**: Распознаёт и отвечает на приветствия (например, "привет").
-   **Валюта**: Выводит актуальные курсы валют с сайта ЦБ РФ.
-   **Погода**:
  - Общий запрос погоды (`"погода"`)
  - Погода в указанном городе (например, `"погода в Москва"`)
-   **Википедия**: Даёт краткое описание по запросу (например, `"что такое Laravel"`).
-   **Удаление сообщений**: Удаляет сообщения, содержащие определённые ключевые слова.
-   **Ответы по ключевым словам**: Отвечает заранее заданными фразами на ключевые слова.

## Технологии

-   **Фреймворк**: Laravel 12.1.1
-   **Язык**: PHP 8.3.11
-   **SDK Телеграм**: `telegram/telegram-bot-sdk`
-   **Контейнеризация**: Docker, Docker Compose
-   **Веб-сервер**: Nginx (через Docker)
-   **API`s**:  
  - Wikipedia
  - OpenWeatherMap
  - Центральный банк РФ
-   **Проверка доступности**: через cron-job.org по `/api/ping` 
