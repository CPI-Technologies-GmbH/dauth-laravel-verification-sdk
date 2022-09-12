<?php


namespace MaxTrax\ChallengeSDK\Commands;


use MaxTrax\ChallengeSDK\Commands\CreateTokenContent;
use MaxTrax\ChallengeSDK\Commands\Instances\GetIPFSInstance;
use MaxTrax\ChallengeSDK\Commands\VerifyMaxTraxData;
use MaxTrax\ChallengeSDK\Http\Client\CryptoMicroserviceClient;
use InvalidArgumentException;

/**
 * Class AbstractVerification
 * @package MaxTrax\ChallengeSDK\Verifications
 */
trait MaxTraxVerificationProvider {

    public function verifySignature(string $message, string $signature, string $address) {
        if(!CryptoMicroserviceClient::verify($message, $signature, $address)) {
            throw new InvalidArgumentException('Signature invalid!');
        }
    }

    public function verifyHistory(?array $history) {
        if($history !== null) {
            app(VerifyMaxTraxData::class)->run($history);
        }
    }

    public function postVerification(string $providerName, string $providerAddress, string $providerLogo, array $data, string $encryptionKey, string $address, ?array $history): array {
        $content = app(CreateTokenContent::class)->run(
            $history ?? [],
            $address,
            $providerName,
            $providerAddress,
            $providerLogo,
            json_encode($data),
            collect($data)->keys()->toArray(),
            $encryptionKey
        );

        $ipfs = app(GetIPFSInstance::class)->run();
        $path = $ipfs->add(json_encode($content));

        return [
            'ipfsPath' => $path,
            'content' => $content,
            'signature' => CryptoMicroserviceClient::sign($path, $providerName),
            'signerAddress' => $providerAddress
        ];
    }
}
