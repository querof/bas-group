# BAS-Group

## Overview

This project is an implementation of **Secret message**. It includes endpoints to push messages, retrieve a specific message, and retrieve all messages for a recipient. The project also includes Docker support for containerized deployment.

## Requirements

- PHP
- Composer
- Docker
- Docker Compose

## Installation

1. **Clone the repository:**

   ```sh
   git clone https://github.com/your-repo/bas-group.git
   cd bas-group
   ```

2. **Start the Docker containers:**

   ```sh
   make start_containers
   ```

3. **Install dependencies and run migrations:**

   ```sh
   make install
   ```

## Makefile Commands

- **Start containers:**

  ```sh
  make start_containers
  ```

- **Stop containers:**

  ```sh
  make stop_containers
  ```

- **Restart containers:**

  ```sh
  make restart_containers
  ```

- **Install dependencies and run migrations:**

  ```sh
  make install
  ```

## Endpoints

### Push a Message

- **URL:** `/message`
- **Method:** `POST`
- **Description:** Push a new message to a recipient.
- **Request Body:**
  ```json
  {
    "identifier": "recipient-identifier",
    "message": "Your message here"
  }
  ```
- **Response:**
    - `201 Created` on success
    - `404 Not Found` if the recipient is not found
    - `400 Bad Request` if the message is empty
    - `500 Internal Server Error` on failure

### Get a Specific Message

- **URL:** `/recipient/{recipient}/message/{message}`
- **Method:** `GET`
- **Description:** Retrieve a specific message for a recipient.
- **Response:**
    - `200 OK` with the decrypted message
    - `404 Not Found` if the recipient or message is not found

### Get All Messages

- **URL:** `/recipient/{recipient}/messages`
- **Method:** `GET`
- **Description:** Retrieve all messages for a recipient.
- **Response:**
    - `200 OK` with an array of decrypted messages
    - `404 Not Found` if the recipient is not found

## Running Tests

To run the tests, use the following command:

```sh
make tests
```

## License

This project is licensed under the MIT License.
```