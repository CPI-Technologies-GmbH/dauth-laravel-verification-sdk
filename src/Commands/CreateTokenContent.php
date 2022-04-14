<?php


namespace DAuth\ChallengeSDK\Commands;


use DAuth\ChallengeSDK\Http\Client\CryptoMicroserviceClient;

class CreateTokenContent
{
    protected VerifyDAuthData $verifyDAuthData;

    /**
     * @param VerifyDAuthData $verifyDAuthData
     */
    public function __construct(VerifyDAuthData $verifyDAuthData)
    {
        $this->verifyDAuthData = $verifyDAuthData;
    }

    public function run(array $history, string $address, string $providerName, string $providerAddress, string $logoUrl, string $content, array $addedFields, string $publicKey) {
        $i = 0;
        $attributes = [];
        $previousSignChallenge = null;
        $data = [];
        $new = [];

        // Will throw an exception if history is not valid. Will check on Ethereum blockchain, if the signer signatures are all valid
        $this->verifyDAuthData->run($history);

        foreach($history as $key => $value) {
            $data[$i] = $value;
            $attributes[] = [
                'trait_type' => $value['provider'],
                'value' => 'Verified',
            ];
            $i++;
            $previousSignChallenge = $value['sha3'];
        }

        $new['data'] = CryptoMicroserviceClient::encrypt($content, $publicKey);
        $new['fields'] = $addedFields;
        $new['logoUrl'] = $logoUrl;
        $new['provider'] = $providerName;

        if($previousSignChallenge !== null) {
            $new['previousSign'] = CryptoMicroserviceClient::sign($previousSignChallenge, $providerName);
        }

        $new['sha3'] = hash('sha3-512', json_encode($content));
        $new['signer'] = $providerAddress;
        $new['timestamp'] = time();

        $new['signature'] = CryptoMicroserviceClient::sign(json_encode($new), $providerName);

        $data[$i] = $new;
        $attributes[] = [
            'trait_type' => $providerName,
            'value' => 'Verified',
        ];
        $attributes[] = [
            'trait_type' => 'Verified accounts',
            'value' => $i + 1,
        ];

        return [
            'description' => config('dauth.content.description'),
            'external_url' => config('dauth.content.external_url'),
            'image' => config('dauth.content.image_path') . $address,
            'verifications' => $data,
            'version' => config('dauth.content.version'),
            'attributes' => $attributes,
        ];
    }
}
