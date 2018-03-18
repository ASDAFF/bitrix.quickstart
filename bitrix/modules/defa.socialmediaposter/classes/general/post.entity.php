<?
IncludeModuleLangFile(__FILE__);

interface DSocialPosterEntity
{
    /** @noinspection PhpAbstractStaticMethodInspection */
    public static function GetID();
	public static function GetName();
	public function Authorize(DSocialPosterConnection $connection, DSocialPosterParams $settings);
	public function GetParams(DSocialPosterConnection $connection, DSocialPosterParams $settings);
	public function GetHash(DSocialPosterConnection $connection, DSocialPosterParams $settings);
	public function GetSettingsMap();
	public function PostMessage(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $postParams);
	public function PostPhoto(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $albumParams, DSocialPostParams $entityParams, &$sCreatedAlbum);
	public function PostVideo(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $albumParams, DSocialPostParams $entityParams, &$sCreatedAlbum);
}
abstract class DSocialPosterBaseEntity
{
	private $connection;
	private $settings = array();
	private $postParams = array();
	
	public static function GetID()
	{
		return __CLASS__;	
	}
	private function ApplySettings(DSocialPosterParams $settings)
	{
		foreach($this->GetSettingsMap() as $propertyID => $arProperty)
			if($settings->HasParam($propertyID))
				$this->settings[$propertyID] = $settings->GetParam($propertyID);
	}	
	public function GetSettingsMap()
	{
		return array(
			"LOGIN" => array("NAME" => "login", "DESCRIPTION" => "", "TYPE" => "string"),
			"PASSWORD" => array("NAME" => "password", "DESCRIPTION" => "", "TYPE" => "password"),
		);
	}
	public function GetConnection()
	{
		return $this->connection;
	}
	public function GetSettings()
	{
		return $this->settings;
	}
	public function Log($errorCode)
	{
		DSocialMediaPosterEventLog::Add($this->postParams->GetID(), $this->GetID(), $errorCode, $this->connection->GetLastResult());
	}
	public function PostMessage(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $postParams)
	{
		$this->connection = $connection;
		$this->settings = $settings;
		$this->postParams = $postParams;
	}
	public function PostPhoto(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $albumParams, DSocialPostParams $entityParams, &$sCreatedAlbum)
	{
		return false;
	}
	public function PostVideo(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $albumParams, DSocialPostParams $entityParams, &$sCreatedAlbum)
	{
		$sCreatedAlbum = "";
		return $this->PostMessage($connection, $settings, $entityParams);
	}
	public function ParseForm($str)
	{
		$arResult = array();
		if(preg_match_all('#<input[^>]+type="hidden"[^>]+>#is', $str, $match))
		{
			foreach($match[0] as $input)
			{
				$arTags = array();
				if(preg_match_all('#(name|value)\s*=\s*["\']?([^"\']*)["\']?#i', $input, $m))
					$arTags = array_combine($m[1], $m[2]);
				if(!empty($arTags))
					$arResult[$arTags["name"]] = $arTags["value"];
			}
		}
		return $arResult;
	}
}

class DSocialPosterEntityManager extends DSocialIterator
{
	private $iCurrentPos = 0;
	
	private static $instance = null;
	
	public static function GetInstance()
	{
		if(self::$instance == null)
		{		
			self::$instance = new self();						
			$events = GetModuleEvents(DSocialMediaPoster::$MODULE_ID, "OnBuildPosterList");
			while($arEvent = $events->Fetch())
				self::$instance->Add(ExecuteModuleEventEx($arEvent, array()));
		}	
		return self::$instance;
	}
	public function Add($instance)
	{
		if(!$instance instanceof DSocialPosterEntity)
			return false;
			
		foreach($this->array as $ob)
			if($ob instanceof $instance)
				return false;		

		parent::Add($instance);		
	}
	private function __construct()
	{
	}	
	public function SetFirst()
	{
		$this->rewind();
	}
	public function SetPos()
	{
		$this->rewind();
	}
	public function GetByID($ID)
	{
		foreach($this->array as $ob)
		{
			if($ob->GetID() === $ID)
				return $ob;
		}
		return false;
	}
	public function GetNext()
	{
		if($this->valid())
		{
			$res = $this->current();			
			$this->next();
			return $res;
		}
		return false;
	}	
}
?>