<?
IncludeModuleLangFile(__FILE__);

class cityExport{
	public $citArray;
	public $arMP;
	public $regLinks;
	public $errCity;
	public $timeLimit;
	public static $addedCity = 0;
	private $fname;

	public $startTime;
	public $curIndex;
	public $country;
	public $countryMode = 'rus';

	public $arUploaded;
	public $result;

	public $error;

	public static $impMode = false;

	function cityExport($countryLink='rus',$timeLimit=60,$fname='tmpExport.txt'){
		$countryParams = sdekOption::getCountryDescr($countryLink);
		if(!$countryParams)
			return false;

		$this->fname = $fname;
	// может отсутствовать csv-шник, если запущен не откуда надо, можно кинуть warning и попробовать скачать его
		if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.sdekOption::$MODULE_ID.'/'.$countryParams['FILE'])){
			if(!sdekOption::requestCityFile($countryLink)){
				$this->error = GetMessage('IPOLSDEK_SYNCTY_ERR_NOFILE');
				return;
			}
		}
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.sdekOption::$MODULE_ID.'/'.$fname))
			unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.sdekOption::$MODULE_ID.'/'.$fname);

		$this->citArray  = explode("\n",file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.sdekOption::$MODULE_ID.'/'.$countryParams['FILE']));
		$this->timeLimit = ($timeLimit) ? $timeLimit : 60;
		$this->curIndex = 1;
		$this->arUploaded = array();

		$countries = CSaleLocation::GetCountryList();

		while($country=$countries->Fetch()){
			if(in_array($country['NAME'],$countryParams['NAME'])){
				$this->country = $country['ID'];
				break;
			}
		}

		if(!$this->country && $countries->SelectedRowsCount())
			$this->error = GetMessage('IPOLSDEK_SYNCTY_ERR_NOCOUNTRY').GetMessage('IPOLSDEK_SYNCTY_'.$countryLink);

		$this->countryMode = $countryLink;

		$this->arMP = $this->getBitrixMP();

		unlink($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".sdekOption::$MODULE_ID."/".$countryParams['FILE']);
	}

	function getBitrixMP(){
		if(!$this->regLinks)
			$this->getRegLinks();
		$arCities = array('ORIG'=>array(),'LANG'=>array());
		$workedId = array();
		$smpledRegions = array();

		$cities = CSaleLocation::GetList(array(),array("COUNTRY_ID"=>$this->country,"REGION_LID"=>"ru","CITY_LID"=>"ru"));
		while($element=$cities->Fetch()){
			if(!in_array($element['ID'],$workedId)){
				if($element['REGION_NAME']){
					if(!array_key_exists($element['REGION_NAME_ORIG'],$smpledRegions))
						$smpledRegions[$element['REGION_NAME_ORIG']] = $this->simpleRegion($element['REGION_NAME_ORIG']);
					if(!array_key_exists($element['REGION_NAME'],$smpledRegions))
						$smpledRegions[$element['REGION_NAME']] = $this->simpleRegion($element['REGION_NAME']);

					$arCities['ORIG'][$smpledRegions[$element['REGION_NAME_ORIG']]][$element['ID']] = $this->simpleCity($element['CITY_NAME_ORIG']);
					$arCities['LANG'][$smpledRegions[$element['REGION_NAME']]][$element['ID']]      = $this->simpleCity($element['CITY_NAME']);
					$workedId[]=$element['ID'];
				}else
					$arCities['NO_REGION'][$element['ID']] = $element['CITY_NAME'];
			}
		}

		$cities = CSaleLocation::GetList(array(),array("COUNTRY_ID"=>$this->country,"REGION_LID"=>false,"CITY_LID"=>"ru"));
		while($element=$cities->Fetch()){
			if(!in_array($element['ID'],$workedId)){
				if($element['REGION_NAME']){
					if(!array_key_exists($element['REGION_NAME_ORIG'],$smpledRegions))
						$smpledRegions[$element['REGION_NAME_ORIG']] = $this->simpleRegion($element['REGION_NAME_ORIG']);
					if(!array_key_exists($element['REGION_NAME'],$smpledRegions))
						$smpledRegions[$element['REGION_NAME']] = $this->simpleRegion($element['REGION_NAME']);

					$arCities['ORIG'][$smpledRegions[$element['REGION_NAME_ORIG']]][$element['ID']] = $this->simpleCity($element['CITY_NAME_ORIG']);
					$arCities['LANG'][$smpledRegions[$element['REGION_NAME']]][$element['ID']]      = $this->simpleCity($element['CITY_NAME']);
					$workedId[]=$element['ID'];
				}else
					$arCities['NO_REGION'][$element['ID']] = $element['CITY_NAME'];
			}
		}
		$arCities['links'] = array_flip($smpledRegions);

		return $arCities;
	}
	
	function getRegLinks(){
		$this->regLinks = array();
		for($i=1;$i<89;$i++)
			$this->regLinks[GetMessage("IPOLSDEK_RK_".$i)] = GetMessage("IPOLSDEK_RV_".$i);
	}
	
	function start(){
		if($this->impMode) return;
		if($this->error){
			$this->result=array(
				'result' => 'error',
				'error'  => $this->error
			);
			return false;
		}
		$this->startTime = mktime();
		for($i=$this->curIndex;$i<count($this->citArray);$i++){
			if(!$this->citArray[$i])
				continue;
			$tmpCity = explode(';',$this->citArray[$i]);

			$this->getCity(sdekHelper::zaDEjsonit($tmpCity));
			if(mktime()-$this->startTime > $this->timeLimit){
				$this->curIndex=$i;
				$this->pauseExport();
				return;
			}
		}
		$this->endExport();
	}

	function getCity($cityArr){
		$arCities = array();
		$mode = '';
		$sRcA = $this->simpleRegion($cityArr[3]);
		// ищем, будто регион найден или без региона
		if(array_key_exists($sRcA,$this->arMP['ORIG']))
			$mode = 'ORIG';
		elseif(array_key_exists($sRcA,$this->arMP['LANG']))
			$mode = 'LANG';
		else
			$mode = 'NO_REGION';
		$sCity = $this->simpleCity($cityArr[2]);
		$fnded = $this->findCity($sCity,$this->arMP[$mode][$sRcA],$sRcA,$cityArr);
		//если не нашли и есть непонятные регионы - ищем в куче непонятных регионов
		if(!$fnded && array_key_exists("UNDEFINED",$this->arMP['ORIG'])){
			if($mode == 'NO_REGION')
				$this->arMP['links'][$sRcA] = $sRcA;
			$fnded = $this->findCity($sCity,$this->arMP['ORIG']["UNDEFINED"],$sRcA,$cityArr);
		}
		if(!$fnded && array_key_exists("UNDEFINED",$this->arMP['LANG'])){
			if($mode == 'NO_REGION')
				$this->arMP['links'][$sRcA] = $sRcA;
			$fnded = $this->findCity($sCity,$this->arMP['LANG']["UNDEFINED"],$sRcA,$cityArr);
		}
		if(!$fnded && array_key_exists("NO_REGION",$this->arMP)){
			$this->arMP['links'][$sCity] = $sCity;
			$fnded = $this->findCity($sCity,$this->arMP["NO_REGION"],$sCity,$cityArr);
		}

		if(!$fnded)
			$this->errCity['notFound'][]=array(
				'sdekId' => $cityArr[0],
				'name'   => $cityArr[2],
				'region' => $cityArr[3],
				'pay'    => $cityArr[5]
			);
	}
	
	function findCity($sCity,$arSearch,$sRcA,$cityArr){
		$fnded = false;
		if(is_array($arSearch))
			foreach($arSearch as $id => $cityName)
				if($sCity == $cityName){
					// синхронизация, а не экспорт
					if(!self::$impMode){
						// найден
						if(!array_key_exists($id,$this->arUploaded)){
							$ic = sqlSdekCity::Add(array(
								'BITRIX_ID' => $id,
								'SDEK_ID'   => $cityArr[0],
								'NAME'      => $cityName,
								'REGION'    => $this->arMP['links'][$sRcA],
								'PAYNAL'    => $cityArr[5],
								'COUNTRY'	=> $this->countryMode
							));
							$fnded = true;
							if($ic){
								$this->arUploaded[$id]=$cityArr[3]." ".$cityArr[2];
								$this->addedCity++;
							}
							else
								$this->arUploaded[$id]=$cityArr[3].", ".$cityArr[2];
						}else{
							if(!is_array($this->errCity['many']))
								$this->errCity['many'] = array();
							if(!array_key_exists($id,$this->errCity['many']))
								$this->errCity['many'][$id]=array(
									'takenLbl' => $this->arUploaded[$id],
									'sdekCity' => array(),
								);
							$this->errCity['many'][$id]['sdekCity'][$cityArr[0]]=array(
								'name'     => $cityArr[2],
								'region'   => $cityArr[3],
							);
						}
					}else
						$fnded = true;
					break;
				}
		return $fnded;
	}
	
	function simpleCity($city){
		if(strpos($city,"(")!==false)
			$city = trim(substr($city,0,strpos($city,"(")));
		if(strpos($city,".")!==false)
			$city = trim(substr($city,0,strpos($city,".")));
		if(strpos($city,",")!==false)
			$city = trim(substr($city,0,strpos($city,",")));
		if(strpos($city,GetMessage('IPOLSDEK_CHANGE_YO'))!==false)
			$city = str_replace(GetMessage('IPOLSDEK_CHANGE_YO'),GetMessage('IPOLSDEK_CHANGE_YE'),$city);
		return $city;
	}

	function simpleRegion($region){
		if(class_exists('sdekhelper'))
			$region = sdekhelper::toUpper($region);

		$finded = false;

		foreach($this->regLinks as $find => $label){
			if(strpos($region,$find)!==false){
				$region = $label;
				$finded = true;
				break;
			}
		}

		return ($finded)?$region:"UNDEFINED";
	}
	
	protected function pauseExport(){
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.sdekOption::$MODULE_ID.'/'.$this->fname,serialize($this));
		$this->result = array(
			'result' => 'pause',
			'added'  => $this->addedCity,
			'done'   => $this->curIndex,
			'total'  => count($this->citArray)
		);
	}

	public function quickSave(){
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.sdekOption::$MODULE_ID.'/'.$this->fname,serialize($this));
	}
	
	protected function endExport(){
		$addCntr = ($this->countryMode == 'rus') ? '' : '_'.$this->countryMode;
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.sdekOption::$MODULE_ID.'/errCities'.$addCntr.'.json',json_encode(sdekOption::zajsonit($this->errCity)));
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.sdekOption::$MODULE_ID.'/'.$this->fname))
			unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.sdekOption::$MODULE_ID.'/'.$this->fname);
		$this->result =  array(
			'result' => 'end',
			'added'  => ($this->addedCity) ? $this->addedCity : 0,
		);
	}
	
	// импорт
	static $importIndex  = 0;
	static $addedCities  = false;
	static $backRegions  = false;
	static $arNewRegions = false;
	static $workMode = 0;
	static $starter = false;

	static $cityType = false;
	
	function loadCities(){
		$this->impMode = true;
		if($this->error){
			$this->result=array(
				'result' => 'error',
				'error'  => $this->error
			);
			return false;
		}

		if(!$this->cityType && sdekOption::isLocation20()){
			$this->cityType = sdekOption::getCityTypeId();
			if(!$this->cityType){
				$this->result=array(
					'result' => 'error',
					'error'  => GetMessage("IPOLSDEK_IMPORT_ERROR_NOCITY"),
				);
				return false;
			}
		}

		$this->startTime = mktime();
		// определяем связку с регионами
		if(!$this->backRegions)
			foreach($this->arMP['links'] as $connect => $bitrixName){
				$region = CSaleLocation::GetList(array(),array("COUNTRY_ID"=>$this->country,"REGION_LID"=>"ru",'REGION_NAME'=>$bitrixName,"CITY_NAME"=>false))->Fetch();
				if($region)
					if(!is_array($this->backRegions))
						$this->backRegions = array();
					$this->backRegions[$connect] = $region['REGION_ID'];
			}

		// определяем города ошибочные
		if($this->workMode == 0){
			if($this->curIndex < count($this->citArray))
				for($i=$this->curIndex;$i<count($this->citArray);$i++){
					if(!$this->citArray[$i])
						continue;
					$tmpCity = explode(';',$this->citArray[$i]);
					$this->getCity(sdekHelper::zaDEjsonit($tmpCity));
					if(mktime()-$this->startTime > $this->timeLimit){
						$this->curIndex=$i;
						$this->pauseImport();
						return;
					}
				}
			$this->workMode = 1;
		}

		// заполняем связку город-регион
		if(
			$this->workMode == 1 && 
			count($this->errCity['notFound'])
		){
			$checker = count($this->errCity['notFound']);
			if($this->importIndex < $checker){
				for($i=$this->importIndex;$i<$checker;$i++){
					if(!$this->errCity['notFound'][$i]) continue;
					if(!is_array($this->addedCities))
						$this->addedCities = array();
					$regions = $this->findRegion($this->errCity['notFound'][$i]['region']);
					if($regions && $regions['REGION_ID'])
						$this->addedCities[]= array(
							"LINK"    => $i,
							"REGION"  => $regions['REGION_ID'],
							"TOTABLE" => $regions['TOTABLE'],
						);
					else
					if(mktime()-$this->startTime > $this->timeLimit){
						$this->importIndex=$i;
						$this->pauseImport();
						return;
					}
				}
			}
			$this->importIndex = 0;
			$this->workMode = 2;
		}

		// добавляем города
		if(
			$this->workMode == 2 &&
			count($this->addedCities)
		){
			$checker = count($this->addedCities);
			$this->error = '';
			if($this->importIndex < $checker)
				for($i=$this->importIndex;$i<$checker;$i++){
					if(sdekOption::isLocation20() && !$this->starter && $this->starter !== 0){
						$res = \Bitrix\Sale\Location\LocationTable::getList(
						array(
							'filter' => array("%CODE"=>'C_'),
							'order'  => array("ID" => "DESC", "CODE" => "DESC"),
							'limit'  => 1,
							'select' => array('CODE')
						))->Fetch();
						if($res)
							$this->starter = intval(substr($res['CODE'],2))+1;
						else
							$this->starter = 0;
					}

					$name = $this->errCity['notFound'][$this->addedCities[$i]['LINK']]['name'];
					$sourse = $this->errCity['notFound'][$this->addedCities[$i]['LINK']];
					$engName = Cutil::translit($name,"ru",array("replace_space" => " "));
					$ID = false;
					if(sdekOption::isLocation20()){
						$res = \Bitrix\Sale\Location\LocationTable::add(array(
							'CODE' => 'C_'.($i+$this->starter),
							'SORT' => '100',
							'PARENT_ID' => $this->addedCities[$i]['REGION'],
							'TYPE_ID' => $this->cityType,
							'NAME' => array(
								'ru' => array(
									'NAME' => $name
								),
								'en' => array(
									'NAME' => $engName
								),
							),
						),array('REBALANCE' => false));
						if($res->isSuccess())
							$ID = $res->getId();
						else
							$this->error .= $name.": ".implode(';',$res->getErrorMessages())."<br>";
					}else{
						$arFields = array(
							"SORT" => 100,
							"COUNTRY_ID" => $this->country,
							"WITHOUT_CITY" => "N",
							"REGION_ID" => $this->addedCities[$i]['REGION'],
							"CITY" => array(
								"NAME" => $engName,
								"SHORT_NAME" => $engName,
								"ru" => array(
									"LID" => "ru",
									"NAME" => $name,
									"SHORT_NAME" => $name
								),
								"en" => array(
									"LID" => "en",
									"NAME" => $engName,
									"SHORT_NAME" => $engName
								)
						   )
						);

						$ID = CSaleLocation::Add($arFields);
					}

					if($ID){
							$ic = sqlSdekCity::Add(array(
								'BITRIX_ID' => $ID,
								'SDEK_ID'   => $sourse['sdekId'],
								'NAME'      => $name,
								'REGION'    => $this->addedCities[$i]['TOTABLE'],
								'PAYNAL'    => $sourse['pay'],
								'COUNTRY'	=> $this->countryMode
							));
							if($ic)
								$this->addedCity++;
					}
					if(mktime()-$this->startTime > $this->timeLimit){
						$this->importIndex=$i+1;
						$this->pauseImport();
						return;
					}
				}
			$this->endExport();
		}
	}

	function findRegion($_region){
		$region = $this->simpleRegion($_region);
		$arReturn = array(
			"TOTABLE" => $this->arMP['links'][$region],
		);
		if(array_key_exists($region,$this->backRegions))
			$arReturn['REGION_ID'] = $this->backRegions[$region];
		return $arReturn;
	}

	function pauseImport(){
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.sdekOption::$MODULE_ID.'/'.$this->fname,serialize($this));
		switch($this->workMode){
			case false:
			case 0:
				$mode = "gettingCities";
				$done = $this->curIndex;
				$left = count($this->citArray);
			break;
			case 1:
				$mode = "definingCities";
				$done = $this->importIndex;
				$left = count($this->errCity['notFound']);
			break;
			case 2:
				$mode  = "addingCities";
				$done = $this->importIndex;
				$left = count($this->addedCities);	
			break;
		}

		$this->result = array(
			'result' => 'pause',
			'mode'   => $mode,
			'done'   => $done,
			'total'  => $left
		);
	}
}
?>