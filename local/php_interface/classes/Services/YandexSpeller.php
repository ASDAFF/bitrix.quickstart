<?php

namespace Cpeople\Classes\Services;


class YandexSpeller
{
    public static function checkText($text)
    {
        $result = self::api($text);

        return $result ? $result : false;
    }

    public static function correctText($text)
    {
        $mistakes = self::checkText($text);

        if(!$mistakes) return $text;

        foreach(array_reverse($mistakes) as $mistake)
        {
            if(!empty($mistake['s']))
            {
                $text = substr($text, 0, $mistake['col']) . $mistake['s'][0] . substr($text, $mistake['col'] + $mistake['len']);
            }
        }

        return $text;
    }

    private static function api($text)
    {
        $url = 'http://speller.yandex.net/services/spellservice.json/checkText?text=';

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_URL => $url . urlencode($text)
        ));
        $result = curl_exec($ch);
        $resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($resultCode != 200) return false;

        $result = json_decode($result, true);

        return $result;
    }
}