<?php

class YiveApiClient
{
    const HOST = 'https://app.yive.io';

    private $token;

    function __construct($token)
    {
        $this->token = $token;
    }

    function get($endpoint)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::HOST.$endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Authorization: Bearer ".$this->token
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $response = json_encode(['message' => curl_error($curl)]);
        }

        curl_close($curl);

        return json_decode($response, true);
    }

    function post($endpoint, $data = [])
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::HOST.$endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => is_array($data) ? json_encode($data) : $data,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Bearer ".$this->token,
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $response = json_encode(['message' => curl_error($curl)]);
        }

        curl_close($curl);

        return json_decode($response, true);
    }
}