<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arParams["COUNT"] = ($arParams["COUNT"] == "") ? 1 : $arParams["COUNT"];
$rsSites = CSite::GetByID(SITE_ID);
$arSite = $rsSites->Fetch();
        
if($arParams["CONSUMER_KEY"] != "" && $arParams["CONSUMER_SECRET"] != "" && $arParams["USER_TOKEN"] != "" && $arParams["USER_SECRET"] != "")
{
    require_once("libs/tmhOAuth.php");
    require_once("libs/tmhUtilities.php");
        
    if($this->StartResultCache(false, $arParams["ACCOUNT"]))
    {
        $tmhOAuth = new tmhOAuth(array(
            'consumer_key'    => $arParams["CONSUMER_KEY"], //'YOUR_CONSUMER_KEY',
            'consumer_secret' => $arParams["CONSUMER_SECRET"], //'YOUR_CONSUMER_SECRET',
            'user_token'      => $arParams["USER_TOKEN"], //'A_USER_TOKEN',
            'user_secret'     => $arParams["USER_SECRET"], //'A_USER_SECRET',
            'curl_ssl_verifypeer'   => false
        ));
		//1/statuses/user_timeline
        $code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/user_timeline'), array(
            'screen_name'      => $arParams["ACCOUNT"],
            'count'            => $arParams["COUNT"],
        ));
        
        $minutes = array(GetMessage("V1RT_TWITTER_M1"), GetMessage("V1RT_TWITTER_M2"), GetMessage("V1RT_TWITTER_M3"));
        $hours = array(GetMessage("V1RT_TWITTER_H1"), GetMessage("V1RT_TWITTER_H2"), GetMessage("V1RT_TWITTER_H3"));

        if($code == 200)
        {
            $tweets = json_decode($tmhOAuth->response['response'], true);
            foreach($tweets as $tweet)
            {
                $diff = time() - strtotime($tweet['created_at']);
                if($diff < 60*60)
                {
                    $n = floor($diff/60);
                    $created_at = $n." ".$minutes[($n%10==1 && $n%100!=11 ? 0 : ($n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20) ? 1 : 2))]." назад";
                }
                elseif($diff < 60*60*24)
                {
                    $n = floor($diff/(60*60));
                    $created_at = $n." ".$hours[($n%10==1 && $n%100!=11 ? 0 : ($n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20) ? 1 : 2))]." назад";;
                }
                else
                {
                    $created_at = date('d F', strtotime($tweet['created_at']));
                    $d = explode(" ", $created_at);
                    $a = array('January','February','March','April','May','June','July','August','September','October','November','December');
                    $b = array(
                        GetMessage("V1RT_TWITTER_MON1"), 
                        GetMessage("V1RT_TWITTER_MON2"), 
                        GetMessage("V1RT_TWITTER_MON3"), 
                        GetMessage("V1RT_TWITTER_MON4"), 
                        GetMessage("V1RT_TWITTER_MON5"), 
                        GetMessage("V1RT_TWITTER_MON6"),
                        GetMessage("V1RT_TWITTER_MON7"),
                        GetMessage("V1RT_TWITTER_MON8"),
                        GetMessage("V1RT_TWITTER_MON9"),
                        GetMessage("V1RT_TWITTER_MON10"),
                        GetMessage("V1RT_TWITTER_MON11"),
                        GetMessage("V1RT_TWITTER_MON12")
                    ); 
                    $created_at = $d[0]." ".str_replace($a, $b, $d[1]); 
                }
                
                if($arSite["CHARSET"] != "UTF-8")
                {
                    $arResult["TWITS"][] = array(
                        "TEXT" => preg_replace("#(https?|ftp)://\S+[^\s.,> )\];'\"!?]#", '<!--noindex--><a href="\\0" onclick="window.open(this.href); return false;" rel="nofollow">\\0</a><!--/noindex-->', utf8win1251($tweet["text"])),
                        "CREATED_AT" => $created_at,
                        "ID" => $tweet["id_str"]
                    );
                }
                else
                {
                    $arResult["TWITS"][] = array(
                        "TEXT" => preg_replace("#(https?|ftp)://\S+[^\s.,> )\];'\"!?]#", '<!--noindex--><a href="\\0" onclick="window.open(this.href); return false;" rel="nofollow">\\0</a><!--/noindex-->', $tweet["text"]),
                        "CREATED_AT" => $created_at,
                        "ID" => $tweet["id_str"]
                    );
                }
            }
        }
        /*
        else
            tmhUtilities::pr($tmhOAuth->response);
        */
        $this->IncludeComponentTemplate();
    }
}
?>