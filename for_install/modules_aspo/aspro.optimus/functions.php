<?
CModule::IncludeModule("main");
CModule::IncludeModule("iblock");

set_time_limit(0);

if(!function_exists("ClearAllSitesCacheComponents")){
	function ClearAllSitesCacheComponents($arComponentsNames){
		if($arComponentsNames && is_array($arComponentsNames)){
			global $CACHE_MANAGER;
			$arSites = array();
			$rsSites = CSite::GetList($by = "sort", $order = "desc", array("ACTIVE" => "Y"));
			while($arSite = $rsSites->Fetch()){
			  $arSites[] = $arSite;
			}
			foreach($arComponentsNames as $componentName){
				foreach($arSites as $arSite){
					CBitrixComponent::clearComponentCache($componentName, $arSite["ID"]);
				}
			}
		}
	}
}

if(!function_exists("ClearAllSitesCacheDirs")){
	function ClearAllSitesCacheDirs($arDirs){
		if($arDirs && is_array($arDirs)){
			foreach($arDirs as $dir){
				$obCache = new CPHPCache();
				$obCache->CleanDir("", $dir);
			}
		}
	}
}

if(!function_exists("GetIBlocks")){
	function GetIBlocks(){
		$arRes = array();
		$dbRes = CIBlock::GetList(array(), array("ACTIVE" => "Y"));
		while($item = $dbRes->Fetch()){
			$arRes[$item["LID"]][$item["IBLOCK_TYPE_ID"]][$item["CODE"]][] = $item["ID"];
		}
		return $arRes;
	}
}

if(!function_exists("GetSites")){
	function GetSites(){
		$arRes = array();
		$dbRes = CSite::GetList($by="sort", $order="desc", array("ACTIVE" => "Y"));
		while($item = $dbRes->Fetch()){
			$arRes[$item["LID"]] = $item;
		}
		return $arRes;
	}
}

if(!function_exists("GetCurVersion")){
	function GetCurVersion($versionFile){
		$ver = false;
		if(file_exists($versionFile)){
			$arModuleVersion = array();
			include($versionFile);
			$ver = trim($arModuleVersion["VERSION"]);
		}
		return $ver;
	}
}

if(!function_exists("CreateBakFile")){
	function CreateBakFile($file, $curVersion = CURRENT_VERSION){
		$file = trim($file);
		if(file_exists($file)){
			$arPath = pathinfo($file);
			$backFile = $arPath['dirname'].'/_'.$arPath['basename'].'.back'.$curVersion;
			if(!file_exists($backFile)){
				@copy($file, $backFile);
			}
		}
	}
}

if(!function_exists("RemoveFileFromModuleWizard")){
	function RemoveFileFromModuleWizard($file){
		@unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.MODULE_NAME.'/install/wizards/'.PARTNER_NAME.'/'.MODULE_NAME_SHORT.$file);
		@unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/wizards/'.PARTNER_NAME.'/'.MODULE_NAME_SHORT.$file);
	}
}

if(!function_exists("RemoveFileFromTemplate")){
	function RemoveFileFromTemplate($file, $bModule = true){
		@unlink($_SERVER['DOCUMENT_ROOT'].TEMPLATE_PATH.$file);
		if($bModule){
			RemoveFileFromModuleWizard('/site/templates/'.TEMPLATE_NAME.$file);
		}
	}
}

if(!function_exists('SearchFilesInPublicRecursive')){
	function SearchFilesInPublicRecursive($dir, $pattern, $flags = 0){
		$arDirExclude = array('bitrix', 'upload');
		$pattern = str_replace('//', '/', str_replace('//', '/', $dir.'/').$pattern);
		$files = glob($pattern, $flags);
		foreach(glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir){
			if(!in_array(basename($dir), $arDirExclude)){
				$files = array_merge($files, SearchFilesInPublicRecursive($dir, basename($pattern), $flags));
			}
		}
		return $files;
	}
}

if(!function_exists('RemoveOldBakFiles')){
	function RemoveOldBakFiles(){
		$arDirs = $arFiles = array();
		$arDirExclude = array($_SERVER["DOCUMENT_ROOT"].'/bitrix', $_SERVER["DOCUMENT_ROOT"].'/upload');

		if(file_exists($_SERVER["DOCUMENT_ROOT"].'/bitrix/templates/')){
			if($arTemplates = glob($_SERVER["DOCUMENT_ROOT"].'/bitrix/templates/'.PARTNER_NAME.'*', 0)){
				foreach($arTemplates as $templatePath){
					$arDirs[] = str_replace('//', '/', $templatePath.'/');
				}
			}
		}

		if(file_exists($_SERVER["DOCUMENT_ROOT"].'/local/templates/')){
			if($arTemplates = glob($_SERVER["DOCUMENT_ROOT"].'/local/templates/'.PARTNER_NAME.'*', 0)){
				foreach($arTemplates as $templatePath){
					$arDirs[] = str_replace('//', '/', $templatePath.'/');
				}
			}
		}

		if(file_exists($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/')){
			if($arModules = glob($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.PARTNER_NAME.'*', 0)){
				foreach($arModules as $modulePath){
					$arDirs[] = str_replace('//', '/', $modulePath.'/');
				}
			}
		}

		/*if($arSites = GetSites()){
			foreach($arSites as $siteID => $arSite){
				$arSite['DIR'] = str_replace('//', '/', '/'.$arSite['DIR']);
				if(!strlen($arSite['DOC_ROOT'])){
					$arSite['DOC_ROOT'] = $_SERVER["DOCUMENT_ROOT"];
				}
				$arSite['DOC_ROOT'] = str_replace('//', '/', $arSite['DOC_ROOT'].'/');
				$arDirs[] = str_replace('//', '/', $arSite['DOC_ROOT'].$arSite['DIR']);
			}
		}*/

		$i = 0;

		while($arDirs && ++$i < 10000){
			$dir = array_pop($arDirs);
			$arFiles = array_merge($arFiles, (array)glob($dir.'_*.back*', GLOB_NOSORT));
			foreach((array)glob($dir.'*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir){
				if(strlen($dir) && !in_array($dir, $arDirExclude) && strpos($dir, PARTNER_NAME) !== false){
					$arDirs[] = str_replace('//', '/', $dir.'/');
				}
			}
		}

		if($arFiles){
			foreach($arFiles as $file){
				if(file_exists($file) && !is_dir($file)){
					if(time() - filemtime($file) >= 1209600){ // 14 days
						@unlink($file);
					}
				}
			}
		}
	}
}

if(!function_exists("GetDBcharset")){
	function GetDBcharset(){
		$sql='SHOW VARIABLES LIKE "character_set_database";';
		if(method_exists('\Bitrix\Main\Application', 'getConnection')){
			$db=\Bitrix\Main\Application::getConnection();
			$arResult = $db->query($sql)->fetch();
			return $arResult['Value'];
		}elseif(defined("BX_USE_MYSQLI") && BX_USE_MYSQLI == true){
			if($result = @mysqli_query($sql)){
				$arResult = mysql_fetch_row($result);
				return $arResult[1];
			}
		}elseif($result = @mysql_query($sql)){
			$arResult = mysql_fetch_row($result);
			return $arResult[1];
		}
		return false;
	}
}

if(!function_exists("GetMes")){
	function GetMes($str){
		static $isUTF8;
		if($isUTF8 === NULL){
			$isUTF8 = GetDBcharset() == 'utf8';
		}
		return ($isUTF8 ? iconv('CP1251', 'UTF-8', $str) : $str);
	}
}

if(!function_exists("UpdaterLog")){
	function UpdaterLog($str){
		static $fLOG;
		if($bFirst = !$fLOG){
			$fLOG = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.MODULE_NAME.'/updaterlog.txt';
		}
		if(is_array($str)){
			$str = print_r($str, 1);
		}
		@file_put_contents($fLOG, ($bFirst ? PHP_EOL : '').date("d.m.Y H:i:s", time()).' '.$str.PHP_EOL, FILE_APPEND);
	}
}

if(!function_exists("InitComposite")){
	function InitComposite($arSites){
		if(class_exists("CHTMLPagesCache")){
			if(method_exists("CHTMLPagesCache", "GetOptions")){
				if($arHTMLCacheOptions = CHTMLPagesCache::GetOptions()){
					if($arHTMLCacheOptions["COMPOSITE"] !== "Y"){
						$arDomains = array();
						if($arSites){
							foreach($arSites as $arSite){
								if(strlen($serverName = trim($arSite["SERVER_NAME"], " \t\n\r"))){
									$arDomains[$serverName] = $serverName;
								}
								if(strlen($arSite["DOMAINS"])){
									foreach(explode("\n", $arSite["DOMAINS"]) as $domain){
										if(strlen($domain = trim($domain, " \t\n\r"))){
											$arDomains[$domain] = $domain;
										}
									}
								}
							}
						}

						if(!$arDomains){
							$arDomains[$_SERVER["SERVER_NAME"]] = $_SERVER["SERVER_NAME"];
						}

						if(!$arHTMLCacheOptions["GROUPS"]){
							$arHTMLCacheOptions["GROUPS"] = array();
						}
						$rsGroups = CGroup::GetList(($by="id"), ($order="asc"), array());
						while($arGroup = $rsGroups->Fetch()){
							if($arGroup["ID"] > 2){
								if(in_array($arGroup["STRING_ID"], array("RATING_VOTE_AUTHORITY", "RATING_VOTE")) && !in_array($arGroup["ID"], $arHTMLCacheOptions["GROUPS"])){
									$arHTMLCacheOptions["GROUPS"][] = $arGroup["ID"];
								}
							}
						}

						$arHTMLCacheOptions["COMPOSITE"] = "Y";
						$arHTMLCacheOptions["DOMAINS"] = array_merge((array)$arHTMLCacheOptions["DOMAINS"], (array)$arDomains);
						CHTMLPagesCache::SetEnabled(true);
						CHTMLPagesCache::SetOptions($arHTMLCacheOptions);
						bx_accelerator_reset();
					}
				}
			}
		}
	}
}

if(!function_exists('IsCompositeEnabled')){
	function IsCompositeEnabled(){
		if(class_exists('CHTMLPagesCache')){
			if(method_exists('CHTMLPagesCache', 'GetOptions')){
				if($arHTMLCacheOptions = CHTMLPagesCache::GetOptions()){
					if(method_exists('CHTMLPagesCache', 'isOn')){
						if (CHTMLPagesCache::isOn()){
							if(isset($arHTMLCacheOptions['AUTO_COMPOSITE']) && $arHTMLCacheOptions['AUTO_COMPOSITE'] === 'Y'){
								return 'AUTO_COMPOSITE';
							}
							else{
								return 'COMPOSITE';
							}
						}
					}
					else{
						if($arHTMLCacheOptions['COMPOSITE'] === 'Y'){
							return 'COMPOSITE';
						}
					}
				}
			}
		}

		return false;
	}
}

if(!function_exists('EnableComposite')){
	function EnableComposite($auto = false){
		if(class_exists('CHTMLPagesCache')){
			if(method_exists('CHTMLPagesCache', 'GetOptions')){
				if($arHTMLCacheOptions = CHTMLPagesCache::GetOptions()){
					$arHTMLCacheOptions['COMPOSITE'] = 'Y';
					$arHTMLCacheOptions['AUTO_UPDATE'] = 'Y'; // standart mode
					$arHTMLCacheOptions['AUTO_UPDATE_TTL'] = '0'; // no ttl delay
					$arHTMLCacheOptions['AUTO_COMPOSITE'] = ($auto ? 'Y' : 'N'); // auto composite mode
					CHTMLPagesCache::SetEnabled(true);
					CHTMLPagesCache::SetOptions($arHTMLCacheOptions);
					bx_accelerator_reset();
				}
			}
		}
	}
}
?>