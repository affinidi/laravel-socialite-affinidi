# Overview

**AUGMENT EXPERIENCES WITH A SAFER, SIMPLER AND MORE PRIVATE WAY TO LOGIN**

A paradigm shift in the registration and sign-in process, Affinidi Login is a game-changing solution for developers. With our revolutionary passwordless authentication solution your user's first sign-in doubles as their registration, and all the necessary data for onboarding can be requested during this streamlined sign-in/signup process. End users are in full control, ensuring that they consent to the information shared in a transparent and user-friendly manner. This streamlined approach empowers developers to create efficient user experiences with data integrity, enhanced security and privacy, and ensures compatibility with industry standards.

| Passwordless Authentication | Decentralised Identity Management | Uses Latest Standards |
|---|---|---|
| Offers a secure and user-friendly alternative to traditional password-based authentication by eliminating passwords and thus removing the vulnerability to password-related attacks such as phishing and credential stuffing. | Leverages OID4VP to enable users to control their data and digital identity, selectively share their credentials and authenticate themselves across multiple platforms and devices without relying on a centralised identity provider. | Utilises OID4VP to enhance security of the authentication process by verifying user authenticity without the need for direct communication with the provider, reducing risk of tampering and ensuring data integrity. |

## Seamless Affinidi Login Integration in Laravel

Building a Laravel Web Application with Affinidi Login Integration using Socialite.

## Prerequisites
Your local machine should have 
1. [PHP 8.1](https://www.php.net/downloads) 
2. [Composer](https://getcomposer.org/download/) installed.

## Learning 

**Laravel**
Laravel is a web application framework with expressive, elegant syntax. Laravel is accessible, powerful, and provides tools required for large, robust applications.
Laravel has the most extensive and thorough [documentation](https://laravel.com/docs).

**Socialite**
Laravel provides a simple, convenient way to authenticate with OAuth providers, more details [here](https://laravel.com/docs/10.x/socialites).

<br>

## Setup & Run application

Open this directory in VS code or your favorite editor

 1. Install the dependencies by executing the below command in terminal
    ```
    composer install
    ```
 2. If Affinidi Login configuration is already set in your `.env` file, then jump to step 7
 3. Create the `.env` file in the sample application by running the following command
    ```
    cp .env.example .env
    ```
 4. Create [Affinidi Login Configuration](https://docs.affinidi.com/docs/affinidi-login/login-configuration/#create-login-configuration) by giving name as `Laravel App` and `Redirect URIs` as `http://localhost:8000/login/affinidi/callback`. Sample response is given below
    ```
    {
        ...
        "auth": {
            "clientId": "<AUTH.CLIENT_ID>",
            "clientSecret": "<AUTH.CLIENT_SECRET>",
            "issuer": "https://<PROJECT_ID>.apse1.login.affinidi.io"
        }
        ...
    }
    ```
    **Important**: Safeguard the Client ID and Client Secret and Issuer; you'll need them for setting up your environment variables. Remember, the Client Secret will be provided only once.

    **Note**: By default Login Configuration will requests only `Email VC`, if you want to request email and profile VC, you can refer PEX query under `docs\loginConfig.json` and execute the below affinidi CLI command to update PEX
    ```
    affinidi login update-config --id <CONFIGURATION_ID> -f docs\loginConfig.json
    ```
 
 5. Update below environment variables in `.env` based on the auth credentials received from the Login Configuration created earlier:
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
6. Run the application
    ```
    php artisan serve
    ```
7. Open the [http://localhost:8000/](http://localhost:8000/), which displays login page 
    **Important**: You might error on redirect URL mismatch if you are using `http://127.0.0.1:8000/` instead of `http://localhost:8000/`. 
8. Click on `Affinidi Login` button to initiate OIDC login flow with Affinidi Vault
