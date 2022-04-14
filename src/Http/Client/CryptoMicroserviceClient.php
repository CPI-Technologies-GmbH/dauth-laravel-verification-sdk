<?php


namespace DAuth\ChallengeSDK\Http\Client;

/**
 * Please check the dAuth Github for the crypto microservice. In production this should run on another server, which is not
 * reachable over any public network.
 */
class CryptoMicroserviceClient
{
    public static function encrypt(string $message, string $publicKey): string
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            config('dauth.microserviceUrl') . '/encrypt',
            ['multipart' => [
                [
                    'name' => 'msg',
                    'contents' => $message
                ],
                [
                    'name' => 'enc',
                    'contents' => $publicKey
                ],
            ]]
        )->getBody();
        $response = json_decode($response, true);

        return $response['data']['encryptedResponse'];
    }
    public static function sign(string $message, string $provider): array
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            config('dauth.microserviceUrl') . '/sign',
            ['multipart' => [
                [
                    'name' => 'msg',
                    'contents' => $message
                ],
                [
                    'name' => 'provider',
                    'contents' => $provider
                ],
            ]]
        )->getBody();
        $response = json_decode($response, true);

        return $response['data'];
    }
    public static function verifyrsv(string $message, string $r, string $s, int $v, string $targetAddr): bool
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            config('dauth.microserviceUrl') . '/verifyrsv',
            ['multipart' => [
                [
                    'name' => 'msg',
                    'contents' => $message
                ],
                [
                    'name' => 'r',
                    'contents' => $r
                ],
                [
                    'name' => 's',
                    'contents' => $s
                ],
                [
                    'name' => 'v',
                    'contents' => $v
                ],
                [
                    'name' => 'targetAddr',
                    'contents' => $targetAddr
                ],
            ]]
        )->getBody();
        $response = json_decode($response, true);

        return $response['data']['success'] === true;
    }

    public static function verify(string $message, string $signature, string $targetAddr): bool
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            config('dauth.microserviceUrl') . '/verify',
            ['multipart' => [
                [
                    'name' => 'msg',
                    'contents' => $message
                ],
                [
                    'name' => 'signature',
                    'contents' => $signature
                ],
                [
                    'name' => 'targetAddr',
                    'contents' => $targetAddr
                ],
            ]]
        )->getBody();
        $response = json_decode($response, true);

        return $response['data']['success'] === true;
    }
}
