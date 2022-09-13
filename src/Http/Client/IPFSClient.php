<?php

namespace MaxTrax\ChallengeSDK\Http\Client;

/*
    Copied from Cloutier\PhpIpfsApi;
	This code is licensed under the MIT license.
	See the LICENSE file for more information.
*/

class IPFSClient {
    private $gatewayIP;
    private $gatewayPort;
    private $gatewayApiPort;
    private $gatewayApiHeaders;

    function __construct($ip = "localhost", $port = "8080", $apiPort = "5001", $gatewayApiHeaders = null) {
        $this->gatewayIP      = $ip;
        $this->gatewayPort    = $port;
        $this->gatewayApiPort = $apiPort;
        $this->gatewayApiHeaders = $gatewayApiHeaders;
    }

    public function cat ($hash) {
        $ip = $this->gatewayIP;
        $port = $this->gatewayPort;
        return $this->curl("https://$ip:$port/ipfs/$hash");

    }

    public function add ($content) {
        $ip = $this->gatewayIP;
        $port = $this->gatewayApiPort;

        $req = $this->curl("https://$ip:$port/api/v0/add?stream-channels=true", $content);
        $req = json_decode($req, TRUE);

        if(empty($req['Hash'])) {
            throw new \Exception("Invalid response from ipfs (https://$ip:$port/api/v0/add?stream-channels=true): " . json_encode($req));
        }

        return $req['Hash'];
    }

    public function ls ($hash) {
        $ip = $this->gatewayIP;
        $port = $this->gatewayApiPort;

        $response = $this->curl("https://$ip:$port/api/v0/ls/$hash");

        $data = json_decode($response, TRUE);

        return $data['Objects'][0]['Links'];
    }

    public function size ($hash) {
        $ip = $this->gatewayIP;
        $port = $this->gatewayApiPort;

        $response = $this->curl("https://$ip:$port/api/v0/object/stat/$hash");
        $data = json_decode($response, TRUE);

        return $data['CumulativeSize'];
    }

    public function pinAdd ($hash) {

        $ip = $this->gatewayIP;
        $port = $this->gatewayApiPort;

        $response = $this->curl("https://$ip:$port/api/v0/pin/add/$hash");
        $data = json_decode($response, TRUE);

        return $data;
    }

    public function version () {
        $ip = $this->gatewayIP;
        $port = $this->gatewayApiPort;
        $response = $this->curl("https://$ip:$port/api/v0/version");
        $data = json_decode($response, TRUE);
        return $data["Version"];
    }

    private function curl ($url, $data = null) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);

        $additionalHeaders = $this->gatewayApiHeaders;

        if ($data != null) {
            $additionalHeaders = array_merge($additionalHeaders, array('Content-Type: multipart/form-data; boundary=a831rwxi1a3gzaorw1w2z49dlsor'));

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "--a831rwxi1a3gzaorw1w2z49dlsor\r\nContent-Type: application/octet-stream\r\nContent-Disposition: file; \r\n\r\n" . $data);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $additionalHeaders);

        $output = curl_exec($ch);

        if ($output == FALSE) {
            throw new \Exception('No response from IPFS!');
        }
        curl_close($ch);


        return $output;
    }
}


