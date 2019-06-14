<?

namespace Api\FormDesigner\Crm;

use Bitrix\Main;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web;

Loc::loadMessages(__FILE__);

class Lead
{
	const CACHE_TTL  = 86400 * 7;
	const CACHE_PATH = '/api.formdesigner/crm/';

	public $error = null;

	private $ID;
	private $arLink;  //Авторизация из базы
	private $arAuth; //Авторизация с помощью Логина и Пароля
	private $authHash;

	public function __construct($ID, $arAuth = null)
	{
		if($ID) {
			if($this->arLink = CrmTable::getRowById($ID)) {
				$this->ID = intval($ID);
			}
		}

		if(is_array($arAuth)) {
			$this->arAuth = array('LOGIN' => trim($arAuth['LOGIN']), 'PASSWORD' => trim($arAuth['PASSWORD']));
		}
	}

	public function getFields($bReload = false)
	{
		$cachePath = self::CACHE_PATH;
		$cacheId   = $this->ID . '_lead.get_fields';

		$cache = Cache::createInstance();

		if($bReload) {
			$cache->clean($cacheId, $cachePath);
		}

		$fields = array();
		if($cache->initCache(self::CACHE_TTL, $cacheId, self::CACHE_PATH)) {
			$fields = $cache->getVars();
		}
		elseif($cache->startDataCache()) {

			$fields = array();
			$result = $this->query('lead.get_fields');
			if($result['FIELDS']) {

				foreach($result['FIELDS'] as $arField) {
					$fields[ $arField['ID'] ] = $arField;
				}

				$cache->endDataCache($fields);
			}
			else {
				$cache->abortDataCache();
			}
		}

		return $fields;
	}

	public function add($arFields)
	{
		return $this->query('lead.add', $arFields);
	}

	public function getAuthHash()
	{
		return $this->authHash;
	}

	private function setAuthHash($hash)
	{
		if(strlen($hash) > 0) {
			$this->authHash = $hash;
			CrmTable::update($this->ID, array('HASH' => $hash));
		}
	}

	private function query($method, $params = array())
	{
		global $APPLICATION;

		if(!$method)
			$method = 'lead.add';

		$arPostFields = array(
			 'method' => $method,
		);

		if($this->arAuth) {
			$arPostFields['LOGIN']    = $this->arAuth['LOGIN'];
			$arPostFields['PASSWORD'] = $this->arAuth['PASSWORD'];
		}
		else {
			$arPostFields['AUTH'] = $this->arLink['HASH'];
		}

		$arPostFields = array_merge($params, $arPostFields);
		$arPostFields = $APPLICATION->ConvertCharsetArray($arPostFields, LANG_CHARSET, 'UTF-8');

		$client = new Web\HttpClient();
		$res    = $client->post($this->arLink['URL'], $arPostFields);
		$arData = \CUtil::JsObjectToPhp($res);

		if($arData['error'] >= 400) {
			$this->error = $arData['error_message'];
		}
		else {
			if($arData['AUTH']) {
				$this->setAuthHash($arData['AUTH']);
			}
		}

		/*
		$arPrint = array(
			 '$_REQUEST'=>$_REQUEST,
			 '$arPostFields'=>$arPostFields,
			 '$arData'=>$arData,
		);
		$tttfile=dirname(__FILE__).'/1_txt.php';
		file_put_contents($tttfile, "<pre>".print_r($arPrint,1)."</pre>\n");
		*/

		return $arData;
	}

}