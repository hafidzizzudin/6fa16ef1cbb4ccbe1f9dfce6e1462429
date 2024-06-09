## Email Sender Project
This service main functionality is to send email asynchronously using worker. The detail information about this service is listed below:
- Language: `PHP`
- Dependency Management: `composer`
- Database: `PostgreSQL`
- Protocol: `HTTP`
- Queue: `Redis`

### Database migration
#### Run Manual
Run `.sql` script manually one by one with ordering by prefix e.g 000001_, 000001_, etc

or

#### Using `migrate` CLI
1. Installation [source](https://github.com/golang-migrate/migrate)
    - WINDOWS:
        ```
        scoop install migrate
        ```
    - MAC: 
        ```
        brew install golang-migrate
        ```
    - LINUX: 
        ```
        $ curl -L https://packagecloud.io/golang-migrate/migrate/gpgkey| apt-key add -
        $ echo "deb https://packagecloud.io/golang-migrate/migrate/ubuntu/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/migrate.list
        $ apt-get update
        $ apt-get install -y migrate
        ```
    please visit https://www.freecodecamp.org/news/database-migration-golang-migrate for detail instruction
2. Add database config into `.env` file. Ensure following variables exist and don't forget to fill the value:
    ```
    DB_HOST=
    DB_PORT=
    DB_DATABASE=
    DB_USERNAME=
    DB_PASSWORD=
    ```
3. Execute Migration
    - Up: `make migrate-up`
    - Down: `make migrate-down`

### API Reference

#### Register

```http
  POST /register
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `email`      | `string` | **Required**. user email|
| `password`      | `string` | **Required**. user password|

#### Login

```http
  POST /login
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `email`      | `string` | **Required**. user email|
| `password`      | `string` | **Required**. user password|

#### Send email

```http
  POST /email
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `is_html`      | `boolean` | **Optional**. default value false|
| `email_to`      | `string` | **Required**. Target email to whom the email will be sent|
| `subject`      | `string` | **Required**. Email subject|
| `body`      | `string` | **Required**. Email body that can be in HTML format by setting `is_html` to `true`|

example
```json
{
    "is_html": "true",
    "email_to": "user@user.com",
    "subject": "testing subject",
    "body": "<br>Hello World<br>"
}
```