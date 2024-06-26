<?php
require_once 'vendor/autoload.php';
class Api{
    protected string $api;
    protected string $url;
    public function __construct(string $api, string $url){
        $this->api = $api;
        $this->url = $url;
    }
    protected function getResponse(): object
    {
        $parameters = [
            'start' => '1',
            'limit' => '5000',
            'convert' => 'USD'
        ];

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: ' . $this->api,
        ];
        $qs = http_build_query($parameters);
        $request = "{$this->url}?{$qs}";


        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $request,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1
        ]);

        $response = curl_exec($curl);
        $data = json_decode($response);
        curl_close($curl);
        return $data;
    }
}
