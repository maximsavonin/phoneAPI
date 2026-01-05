# API для мобильного приложения - Библиотека книг
## Authorization
To authorize, make a **POST** request to `/login` with `username` and `password` in JSON format.  
The response is a JSON object with the keys `success`, `message`, and if registration is successful, it also includes `token` and `expires_at` (the token and the time when it will expire).

**Request:**
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

**Request:**
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
## Авторизация
Для авторизации требуется сделать **POST** запрос `/login` с передачей `username` и `password` в JSON формате.  
Ответ JSON с ключами `success`, `message` и если регистрация успешна то передаётся `token` и `expires_at` (токен и вряме когда токен будет не действителен)

**Запрос:**
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

**Запрос:**
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