# Overview

**AUGMENT EXPERIENCES WITH A SAFER, SIMPLER AND MORE PRIVATE WAY TO LOGIN**

A paradigm shift in the registration and sign-in process, Affinidi Login is a game-changing solution for developers. With our revolutionary passwordless authentication solution your user's first sign-in doubles as their registration, and all the necessary data for onboarding can be requested during this streamlined sign-in/signup process. End users are in full control, ensuring that they consent to the information shared in a transparent and user-friendly manner. This streamlined approach empowers developers to create efficient user experiences with data integrity, enhanced security and privacy, and ensures compatibility with industry standards.

| Passwordless Authentication                                                                                                                                                                                                   | Decentralised Identity Management                                                                                                                                                                                                      | Uses Latest Standards                                                                                                                                                                                                 |
| ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Offers a secure and user-friendly alternative to traditional password-based authentication by eliminating passwords and thus removing the vulnerability to password-related attacks such as phishing and credential stuffing. | Leverages OID4VP to enable users to control their data and digital identity, selectively share their credentials and authenticate themselves across multiple platforms and devices without relying on a centralised identity provider. | Utilises OID4VP to enhance security of the authentication process by verifying user authenticity without the need for direct communication with the provider, reducing risk of tampering and ensuring data integrity. |

## Introduction

This package extends Socialite to enable passwordless authentication with the Affinidi OIDC provider.

Learn more about Laravel Socialite [here](https://laravel.com/docs/10.x/socialite)

**Quick Links**

1. [Installation & Usage](#setup--run-application-from-playground-folder)
2. [Create Affinidi Login Configuration](#create-affinidi-login-configuration)
3. Affinidi Login Integration with [Sample Laravel project](#setup--run-application-from-playground-folder)
4. Affinidi Login Integration in [Fresh Laravel Project](#setup--run-application-from-playground-folder)
5. Affinidi Login Integration in [Existing Laravel Project](#setup--run-application-from-playground-folder)

## Installation & Basic Usage

To get started with Affinidi Socialite, follow these steps:

1. Install the Affinidi Socialite package using Composer:

```
composer require affinidi/laravel-socialite-affinidi
```

2. Add the following configuration to your `config/services.php` file:

```
'affinidi' => [
    'base_uri' => env('PROVIDER_ISSUER'),
    'client_id' => env('PROVIDER_CLIENT_ID'),
    'client_secret' => env('PROVIDER_CLIENT_SECRET'),
    'redirect' => '/login/affinidi/callback',
],
```

3. Extend the setup of the Affinidi Socialite driver using our helper class in the `boot()` function of `app\Providers\AppServiceProvider.php`:

```
public function boot(): void
{
    $socialite = $this->app->make(Factory::class);
    // Setup the affinidi driver
    \Affinidi\SocialiteProvider\AffinidiSocialite::extend($socialite);
}
```

## Authentication

To authenticate users using an OAuth provider, you will need two routes: one for redirecting the user to the OAuth provider, and another for receiving the callback from the provider after authentication.

The example routes below demonstrate the implementation of both routes:

```
use Laravel\Socialite\Facades\Socialite;

Route::get('/login/affinidi/redirect', function () {
    return Socialite::driver('affinidi')->redirect();
});

Route::get('/login/affinidi/callback', function () {
    $user = Socialite::driver('affinidi')->user();

    // $user->token
});
```

## Create Affinidi Login Configuration

Create the Login Configuration using [Affinidi Dev Portal](https://portal.affinidi.com/) as illustrated [here](https://docs.affinidi.com/docs/affinidi-login/login-configuration/#using-affinidi-portal). You can given name as "Socialite App" and Redirect URIs as per your application specific e.g. "https://<domain-name>/login/affinidi/callback"

**Important**: Safeguard the Client ID and Client Secret and Issuer; you'll need them for setting up your environment variables. Remember, the Client Secret will be provided only once.

**Note**: By default Login Configuration will requests only `Email VC`, if you want to request email and profile VC, you can refer PEX query under (docs\loginConfig.json)[playground\example\docs\loginConfig.json] and execute the below affinidi CLI command to update PEX

```
affinidi login update-config --id <CONFIGURATION_ID> -f docs\loginConfig.json
```

## Setup & Run application from playground folder

Open the directory `playground/example` in VS code or your favorite editor

1.  Install the dependencies by executing the below command in terminal
    ```
    composer install
    ```
2.  Create the `.env` file in the sample application by running the following command
    ```
    cp .env.example .env
    ```
3.  Create Affinidi Login Configuration as mentioned [here](#create-affinidi-login-configuration)

4.  Update below environment variables in `.env` based on the auth credentials received from the Login Configuration created earlier:
    ```
    PROVIDER_CLIENT_ID="<AUTH.CLIENT_ID>"
    PROVIDER_CLIENT_SECRET="<AUTH.CLIENT_SECRET>"
    PROVIDER_ISSUER="<AUTH.CLIENT_ISSUER>"
    ```
    Sample values looks like below
    ```
    PROVIDER_CLIENT_ID="xxxxx-xxxxx-xxxxx-xxxxx-xxxxx"
    PROVIDER_CLIENT_SECRET="xxxxxxxxxxxxxxx"
    PROVIDER_ISSUER="https://yyyy-yyy-yyy-yyyy.apse1.login.affinidi.io"
    ```
5.  Run the application
    ```
    php artisan serve
    ```
6.  Open the [http://localhost:8000/](http://localhost:8000/), which displays login page
    **Important**: You might error on redirect URL mismatch if you are using `http://127.0.0.1:8000/` instead of `http://localhost:8000/`.
7.  Click on `Affinidi Login` button to initiate OIDC login flow with Affinidi Vault

<br/>
<br/>

## Integration Affinidi Login - Fresh Laravel Project

If you want to start fresh without any base reference app, then you can follow the below steps

### Create Laravel Project

Before creating your first Laravel project, you should ensure that your local machine has `PHP` and `Composer` installed.

1. You may create a new Laravel project via the Composer `create-project` command

```
composer create-project laravel/laravel example-app
```

**Note**: If you enounter any issue on creating project like `fileInfo`, then you may have enable the fileInfo extension in your `php.ini` file like below

```
extension=fileinfo
```

2. After the project has been created, start Laravel's local development server using the Laravel's Artisan CLI `serve` command

```
cd example-app

php artisan serve
```

3. Once you have started the Artisan development server, your application will be accessible in your web browser at [http://localhost:8000](http://localhost:8000)

**Note**: If you encounter an error on generating Key, then execute the below command which updates `APP_KEY` in your .env file and then run the app

```
php artisan key:generate
```

### Install Affinidi Socialite Provider

To get started with Socialite, use the Composer package manager to add the package to your project's dependencies

1. Install Affinidi Socialite Library

```
composer require affinidi/laravel-socialite-affinidi
```

2. Open `AppServiceProvider.php` file under `app\Providers`, and bootstrap the Affinidi driver to socialite class inside function `boot()`, the code should look like below

```
public function boot(): void
{
    $socialite = $this->app->make(Factory::class);

    \Affinidi\SocialiteProvider\AffinidiSocialite::extend($socialite);
}
```

3. Add credentials for the Affinidi OIDC provider, should be placed in your application's `config/services.php` configuration file,

```
'affinidi' => [
    'base_uri' => env('PROVIDER_ISSUER'),
    'client_id' => env('PROVIDER_CLIENT_ID'),
    'client_secret' => env('PROVIDER_CLIENT_SECRET'),
    'redirect' => '/login/affinidi/callback',
],
```

### Use Affinidi Provider Inside Controller

1. Create `LoginRegisterController.php` file under `app\Http\Controllers`, which has actions to perform normal login, logout, affinidi login and its callback, reference can be found [here](playground\example\app\Http\Controllers\LoginRegisterController.php)
2. Open `routes\web.php` file and Add Web Routes which invokes the above login controller actions, reference can be found [here](playground\example\routes\web.php)
3. Create file `login.blade.php` under `resources\views` for adding Affinidi Login button, reference can be found [here](playground\example\resources\views\login.blade.php)
4. Create dashboard `dashboard.blade.php` under `resources\views` for displaying the logged in user info, reference can be found [here](playground\example\resources\views\dashboard.blade.php)

### Run the application

1. Run the application
   ```
   php artisan serve
   ```
2. Open the [http://localhost:8000/](http://localhost:8000/), which displays login page
   **Important**: You might error on redirect URL mismatch if you are using `http://127.0.0.1:8000/` instead of `http://localhost:8000/`.
3. Click on `Affinidi Login` button to initiate OIDC login flow with Affinidi Vault

<br/>
<br/>
<br/>

## Integration Affinidi Login to existing Laravel Project

If you want to integrate Affinidi Login to any existing PHP Laravel Application using socialite, then you can follow the below steps

### Install Affinidi Socialite Provider

To get started with Socialite, use the Composer package manager to add the package to your project's dependencies

1. Install Affinidi Socialite Library

```
composer require affinidi/laravel-socialite-affinidi
```

2. Open `AppServiceProvider.php` file under `app\Providers`, and bootrap the affinidi driver to socialite class inside function `boot()`, the code should look like below

```
public function boot(): void
{
    $socialite = $this->app->make(Factory::class);

    \Affinidi\SocialiteProvider\AffinidiSocialite::extend($socialite);
}
```

3. Add credentials for the Affinidi OIDC provider, should be placed in your application's `config/services.php` configuration file,

```
'affinidi' => [
    'base_uri' => env('PROVIDER_ISSUER'),
    'client_id' => env('PROVIDER_CLIENT_ID'),
    'client_secret' => env('PROVIDER_CLIENT_SECRET'),
    'redirect' => '/login/affinidi/callback',
],
```

4. Create the Login Configuration as per step [here](#create-affinidi-login-configuration)

5. Update below environment variables in .env based on the auth credentials obtained from the previous step

PROVIDER_CLIENT_ID="<AUTH.CLIENT_ID>"
PROVIDER_CLIENT_SECRET="<AUTH.CLIENT_SECRET>"
PROVIDER_ISSUER="<AUTH.CLIENT_ISSUER>"

6. Add the Affinidi Login button in your login page, reference can be found [here](<(playground\example\resources\views\login.blade.php)>)

7. Use socialite driver as 'affinidi' in route handler / controller, reference controller can be found [here](playground\example\app\Http\Controllers\LoginRegisterController.php)

### Run the application

1. Run the application
   ```
   php artisan serve
   ```
2. Open the [http://localhost:8000/](http://localhost:8000/), which displays login page
   **Important**: You might error on redirect URL mismatch if you are using `http://127.0.0.1:8000/` instead of `http://localhost:8000/`.
3. Click on `Affinidi Login` button to initiate OIDC login flow with Affinidi Vault

## Call Affinidi APIs

For example, if you want to issue a VC

- Generate Personal access token using command line tool more details [here]() and update .env file with details

```
VAULT_URL="https://vault.affinidi.com"
API_GATEWAY_URL="https://apse1.api.affinidi.io"
TOKEN_ENDPOINT="https://apse1.auth.developer.affinidi.io/auth/oauth2/token"
PROJECT_ID=""
KEY_ID=""
TOKEN_ID=""
PASSPHRASE=""
PRIVATE_KEY=""
```

- Set the service config file

```
'affinidi_tdk' => [
    'api_gateway_url' => env('API_GATEWAY_URL'),
    'token_endpoint' => env('TOKEN_ENDPOINT'),
    'project_Id' => env('PROJECT_ID'),
    'private_key' => env('PRIVATE_KEY'),
    'token_id' => env('TOKEN_ID'),
    'passphrase' => env('PASSPHRASE'),
    'key_id' => env('KEY_ID'),
    'vault_url' => env('VAULT_URL'),
],
```

- Code snippet to invoke TDK helper methods by reading config values

```
 $credentials_request =
    [
        [
            "credentialTypeId" => "AnyTcourseCertificateV1R0",
            "credentialData" => [
                "courseID" => "EMP-IT-AUTOMATION-2939302",
                "course" => [
                    "name" => "IT Automation with Python",
                    "type" => "Professional Certificate",
                    "url" => "",
                    "courseDuration" => "45 Days"
                ],
                "learner" => [
                    "name" => "",
                    "email" => "grajesh.c@affinidi.com",
                    "phone" => ""
                ],
                "achievement" => [
                    "score" => "100",
                    "grade" => "A"
                ],
                "courseMode" => "online",
                "completionDate" => "08/09/2024"
            ]
        ]
    ];


    $apiMethod = '/cis/v1/' . config('services.affinidi_tdk.project_Id') . '/issuance/start';

    $data = \Affinidi\SocialiteProvider\AffinidiTDK::InvokeAPI($apiMethod, [
        'data' => $credentials_request,
        'claimMode' => "TX_CODE"
    ]);

```
