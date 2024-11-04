<?php

namespace Affinidi\SocialiteProvider;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AffinidiTDK
{
    public static function InvokeAPI($apiMethod, $data)
    {
        //Preparing full API Url 
        $apiUrl = config('services.affinidi_tdk.api_gateway_url') . $apiMethod;

        //Getting Project scope token
        $pst = AffinidiTDK::fetchProjectScopedToken();
        Log::info('Invoking Api Url: ' . $apiUrl);

        //Calling API by passing PST with data
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $pst,
            'Content-Type' => 'application/json',
        ])->post($apiUrl, $data);

        $responseJson = $response->json();
        Log::info('Response: ' . json_encode($data));

        return $responseJson;

    }

    public static function fetchProjectScopedToken(): string
    {
        //Check PST is available in file
        $tokenFilePath = 'pst_token.txt';
        if (file_exists($tokenFilePath)) {
            $tokenData = file_get_contents($tokenFilePath);
            if (!AffinidiTDK::isTokenExpired($tokenData)) {
                Log::info('Project Scope Token already exists and its valid ' . $tokenData);
                return $tokenData;
            }
        }
        //Token not exists or expired, so generating new PST 
        Log::info('Generating PST');

        $tdkConfig = config('services.affinidi_tdk');

        $userToken = AffinidiTDK::getUserAccessToken($tdkConfig);

        Log::info('User Access Token: ' . $userToken);

        $api_gateway_url = $tdkConfig['api_gateway_url'];
        $project_Id = $tdkConfig['project_Id'];

        $projectTokenEndpoint = $api_gateway_url . '/iam/v1/sts/create-project-scoped-token';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $userToken,
            'Content-Type' => 'application/json',
        ])->post($projectTokenEndpoint, [
                    'projectId' => $project_Id
                ]);

        Log::info('Response: ' . $response->body());
        $responseData = $response->json();
        Log::info('Parsed Response Data: ' . json_encode($responseData));

        if (!isset($responseData['accessToken'])) {
            Log::error('Access token not found in response: ' . json_encode($responseData));
            $error = new \Error('Access token not found while generating project scope token');
            throw $error;
        }

        Log::info('Access token found in response: ' . $responseData['accessToken']);
        $pst = $responseData['accessToken'];

        file_put_contents($tokenFilePath, $pst);
        return $pst;

    }
    private static function isTokenExpired($token): bool
    {
        list($header, $payload, $signature) = explode('.', $token);

        $payload = json_decode(base64_decode($payload), true);
        if (isset($payload['exp'])) {
            $currentTimestamp = time();
            return $currentTimestamp >= $payload['exp'];
        }

        // If no exp claim, assume expired
        return true;
    }

    private static function getUserAccessToken($tdkConfig)
    {
        $token_endpoint = $tdkConfig['token_endpoint'];
        $private_key = $tdkConfig['private_key'];
        $token_id = $tdkConfig['token_id'];
        $key_id = isset($tdkConfig['key_id']) ? $tdkConfig['key_id'] : $tdkConfig['token_id'];
        $passphrase = isset($tdkConfig['passphrase']) ? $tdkConfig['passphrase'] : null;

        $algorithm = 'RS256';
        $issueTimeS = floor(time());
        // Generate a unique jti value
        $jti = (string) \Str::uuid();
        $payload = [
            'iss' => $token_id,
            'sub' => $token_id,
            'aud' => $token_endpoint,
            'jti' => $jti,
            'iat' => $issueTimeS,
            'exp' => $issueTimeS + 5 * 60
        ];

        $headers = [
            'kid' => $key_id,
        ];
        Log::info('Payload: ' . json_encode($payload));

        $key = openssl_pkey_get_private($private_key, $passphrase);

        $token = JWT::encode($payload, $key, $algorithm, $key_id, $headers);

        Log::info('Token: ' . $token);

        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post($token_endpoint, [
                    'grant_type' => 'client_credentials',
                    'scope' => 'openid',
                    'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                    'client_assertion' => $token,
                    'client_id' => $token_id
                ]);

        Log::info('Response: ' . $response->body());

        $responseData = $response->json();

        if (isset($responseData['access_token'])) {
            return $responseData['access_token'];
        } else {
            Log::error('Access token not found in response: ' . json_encode($responseData));
            $error = new \Error('Access token not found while generating user access token');
            throw $error;
        }

    }
}
