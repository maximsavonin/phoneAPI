# Mobile App API — Book Library

In the examples below, data is shown in JSON format for the convenience of the author and for easier reading by users. The application itself accepts data in application/x-www-form-urlencoded format and responds in JSON.

## Authorization
To authorize, make a **POST** request to `/login` with `username` and `password`.  
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
  "message": "User logged in",
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
For all subsequent requests except `/login` and `/register`, you must pass the token in the Authorization header. If the token is not provided or the token has expired, a 401 error response will be returned.
```json
{
  "success": false,
  "message": "Unauthorized"
}
```
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
| 405  | Method not allowed             | Request method is not GET                |
| 400  | Username and password required | Username or password not provided        |
| 400  | Passwords do not match         | Password does not match the confirmation |
| 409  | Username already exists        | A user with this username already exists |
| 200  | User registered                | Successful registration                  |

## User list
To retrieve the list of users, you need to make a **GET** request to `/users/list`  
The response is JSON with the keys `success`, `message` and `users`, where `users` contains `id` and `username`.

**Response:** /users/list
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

| code | message                 | Explanation                     |
|------|-------------------------|---------------------------------|
| 405  | Method not allowed      | Request method is not POST      |
| 200  |                         | User list successfully returned |

## Give access
To grant a user access, you need to make a **POST** request to `/users/access` with `user_id`.  
The response is JSON with the keys `success` and `message`.

**Request:** /users/access
```json
{
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
| code | message                 | Explanation                     |
|------|-------------------------|---------------------------------|
| 405  | Method not allowed      | Request method is not POST      |
| 404  | User does not exist     | The user does not exist         |
| 409  | access already exists   | Access has already been granted |
| 200  | access has been granted | Access has been granted         |

## Get user's books list
To get a list of a user’s books, make a **GET** request to `/getBooks/user`.  
The response is JSON with keys `success`, `message` and `books` containing `id` and `title`.

**Response:** /getBooks/user
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
| code | message            | Description               |
|------|--------------------|---------------------------|
| 405  | Method not allowed | Request method is not GET |
| 200  |                    | List of books sent        |

## Get another user's book list
To get another user’s books, make a **GET** request to `/getBooks/otherUser` with `owner_id`.  
The response is JSON with keys `success`, `message` and `books` containing `id` and `title`.

**Request:** /users/access
```json
{
  "owner_id": "1"
}
```
**Response:**
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
| code | message            | Description               |
|------|--------------------|---------------------------|
| 405  | Method not allowed | Request method is not GET |
| 403  | access denied      | Access denied             |
| 200  |                    | Book list returned        |

## Book actions
For different book actions, use the endpoint `/book/{action}`

| Method | action | Description           | Parameters                         | Response                            |
|--------|--------|-----------------------|------------------------------------|-------------------------------------|
| POST   | create | Create a new book     | book_title, text/file(.txt, <30Mb) | success, message                    |
| GET    | get    | Get an existing book  | book_id                            | success, message, book(title, text) |
| POST   | update | Edit an existing book | book_id, book_title, text          | success, message                    |
| POST   | delete | Delete a book         | book_id                            | success, message                    |

**Request:** /book/create
```json
{
  "book_title": "Война и мир",
  "text": "abcdef..."
}
```
**Response:**
```json
{
  "success": true,
  "message": "book created"
}
```
| code | message                       | Description                    |
|------|-------------------------------|--------------------------------|
| 405  | Method not allowed            | Request method is not POST     |
| 400  | Book title is required        | Book title not provided        |
| 400  | text or file not transferred  | Book text or file not provided |
| 400  | error uploading file          | Error uploading file           |
| 413  | file is too large (max 30 MB) | File exceeds 30 MB             |
| 415  | file extension not allowed    | File must be .txt              |
| 415  | file must be in UTF-8         | File must be in UTF-8 encoding |
| 500  | error reading file            | Error reading file             |
| 200  | book created                  | Book created                   |

**Request:** /book/get
```json
{
  "book_id": 3
}
```
**Response:**
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
| code | message             | Description               |
|------|---------------------|---------------------------|
| 405  | Method not allowed  | Request method is not GET |
| 400  | book id is required | Book id not provided      |
| 404  | book not found      | Book not found            |
| 200  |                     | Book returned             |

**Request:** /book/update
```json
{
  "book_id": 3,
  "book_title": "Война и мир",
  "text": "abcdef..."
}
```
**Response:**
```json
{
  "success": true,
  "message": "book updated"
}
```
| code | message                | Description                |
|------|------------------------|----------------------------|
| 405  | Method not allowed     | Request method is not POST |
| 400  | book id is required    | Book id not provided       |
| 400  | Book title is required | Book title not provided    |
| 400  | text not transferred   | Book text not provided     |
| 404  | book not found         | Book not found             |
| 200  | book updated           | Book updated               |

**Request:** /book/delete
```json
{
  "book_id": 3
}
```
**Response:**
```json
{
  "success": true,
  "message": "book deleted"
}
```
| code | message                      | Description                |
|------|------------------------------|----------------------------|
| 405  | Method not allowed           | Request method is not POST |
| 400  | book id is required          | Book id not provided       |
| 404  | book not found               | Book not found             |
| 200  | book deleted                 | Book deleted               |