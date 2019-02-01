<?

Class CAqwVideo
{
    protected $services = array('CAqwVideoYoutube', 'CAqwVideoDailymotion', 'CAqwVideoVimeo', 'CAqwVideoYandex', 'CAqwVideoMetacafe', 'CAqwVideoImage');

    function getServiceByUrl($url)
    {
        foreach ($this->services as $service) {
            if (class_exists($service)) {
                $init = new $service($url);
                if (method_exists($init, 'checkService')) {
                    if ($init->checkService() === true) {
                        return $init;
                    }
                }
            }
        }
        return false;
    }

    function onProlog()
    {
        if (CJSCore::IsExtRegistered('fancybox') === false) {
            if (COption::GetOptionString("aqw.video", "jquery") == "N") {
                CJSCore::RegisterExt("fancybox", Array(
                    "js" => "/bitrix/js/aqw.video/fancybox/jquery.fancybox.js",
                    "css" => "/bitrix/js/aqw.video/fancybox/jquery.fancybox.css"
                ));
            } else {
                CJSCore::RegisterExt("fancybox", Array(
                    "js" => "/bitrix/js/aqw.video/fancybox/jquery.fancybox.js",
                    "css" => "/bitrix/js/aqw.video/fancybox/jquery.fancybox.css",
                    "rel" => array('jquery')
                ));
            }
        }
        if (CJSCore::IsExtRegistered('fancyboxbuttons') === false) {
            CJSCore::RegisterExt("fancyboxbuttons", Array(
                "js" => "/bitrix/js/aqw.video/fancybox/helpers/jquery.fancybox-buttons.js",
                "css" => "/bitrix/js/aqw.video/fancybox/helpers/jquery.fancybox-buttons.css",
                "rel" => array('fancybox')
            ));
        }
        if (CJSCore::IsExtRegistered('fancyboxmedia') === false) {
            CJSCore::RegisterExt("fancyboxmedia", Array(
                "js" => "/bitrix/js/aqw.video/fancybox/helpers/jquery.fancybox-media.js",
                "rel" => array('fancybox')
            ));
        }
        CJSCore::Init(array("fancybox", "fancyboxbuttons", "fancyboxmedia"));

    }
}

abstract class CAqwVideoAbstract
{
    protected $url;
    protected $hosts = array();

    function __construct($url)
    {
        $this->url = trim($url);
        $this->parse_url = parse_url($this->url);
    }

    function checkService()
    {
        $parse_url = $this->parse_url;
        return (isset($parse_url['host']) and in_array(strtolower($parse_url['host']), $this->hosts)) ? true : false;
    }
}

class CAqwVideoYoutube extends CAqwVideoAbstract
{
    protected $hosts = array('youtube.com', 'www.youtube.com', 'youtu.be', 'www.youtu.be');

    function getDataByParams($arParams)
    {
        $parse_url = $this->parse_url;
        if (isset($parse_url['query'])) {
            parse_str($parse_url['query'], $query);
            if (isset($query['v'])) {
                return array(
                    'id' => $query['v'],
                    'type' => 'youtube',
                    'preview' => '//i' . rand(1, 3) . '.ytimg.com/vi/' . $query['v'] . '/0.jpg',
                    'player' => '<iframe width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '" allowfullscreen src="//www.youtube.com/embed/' . $query['v'] . '?autoplay=1" frameborder="0"></iframe>',
                    //'player' => '<object width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '"><param name="movie" value="http://www.youtube.com/v/' . $query['v'] . '&hl=en&fs=1&color1=0x2b405b&color2=0x6b8ab6&autoplay=1&enablejsapi=1"></param><param name="wmode" value="transparent"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . $query['v'] . '?&autoplay=1&enablejsapi=1" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="always" allowfullscreen="true" width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '"></embed></object>',
                    'src' => '//www.youtube.com/embed/' . $query['v'],
                );
            }
        }
        if (isset($parse_url['path']) && substr($parse_url['path'], 1)) {

            return array(
                'id' => substr($parse_url['path'], 1),
                'type' => 'youtube',
                'preview' => '//i' . rand(1, 3) . '.ytimg.com/vi/' . substr($parse_url['path'], 1) . '/0.jpg',
                'player' => '<iframe width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '" allowfullscreen src="//www.youtube.com/embed/' . substr($parse_url['path'], 1) . '?autoplay=1" frameborder="0"></iframe>',
                //'player' => '<object width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '"><param name="movie" value="http://www.youtube.com/v/' . substr($parse_url['path'], 1) . '&hl=en&fs=1&color1=0x2b405b&color2=0x6b8ab6&autoplay=1&enablejsapi=1"></param><param name="wmode" value="transparent"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . substr($parse_url['path'], 1) . '?&autoplay=1&enablejsapi=1" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="always" allowfullscreen="true" width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '"></embed></object>',
                'src' => '//www.youtube.com/embed/' . substr($parse_url['path'], 1),
            );
        }
        return false;
    }
}

class CAqwVideoDailymotion extends CAqwVideoAbstract
{
    protected $hosts = array('dailymotion.com', 'www.dailymotion.com');

    function getDataByParams($arParams)
    {
        preg_match('/\/video\/(\w+)_/', $this->url, $matches);
        if ($matches) {

            return array(
                'id' => $matches[1],
                'type' => 'dailymotion',
                'preview' => '//www.dailymotion.com/thumbnail/800x600/video/' . $matches[1],
                'player' => '<iframe src="//www.dailymotion.com/embed/video/' . $matches[1] . '?autoPlay=1" width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '" frameborder="0" allowfullscreen></iframe>',
//                'player' => '<object width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '"><param name="movie" value="http://www.dailymotion.com/swf/' . $matches[1] . '?autoPlay=1" ></param><param name="wmode" value="transparent" ></param><param name="allowFullScreen" value="true" ></param><param name="allowScriptAccess" value="always" ></param><embed wmode="transparent" src="http://www.dailymotion.com/swf/' . $matches[1] . '?autoPlay=1" type="application/x-shockwave-flash" width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '" allowFullScreen="true" allowScriptAccess="always"></embed></object>',
                'src' => '//www.dailymotion.com/swf/' . $matches[1],
            );
        }
        return false;
    }
}

class CAqwVideoVimeo extends CAqwVideoAbstract
{
    protected $hosts = array('vimeo.com', 'www.vimeo.com');

    function getDataByParams($arParams)
    {
        $parse_url = $this->parse_url;
        if (isset($parse_url['path']) && substr($parse_url['path'], 1)) {
            $videoUrl = "https://vimeo.com/api/v2/video/" . intval(substr($parse_url['path'], 1)) . ".json";
            $videoJSON = file_get_contents($videoUrl);
            $videoData = json_decode($videoJSON, true);

            return array(
                'id' => substr($parse_url['path'], 1),
                'type' => 'vimeo',
                'preview' => str_replace('http:', '', $videoData[0]['thumbnail_large']),
                'player' => '<iframe src="//player.vimeo.com/video/' . substr($parse_url['path'], 1) . '?api=1&autoplay=true" frameborder="0" allowfullscreen width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '"></iframe>',
                //'player' => '<object width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '"><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=' . substr($parse_url['path'], 1) . '&autoplay=true" ></param><param name="wmode" value="transparent" ></param><param name="allowFullScreen" value="true" ></param><param name="allowScriptAccess" value="always" ></param><embed wmode="transparent" src="http://vimeo.com/moogaloop.swf?clip_id=' . substr($parse_url['path'], 1) . '&autoplay=true" type="application/x-shockwave-flash" width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '" allowFullScreen="true" allowScriptAccess="always"></embed></object>',
                'src' => '//player.vimeo.com/video/' . substr($parse_url['path'], 1),
            );
        }
        return false;
    }
}

class CAqwVideoYandex extends CAqwVideoAbstract
{
    protected $hosts = array('video.yandex.ru', 'www.video.yandex.ru');

    function getDataByParams($arParams)
    {
        $videoUrl = "https://video.yandex.ru/oembed.json?url=" . rawurlencode($this->url);
        $videoJSON = file_get_contents($videoUrl);
        $videoData = json_decode($videoJSON, true);

        if (is_array($videoData)) {
            $videoID = mt_rand();
            $videoSrc = str_replace('/get/', '/lite/', $videoData['thumbnail_url']);
            $videoParseSrc = pathinfo($videoSrc);
            $videoSrc = $videoParseSrc['dirname'];

            return array(
                'id' => $videoID,
                'type' => 'yandex',
                'preview' => str_replace('http:', '', $videoData['thumbnail_url']),
                'player' => '<object width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '"><param name="flashvars" value="autostart=yes"></param><param name="movie" value="' . $videoSrc . '" ></param><param name="wmode" value="transparent" ></param><param name="allowFullScreen" value="true" ></param><param name="allowScriptAccess" value="always" ></param><embed flashvars="autostart=yes" wmode="transparent" src="' . $videoSrc . '" type="application/x-shockwave-flash" width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '" allowFullScreen="true" allowScriptAccess="always"></embed></object>',
                'src' => $videoSrc,
            );
        }
        return false;
    }
}

class CAqwVideoMetacafe extends CAqwVideoAbstract
{
    protected $hosts = array('metacafe.com', 'www.metacafe.com');

    function getDataByParams($arParams)
    {
        preg_match('/\/watch\/([\w-]+\/\w*)/', $this->url, $matches);
        if ($matches) {
            $video_id_arr = explode('/', $matches[1]);

            return array(
                'id' => $matches[1],
                'type' => 'metacafe',
                'preview' => '//s1.mcstatic.com/thumb/' . $video_id_arr[0] . '.jpg',
                'player' => '<iframe src="//www.metacafe.com/embed/' . $video_id_arr[0] . '/?ap=1" width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '" allowFullScreen frameborder="0"></iframe>',
                //'player' => '<object width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '"><param name="flashVars" value="playerVars=showStats=no|autoPlay=yes|" ></param><param name="movie" value="http://www.metacafe.com/fplayer/' . $matches[1] . '.swf"></param><param name="wmode" value="transparent" ></param><param name="allowFullScreen" value="true" ></param><param name="allowScriptAccess" value="always" ></param><embed flashVars="playerVars=showStats=no|autoPlay=yes|" src="http://www.metacafe.com/fplayer/' . $matches[1] . '.swf" width="' . $arParams['WIDTH'] . '" height="' . $arParams['HEIGHT'] . '" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" name="Metacafe_' . $matches[1] . '" type="application/x-shockwave-flash"></embed></object>',
                'src' => '//www.metacafe.com/embed/' . $video_id_arr[0] . '/',
            );
        }
        return false;
    }
}

class CAqwVideoImage extends CAqwVideoAbstract
{
    protected $extension_images = array('jpg' => true, 'bmp' => true, 'gif' => true, 'png' => true);

    function checkService()
    {
        $parse_url = $this->parse_url;
        if (isset($parse_url['host'])) {
            //если находим картинку
            if (isset($parse_url['path'])) {
                $path_parts = pathinfo($parse_url['path']);
                if (isset($path_parts['extension']) && array_key_exists(strtolower($path_parts['extension']), $this->extension_images)) {
                    return true;
                }
            }
        }
        return false;
    }

    function getDataByParams()
    {
        $uniquePhoto = uniqid('aqw_video_fullimg_');

        return array(
            'id' => $uniquePhoto,
            'type' => 'image',
            'preview' => str_replace('http:', '', $this->url),
            'player' => '<img border="0" id="' . $uniquePhoto . '" src="">',
        );
    }
}

?>