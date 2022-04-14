<?php

namespace DAuth\ChallengeSDK\Commands\Instances;

use Ethereum\SmartContract;
use Web3\Contract;

class GetContractInstance
{
    public function run(): Contract {
        $abi = file_get_contents(resource_path('abi/dauth.json'));

        return new Contract(
            config('dauth.eth.url'),
            json_decode($abi,true)['abi']
        );
    }
}
