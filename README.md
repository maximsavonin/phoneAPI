# API для мобильного приложения - Библиотека книг
## Authorization
To authorize, make a **POST** request to `/login` with `username` and `password` in JSON format.  
The response is a JSON object with the keys `success`, `message`, and if registration is successful, it also includes `token` and `expires_at` (the token and the time when it will expire).

**Request:** /login
```json
{
  "username": "login",
  "password": "password"
}
```
**Response:**
```json
{
  "success": true,
  "message": "User logined",
  "token": "a1b2c3d4...",
  "expires_at": "2026-01-04 09:40:52"
}
```
| code | message                        | Explanation                       |
|------|--------------------------------|-----------------------------------|
| 405  | Method not allowed             | Request method is not POST        |
| 400  | Username and password required | Username or password not provided |
| 200  | User logged in                 | Successful login                  |
| 400  | Wrong username or password     | Username or password is incorrect |
## Registration
To register, make a **POST** request to `/register` with `username`, `password`, and `password_confirm`.  
The response follows the same format as the `/login` request.

**Request:** /register
```json
{
  "username": "login",
  "password": "password",
  "password_confirm": "password"
}
```
**Response:**
```json
{
  "success": true,
  "message": "User registered",
  "token": "a1b2c3d4...",
  "expires_at": "2026-01-04 09:40:52"
}
```
| code | message                        | Explanation                              |
|------|--------------------------------|------------------------------------------|
| 405  | Method not allowed             | Request method is not POST               |
| 400  | Username and password required | Username or password not provided        |
| 400  | Passwords do not match         | Password does not match the confirmation |
| 200  | Username already exists        | A user with this username already exists |
| 409  | User registered                | Successful registration                  |

## User list — grant access
To retrieve the list of users, you need to make a **GET** request to `/listUsers`  
The response is JSON with the keys `success` and `list`, where `list` contains `id` and `username`.

To grant a user access, you need to make a **POST** request to `/listUsers` with `owner_id` and `user_id` passed  
The response is JSON with the keys `success` and `message`.

**Response:** /listUsers
```json
{
  "success": true,
  "message": "",
  "list": [
    {
      "id": 1,
      "username": 'maxim'
    },
    {
      "id": 2,
      "username": 'login'
    }
  ]
}
```
**Request:** /listUsers
```json
{
  "owner_id": "1",
  "user_id": "2"
}
```
**Response:**
```json
{
  "success": true,
  "message": "access has been granted"
}
```
| code | message                 | Explanation                            |
|------|-------------------------|----------------------------------------|
| 405  | Method not allowed      | An unsupported request method was used |
| 404  | User does not exist     | The user does not exist                |
| 409  | access already exists   | Access has already been granted        |
| 200  |                         | User list successfully returned        |
| 200  | access has been granted | Access has been granted                |

## Авторизация
Для авторизации требуется сделать **POST** запрос `/login` с передачей `username` и `password` в JSON формате.  
Ответ JSON с ключами `success`, `message` и если регистрация успешна то передаётся `token` и `expires_at` (токен и вряме когда токен будет не действителен)

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
  "message": "User logined",
  "token": "a1b2c3d4...",
  "expires_at": "2026-01-04 09:40:52"
}
```
### Варианты сообщений:
| code | message                        | Пояснение                    |
|------|--------------------------------|------------------------------|
| 405  | Method not allowed             | Выполнен не POST запрос      |
| 400  | Username and password required | Не передан логин или пароль  |
| 200  | User logined                   | Успешная авторизация         |
| 400  | wrong username or password     | Логи или пароль не правльный |
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
| code | message                        | Пояснение                                 |
|------|--------------------------------|-------------------------------------------|
| 405  | Method not allowed             | Выполнен не POST запрос                   |
| 400  | Username and password required | Не передан логин или пароль               |
| 400  | Passwords do not match         | Пароль не совпадает с подтвеждение пароля |
| 200  | Username already exists        | Пользователь с данным логином существует  |
| 409  | User registered                | Успешная регистрация                      |

## Список пользователей, дать доступ
Для получения списка пользователей требуется сделать **GET** запрос `/listUsers`  
Ответ JSON с ключами `success` и `list` в котором id и username.

Для выдачи пользователю доступа требуется сделать **POST** запрос `/listUsers` с передаче `owner_id` и `user_id`  
Ответ JSON с ключами `success` и `message`.

**Ответ:** /listUsers
```json
{
  "success": true,
  "message": "",
  "list": [
    {
      "id": 1,
      "username": 'maxim'
    },
    {
      "id": 2,
      "username": 'login'
    }
  ]
}
```
**Запрос:** /listUsers
```json
{
  "owner_id": "1",
  "user_id": "2"
}
```
**Ответ:**
```json
{
  "success": true,
  "message": "access has been granted"
}
```
| code | message                 | Пояснение                         |
|------|-------------------------|-----------------------------------|
| 405  | Method not allowed      | Выполнен не обробатываемый запрос |
| 404  | User does not exist     | Пользователь не существует        |
| 409  | access already exists   | доступ уже выдан                  |
| 200  |                         | Передача списка пользователей     |
| 200  | access has been granted | Доступ выдан                      |