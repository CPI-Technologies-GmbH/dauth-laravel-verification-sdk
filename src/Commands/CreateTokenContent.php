<?php

namespace MaxTrax\ChallengeSDK\Commands;

use MaxTrax\ChallengeSDK\Http\Client\CryptoMicroserviceClient;

class CreateTokenContent
{
    protected VerifyMaxTraxData $verifyMaxTraxData;

    /**
     * @param VerifyMaxTraxData $verifyMaxTraxData
     */
    public function __construct(VerifyMaxTraxData $verifyMaxTraxData)
    {
        $this->verifyMaxTraxData = $verifyMaxTraxData;
    }

    public function run(array $history, string $address, string $providerName, string $providerAddress, string $logoUrl, string $content, array $addedFields, string $publicKey) {
        $i = 0;
        $attributes = [];
        $previousSignChallenge = null;
        $data = [];
        $new = [];

        // Will throw an exception if history is not valid. Will check on Ethereum blockchain, if the signer signatures are all valid
        $this->verifyMaxTraxData->run($history);

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
            'description' => config('maxtrax.content.description'),
            'external_url' => config('maxtrax.content.external_url'),
            'image' => config('maxtrax.content.image_path') . $address,
            'verifications' => $data,
            'version' => config('maxtrax.content.version'),
            'attributes' => $attributes,
        ];
    }
}
