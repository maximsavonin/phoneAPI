# API для мобильного приложения - Библиотека книг

Далее в примерах данные указаны в JSON формате для удобства написания автором и чтения пользователей. Само приложение принимает данные в формате application/x-www-form-urlencoded, а отвечает в JSON.

## Авторизация
Для авторизации требуется сделать **POST** запрос `/login` с передачей `username` и `password`.  
Ответ JSON с ключами `success`, `message` и если регистрация успешна то передаётся `token` и `expires_at` (токен и время когда токен будет не действителен)

**Запрос:** /login
```json
{
  "username": "login",
  "password": "password"
}
```
**Ответ:**
```json
{
  "success": true,
  "message": "User logged in",
  "token": "a1b2c3d4...",
  "expires_at": "2026-01-04 09:40:52"
}
```
### Варианты сообщений:
| code | message                        | Пояснение                    |
|------|--------------------------------|------------------------------|
| 405  | Method not allowed             | Выполнен не POST запрос      |
| 400  | Username and password required | Не передан логин или пароль  |
| 200  | User logged in                 | Успешная авторизация         |
| 400  | wrong username or password     | Логи или пароль не правльный |  
Во всех последующих запросах кроме `/login` и `/register` требуется передавать токен в заголовке Authorization. Если токен не передать или токен просрочен будет передан ответ с кодом ошибки 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```
## Регистрация
Для регистрации требуется сделать **POST** запрос `/register` с передачей `username`, `password` и `password_confirm`  
Ответ соответствует запросу `/login`

**Запрос:** /register
```json
{
  "username": "login",
  "password": "password",
  "password_confirm": "password"
}
```
**Ответ:**
```json
{
  "success": true,
  "message": "User registered",
  "token": "a1b2c3d4...",
  "expires_at": "2026-01-04 09:40:52"
}
```
| code | message                        | Пояснение                                  |
|------|--------------------------------|--------------------------------------------|
| 405  | Method not allowed             | Выполнен не POST запрос                    |
| 400  | Username and password required | Не передан логин или пароль                |
| 400  | Passwords do not match         | Пароль не совпадает с подтверждение пароля |
| 409  | Username already exists        | Пользователь с данным логином существует   |
| 200  | User registered                | Успешная регистрация                       |

## Список пользователей
Для получения списка пользователей требуется сделать **GET** запрос `/users/list`  
Ответ JSON с ключами `success`, `message` и `users`, где `users` содержит id и username.

**Ответ:** /users/list
```json
{
  "success": true,
  "message": "",
  "users": [
    {
      "id": 1,
      "username": "maxim"
    },
    {
      "id": 2,
      "username": "login"
    }
  ]
}
```

| code | message                 | Пояснение                     |
|------|-------------------------|-------------------------------|
| 405  | Method not allowed      | Выполнен не GET запрос        |
| 200  |                         | Передача списка пользователей |

## Выдать доступ
Для выдачи пользователю доступа требуется сделать **POST** запрос `/users/access` с передаче `user_id`.  
Ответ JSON с ключами `success` и `message`.

**Запрос:** /users/access
```json
{
  "user_id": "1"
}
```
**Ответ:**
```json
{
  "success": true,
  "message": "access has been granted"
}
```
| code | message                 | Пояснение                  |
|------|-------------------------|----------------------------|
| 405  | Method not allowed      | Выполнен не POST запрос    |
| 404  | User does not exist     | Пользователь не существует |
| 409  | access already exists   | доступ уже выдан           |
| 200  | access has been granted | Доступ выдан               |

## Получение списка книг
Для получения списка книг пользователя требуется сделать **GET** запрос `/getBooks/user`.  
Ответ JSON с ключами `success`, `message` и `books` с полями `id` и `title`.

**Ответ:** /getBooks/user
```json
{
  "success": true,
  "message": "",
  "books": [
    {
      "id": 1,
      "title": "Война и мир"
    },
    {
      "id": 15,
      "title": "Преступление и наказание"
    }
  ]
}
```
| code | message            | Пояснение              |
|------|--------------------|------------------------|
| 405  | Method not allowed | Выполнен не GET запрос |
| 200  |                    | Передан список книг    |

## Получение списка книг другого пользователя
Для получения списка книг пользователя требуется сделать **GET** запрос `/getBooks/otherUser` c передачей `owner_id`.  
Ответ JSON с ключами `success`, `message` и `books` с полями `id` и `title`.

**Запрос:** /users/access
```json
{
  "owner_id": "1"
}
```
**Ответ:**
```json
{
  "success": true,
  "message": "",
  "books": [
    {
      "id": 1,
      "title": "Война и мир"
    },
    {
      "id": 15,
      "title": "Преступление и наказание"
    }
  ]
}
```
| code | message            | Пояснение              |
|------|--------------------|------------------------|
| 405  | Method not allowed | Выполнен не GET запрос |
| 403  | access denied      | Доступ не выдан        |
| 200  |                    | Список книг передан    |

## Действия с книгой
Для разных действий с книгой существует эндпоинт `/book/{action}`

| Метод | action | действие                          | параметры                          | ответ                               |
|-------|--------|-----------------------------------|------------------------------------|-------------------------------------|
| POST  | create | создание новой книги              | book_title, text/file(.txt, <30Mb) | success, message                    |
| GET   | get    | получение существующей книги      | book_id                            | success, message, book(title, text) |
| POST  | update | редактирование существующей книги | book_id, book_title, text          | success, message                    |
| POST  | delete | удаление книги                    | book_id                            | success, message                    |

**Запрос:** /book/create
```json
{
  "book_title": "Война и мир",
  "text": "abcdef..."
}
```
**Ответ:**
```json
{
  "success": true,
  "message": "book created"
}
```
| code | message                       | Пояснение                          |
|------|-------------------------------|------------------------------------|
| 405  | Method not allowed            | Выполнен не POST запрос            |
| 400  | Book title is required        | Не передано название книги         |
| 400  | text or file not transferred  | Не передан текст книги или файл    |
| 400  | error uploading file          | Ошибка загрузки файла              |
| 413  | file is too large (max 30 MB) | Файл слишком большой               |
| 415  | file extension not allowed    | Файл должен быть .txt              |
| 415  | file must be in UTF-8         | Файл должен быть в кодировке UTF-8 |
| 500  | error reading file            | Ошибка чтения файла                |
| 200  | book created                  | Книга создана и сохранена          |

**Запрос:** /book/get
```json
{
  "book_id": 3
}
```
**Ответ:**
```json
{
  "success": true,
  "message": "book created",
  "book": {
    "title": "Война и мир",
    "text": "abcdef..."
  }
}
```
| code | message             | Пояснение              |
|------|---------------------|------------------------|
| 405  | Method not allowed  | Выполнен не GET запрос |
| 400  | book id is required | Не передано id книги   |
| 404  | book not found      | Книга не найдена       |
| 200  |                     | Книга передана         |

**Запрос:** /book/update
```json
{
  "book_id": 3,
  "book_title": "Война и мир",
  "text": "abcdef..."
}
```
**Ответ:**
```json
{
  "success": true,
  "message": "book updated"
}
```
| code | message                | Пояснение                  |
|------|------------------------|----------------------------|
| 405  | Method not allowed     | Выполнен не POST запрос    |
| 400  | book id is required    | Не передано id книги       |
| 400  | Book title is required | Не передано название книги |
| 400  | text not transferred   | Не передан текст книги     |
| 404  | book not found         | Книга не найдена           |
| 200  | book updated           | Книга обновлена            |

**Запрос:** /book/delete
```json
{
  "book_id": 3
}
```
**Ответ:**
```json
{
  "success": true,
  "message": "book deleted"
}
```
| code | message                      | Пояснение                       |
|------|------------------------------|---------------------------------|
| 405  | Method not allowed           | Выполнен не POST запрос         |
| 400  | book id is required          | Не передано id книги            |
| 404  | book not found               | Книга не найдена                |
| 200  | book deleted                 | Книга удалена                   |
