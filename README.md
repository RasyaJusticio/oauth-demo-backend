
# OAuth Demo Backend

Auth Demo is a backend application built with Laravel 11, utilizing Passport and Socialite for OAuth authentication. It serves a REST API for user authentication, supporting both normal email/password login and Google OAuth.

This project is intended to be used with [OAuth Demo Frontend](https://github.com/RasyaJusticio/oauth-demo-frontend)

## Routes

### API Endpoints

| Method | URL                  | Description |
| ------ | -------------------- | ----------- |
| `POST` | `api/auth/register`  | Register using name, email, and passworn. Returns the token as a cookie |
| `POST` | `api/auth/login`     | Login using email and password. Returns the token as a cookie |
| `POST` | `api/auth/logout`    | Logout using the cookie with the token |
| `POST` | `api/auth/google/exchange` | Exchanges the auth code with a cookie that has the token |

### Web Endpoints

| URL                    | Description |
| ---------------------- | ----------- |
| `auth/google/redirect` | Redirects the user to the Google OAuth login page to authenticate via Google |
| `auth/google/callback` | Handles the Google OAuth callback and redirects user to the frontend with the auth code |

## Run Locally

1. Clone the project

    ```bash
    git clone https://github.com/RasyaJusticio/oauth-demo-backend
    ```

2. Go to the project directory

    ```bash
    cd oauth-demo-backend
    ```

3. Setup the environment

   - Copy the `.env.example` file to `.env`

    ```bash
    cp .env.example .env
    ```

   - Configure your database

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=oauth-demo
    DB_USERNAME=root
    DB_PASSWORD=
    ```

   - Add your Google Client ID and Client Secret

    ```env
    GOOGLE_CLIENT_ID=
    GOOGLE_CLIENT_SECRET=
    ```

4. Install dependencies

    ```bash
    composer install
    ```

5. Migrate the migrations

    ```bash
    php artisan migrate
    ```

6. Generate the keys

   - Generate Laravel App key

    ```bash
    php artisan key:generate
    ```

   - Generate Passport encryption key

    ```bash
    php artisan passport:keys
    ```

   - Create the personal access Client

    ```bash
    php artisan passport:client --personal
    ```

7. Start the server

    ```bash
    php artisan serve
    ```

## License

[MIT](/LICENSE)
