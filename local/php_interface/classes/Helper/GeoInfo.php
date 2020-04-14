<?php

namespace Helper;

//todo: не очень хорошо определяет города по ip

class GeoInfo
{
//    private static $url = "http://www.geoplugin.net/json.gp?ip=#ip#&lang=ru";
    public $ip;
    public $city;
    private static $key = "ad864ff282702acf25d0e7de1f4fe6dd";

    public function __construct($ip)
    {
        $this->city = "Москва";
        $this->ip = $ip;

        $url = "http://api.ipstack.com/$ip?access_key=".self::$key."&language=ru";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));

        $response =  @json_decode(curl_exec($ch), true);
        curl_close($ch);

        if($response["city"] && $response["country_code"] === "RU") {
            $this->city = $response["city"];
        }
    }
}
