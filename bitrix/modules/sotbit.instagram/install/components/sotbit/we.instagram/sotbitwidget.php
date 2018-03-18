<?php
class SotbitWidget {
	public $config = array();
	public $profile = array();
	public $data = array();
	public $width = 260;
	public $inline = 4;
	public $view = 12;
	public $toolbar = true;
	public $preview = 'small';
	public $imgWidth = 0;
    public $cache = 6;
    public $module_id = "sotbit.instagram";
    public $charset = "utf-8";
	protected $cacheId;
	public function __construct(){
        $this->config["LOGIN"] = COption::GetOptionString($this->module_id, "LOGIN");
        $this->config["CLIENT_ID"] = COption::GetOptionString($this->module_id, "CLIENT_ID");
		$this->setOptions();
	}
	public function getData(){
	    $obCache = new CPHPCache();
        $cache_time = $this->cache;
        //$cache_time = 0;
        $cache_id = implode("&", $_GET);
        $cache_path = '/sotbit.instagram/';
        if ($cache_time > 0 && $obCache->InitCache($cache_time, $cache_id, $cache_path))
        {
            $vars = $obCache->GetVars();
            $this->profile = $vars["profile"];
            $this->data = $vars["data"];
        }elseif($obCache->StartDataCache() )
        {
            $this->makeQuery();
            $this->doCharset();
            $obCache->EndDataCache(array("profile"=>$this->profile, "data"=>$this->data));
        }

	}

    public function doCharset()
    {
        //if(strtolower($this->charset)=="windows-1251")
        {
            if(isset($this->profile) && !empty($this->profile))
            {   
                $this->circleCharset($this->profile);
            }
            if(isset($this->data) && !empty($this->data))
            {
                $this->circleCharset($this->data);
            }
        }
    }

    public function circleCharset(&$arData)
    {
        if(is_array($arData))
        {
            foreach($arData as &$ar)
            {
                $this->circleCharset($ar);
            }
        }elseif(is_object($arData))
        {
            $arData = (array)$arData;
            foreach($arData as &$ar)
            {
                $this->circleCharset($ar);
            }
        }
        else{
            if(strtolower($this->charset)=="windows-1251") $arData = iconv("utf-8", "windows-1251", $arData);
        }
    }

	public function makeQuery(){
		$user = $this->send('https://api.instagram.com/v1/users/search?q='.$this->config['LOGIN'].'&client_id='.$this->config['CLIENT_ID']);
		$user = json_decode($user);
		if(!empty($user)){
			if($user->meta->code == 200){
				$this->profile['userid'] = $user->data[0]->id;
				$this->profile['username'] = $user->data[0]->username;
				$this->profile['avatar'] = $user->data[0]->profile_picture;
				unset($user);
			}
			else die('User OR CLIENT_ID not found');
		}
		else die('Can\'t connect to Instagram API server.');
		$stats = $this->send('https://api.instagram.com/v1/users/'.$this->profile['userid'].'/?client_id='.$this->config['CLIENT_ID'].'');
		$stats = json_decode($stats);
		if(!empty($stats)){
			if($stats->meta->code == 200){
				$this->profile['posts']	= $stats->data->counts->media;
				$this->profile['followers'] = $stats->data->counts->followed_by;
				$this->profile['following'] = $stats->data->counts->follows;
				unset($stats);
			}
			else die('User OR CLIENT_ID not found');
		}
		else die('Can\'t connect to Instagram API server.');
		$images = $this->send('https://api.instagram.com/v1/users/'.$this->profile['userid'].'/media/recent/?client_id='.$this->config['CLIENT_ID'].'&count='.$this->config['imgCount']);
		$images = json_decode($images);
		if(!empty($images)){
			if($images->meta->code == 200){
				if(!empty($images->data)){
					$this->data = $images->data;
					unset($images);
				}
				else die('Empty data');
			}
			else die('CLIENT_ID not found');
		}
		else die('Can\'t connect to Instagram API server.');
	}

	public function send($url){
		if(extension_loaded('curl')){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_URL, $url);
			$answer = curl_exec($ch);
			curl_close($ch);
			return $answer;
		}
		elseif(ini_get('allow_url_fopen') AND extension_loaded('openssl')){
			$answer = file_get_contents($url);
			return $answer;
		}
		else die('Can\'t send request. You need the cURL extension OR set allow_url_fopen to "true" in php.ini and openssl extension');
	}
	public function setOptions(){
		$this->width -= 2;
		if(isset($_GET['width']) && $_GET['width'])
			$this->width = (int)$_GET['width']-2;
		if(isset($_GET['inline']) && $_GET['inline'])
			$this->inline = (int)$_GET['inline'];
		if(isset($_GET['view']) && $_GET['view'])
			$this->view = (int)$_GET['view'];
		if(isset($_GET['toolbar']) AND $_GET['toolbar'] == "N")
			$this->toolbar = false;
		if(isset($_GET['preview']))
			$this->preview = $_GET['preview'];
        if(isset($_GET['cache']) && $_GET['cache'])
			$this->cache = $_GET['cache'];
        if(isset($_GET['charset']) && $_GET['charset'])
			$this->charset = $_GET['charset'];
        if(isset($_GET['title']) && $_GET['title'])
			$this->title = urldecode($_GET['title']);
        if(isset($_GET['show']) && $_GET['show'])
			$this->show = urldecode($_GET['show']);
		if($this->width>0) 
			$this->imgWidth = round(($this->width-(17+(9*$this->inline)))/$this->inline);
	}
}