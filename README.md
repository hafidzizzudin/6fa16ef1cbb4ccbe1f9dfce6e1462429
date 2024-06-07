## Email Sender Project
This service main functionality is to send email asynchronously using worker. The detail information about this service is listed below:
- Language: `PHP`
- Dependency Management: `composer`
- Database: `MySQL`
- Protocol: `HTTP`

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
