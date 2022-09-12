<?php

namespace MaxTrax\ChallengeSDK\Commands;

use MaxTrax\ChallengeSDK\Commands\Instances\GetContractInstance;
use MaxTrax\ChallengeSDK\Exceptions\HistoryMalformedException;
use MaxTrax\ChallengeSDK\Http\Client\CryptoMicroserviceClient;

class VerifyMaxtraxData
{
    private GetContractInstance $getContractInstance;

    /**
     * VerifyHistorySignature constructor.
     * @param GetContractInstance $getContractInstance
     */
    public function __construct(GetContractInstance $getContractInstance)
    {
        $this->getContractInstance = $getContractInstance;
    }

    /**
     * @param array $data
     * @throws HistoryMalformedException#
     */
    public function run(array $data) {
        $i = 0;
        $previousSign = null;

        foreach($data as $key => $value) {
            if($key !== $i) {
                throw new HistoryMalformedException('Wrong order of data');
            }

            $this->checkDataPresent($value, $i);

            if($i > 0 && $previousSign !== $value['previousSign']) {
                throw new HistoryMalformedException('Previous Sign does not match in dataset ' . $i);
            }

            $signature = $value['signature'];
            unset($value['signature']);

            // Check if signer really signed the signature of the sha3
            if(!CryptoMicroserviceClient::verifyrsv(
                json_encode($value),
                // $signature['hash'],
                $signature['r'],
                $signature['s'],
                $signature['v'],
                $value['signer'])
            ) {
                throw new HistoryMalformedException('Signature does not match in dataset ' . $i);
            }

            $contract = $this->getContractInstance->run();

            // Check if the signer was allowed to sign at this time
            $contract->at(config('maxtrax.contract.address'))
                ->call('canSignAt', $value['signer'], $value['timestamp'], function($err, $result) use ($i) {
                    if($result[0] !== true) {
                        throw new HistoryMalformedException('Signer was not allowed to sign at this time in dataset ' . $i);
                    }
                });

            // Compare signer name
            /* $contract->at(config('maxtrax.contract.address'))
                ->call('getSignerName', $value['signer'], function($err, $result) use ($value, $i) {
                    if($result[0] !== $value['provider']) {
                        throw new HistoryMalformedException('Provider name does not match in dataset ' . $i);
                    }
                }); */

            $previousSign = $signature;
        }
    }

    /**
     * @param array $value
     * @param int $index
     * @throws HistoryMalformedException
     */
    private function checkDataPresent(array $value, int $index) {
        if(isset($value['previousSign']) && $index === 0) {
            throw new HistoryMalformedException('Previous Sign can\'t be in first dataset!');
        }

        if($index > 0 && !isset($value['previousSign'])) {
            throw new HistoryMalformedException('Previous Sign is not given in dataset ' . $index);
        }

        if(!isset($value['signer'])) {
            throw new HistoryMalformedException('Signer not given at dataset ' . $index);
        }

        if(!isset($value['timestamp'])) {
            throw new HistoryMalformedException('Timestamp not given at dataset ' . $index);
        }

        if(!isset($value['signature'])) {
            throw new HistoryMalformedException('Signature not given at dataset ' . $index);
        }

        if(!isset($value['data'])) {
            throw new HistoryMalformedException('Data not given at dataset ' . $index);
        }

        if(!isset($value['sha3'])) {
            throw new HistoryMalformedException('Sha3 not given at dataset ' . $index);
        }

        if(!isset($value['provider'])) {
            throw new HistoryMalformedException('Provider not given at dataset ' . $index);
        }
    }
}
