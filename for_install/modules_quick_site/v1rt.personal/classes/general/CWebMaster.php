<?

class CWebMaster
{
    public static function webmasters()
    {
        self::google();
        self::yandex();
    }
    
    protected static function google()
    {
        global $APPLICATION;
        //<meta name="google-site-verification" content="+nxGUDJ4QpAZ5l9Bsjdi102tLVC21AIh5d1Nl23908vVuFHs34="/>
        $key = COption::GetOptionString("v1rt.personal", "v1rt_personal_wm_google");
        if(strlen($key))
            $APPLICATION->AddHeadString('<meta name="google-site-verification" content="'.$key.'"/>', true);
    }
    
    protected static function yandex()
    {
        global $APPLICATION;
        //<meta name='yandex-verification' content='722e35e8a55101cb' />
        $key = COption::GetOptionString("v1rt.personal", "v1rt_personal_wm_yandex");
        if(strlen($key))
            $APPLICATION->AddHeadString('<meta name="yandex-verification" content="'.$key.'"/>', true);
    }
}

?>