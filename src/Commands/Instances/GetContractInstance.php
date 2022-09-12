<?php

namespace MaxTrax\ChallengeSDK\Commands\Instances;

use Web3\Contract;

class GetContractInstance
{
    public function run(): Contract {
        $abi = file_get_contents(resource_path('abi/maxtrax.json'));

        return new Contract(
            config('maxtrax.eth.url'),
            json_decode($abi,true)['abi']
        );
    }
}
