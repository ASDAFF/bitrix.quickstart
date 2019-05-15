<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
$lang_par = GetMessage("MF_LANG");
$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] != "N" && !$USER->IsAuthorized()) ? "Y" : "N");
$arParams["OK_TEXT"] = trim($arParams["OK_TEXT"]);
if(strlen($arParams["OK_TEXT"]) <= 0) $arParams["OK_TEXT"] = GetMessage("MF_OK_MESSAGE");
$field_for_name = isset($arParams['FIELD_FOR_NAME']) ? $arParams['FIELD_FOR_NAME'] : "iu_none";
$field_for_email = isset($arParams['FIELD_FOR_EMAIL']) ? $arParams['FIELD_FOR_EMAIL'] : "iu_none";
$field_for_theme = isset($arParams['FIELD_FOR_THEME']) ? $arParams['FIELD_FOR_THEME'] : "iu_none";
if(!isset($arParams['USE_ATTACH']) || $arParams['USE_IU_PAT'] !== "Y") $arParams['USE_ATTACH'] = "N";
if(!isset($arParams['SAVE_FILE']) || $arParams['USE_ATTACH'] !== "Y") $arParams['SAVE_FILE'] = "N";
if($arParams['USE_ATTACH'] == "Y" && isset($arParams["MAX_SIZE_FILE"])) {
	$arParams["MAX_SIZE_FILE"] = intval($arParams["MAX_SIZE_FILE"]);
	if(strlen($arParams["MAX_SIZE_FILE"]) <= 0 || $arParams["MAX_SIZE_FILE"] > 1024 || $arParams["MAX_SIZE_FILE"] <= 0)
		$arParams["MAX_SIZE_FILE"] = 1024;
} else $arParams["MAX_SIZE_FILE"] = 1024;
if(!($arParams['USE_ATTACH'] == "Y" && isset($arParams["FILE_FORMAT"])) || empty($arParams["FILE_FORMAT"]) || !is_array($arParams["FILE_FORMAT"]))
	$arParams["FILE_FORMAT"] = array("jpg", "bmp", "gif", "png", "zip", "pdf", "doc", "docx", "xls", "xlsx", "odt", "ppt");
$arField_files = array();
$arParams['NEW_EXT_FIELDS'] = array();
if ($arParams["USE_IU_IB"] == "Y") {
	$IB_DET = $arParams['IB_DET'];
	$IB_ANONS = $arParams['IB_ANONS'];
	$IB_NAME_EL = $arParams['IBE_NAME'];
}
if ($arParams["USE_IU_PAT"] == "Y") {
	$pj = 3;
	foreach($arParams["EXT_FIELDS"] as $ext_field) {
		if (preg_match('/^iu_[0-2]$/',$ext_field)) {
			$name_el_ar = $ext_field;
			$nam_fld = $ext_field=='iu_0'?GetMessage("MF_NAME"):
				($ext_field=='iu_1'?"E-mail":
					GetMessage("MF_ET_TEXT"));
		} else {
			$name_el_ar = "iu_".$pj;
			$nam_fld = $ext_field;
			$pj++;
		}
		if (strlen($ext_field) > 0) {
			$arParams['NEW_EXT_FIELDS'][$name_el_ar] = array(
				$nam_fld,
				(in_array($ext_field, $arParams["REQUIRED_FIELDS"]) ? 1 : 0),
				((in_array($ext_field, $arParams["TEXTAREA_FIELDS"]) || $name_el_ar == 'iu_2') ? 1 : 0)
			);
			if($arParams['USE_ATTACH'] == "Y" && in_array($ext_field, $arParams["FILE_FIELDS"])) {
				$arField_files[] = $name_el_ar;
				$arParams['NEW_EXT_FIELDS'][$name_el_ar][3] = 1;
			} else 
				$arParams['NEW_EXT_FIELDS'][$name_el_ar][3] = 0;
			if($field_for_name == $ext_field)
				$field_for_name = $name_el_ar;
			if($field_for_email == $ext_field)
				$field_for_email = $name_el_ar;
			if($field_for_theme == $ext_field)
				$field_for_theme = $name_el_ar;
			if ($arParams["USE_IU_IB"] == "Y") {
				if($arParams['IB_DET'] == $ext_field) 
					$IB_DET = $name_el_ar;
				if($arParams['IB_ANONS'] == $ext_field)
					$IB_ANONS = $name_el_ar;
				if($arParams['IBE_NAME'] == $ext_field)
					$IB_NAME_EL = $name_el_ar;
			}
		}
	}
} else {
	$rsET = CEventType::GetByID($arParams["EVENT_TYPE_ID"],$lang_par);
	$arET = $rsET->Fetch();
	if (preg_match_all('/#(\w+)#\s-\s(.+)/i',$arET["DESCRIPTION"],$matches)) 
		for($pp=0; $pp < count($matches[1]); $pp++)
			$arParams['NEW_EXT_FIELDS'][$matches[1][$pp]] = array(
				$matches[2][$pp],
				(in_array($matches[1][$pp], $arParams["REQUIRED_FIELDS"])?1:0),
				(in_array($matches[1][$pp], $arParams["TEXTAREA_FIELDS"])?1:0),
				0
			);
}
if($_SERVER["REQUEST_METHOD"] == "POST" && strlen($_POST["submit"]) > 0) {
	if(check_bitrix_sessid()) {
		if(!empty($arParams["REQUIRED_FIELDS"])) {
			foreach ($arParams['NEW_EXT_FIELDS'] as $ne_fld => $ne_field) {
				if($ne_field[3] !== 0) {
					if(($ne_field[1]) && strlen($_FILES["file_".$ne_fld]["name"]) <= 1)
						$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_EXT_FIELDS").' &laquo;'.$ne_field[0].'&raquo;.';
				 } else {
					if(($ne_field[1]) && strlen($_POST["custom"][$ne_fld]) <= 1)
						$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_EXT_FIELDS").' &laquo;'.$ne_field[0].'&raquo;.';
				}
			}
		}
		if($arParams["USE_IU_PAT"] == "Y" && $field_for_email !== "iu_none" && strlen($_POST["custom"][$field_for_email]) > 1 && !check_email($_POST["custom"][$field_for_email]))
			$arResult["ERROR_MESSAGE"][] = GetMessage("MF_EMAIL_NOT_VALID");
		if($arParams["USE_CAPTCHA"] == "Y") {
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
			$captcha_code = $_POST["captcha_sid"];
			$captcha_word = $_POST["captcha_word"];
			$cpt = new CCaptcha();
			$captchaPass = COption::GetOptionString("main", "captcha_password", "");
			if(strlen($captcha_word) > 0 && strlen($captcha_code) > 0) {
				if(!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
					$arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTCHA_WRONG");
			} else $arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTHCA_EMPTY");
		}
		if(empty($arResult)) {
			$arFieldss = array();
			$rsSites = CSite::GetByID(SITE_ID);
			$arSite = $rsSites->Fetch();
			if($arParams["USE_IU_PAT"] == "Y") {
				if($arParams['USE_ATTACH'] == "Y" && empty($arResult["ERROR_MESSAGE"])) {
					if(!preg_match('#^/upload(.*)$#', $arParams["FILE_DIR"])) $arParams["FILE_DIR"] = "/upload/tmp";
					if(preg_match('#^/(.*)/$#', $arParams["FILE_DIR"])) $arParams["FILE_DIR"] = substr($arParams["FILE_DIR"], 0, -1);
					preg_match('#^/upload(.*)$#', $arParams["FILE_DIR"], $file_dir);
					$arFiles = array();
					$arTrans_ru = explode(",", GetMessage("MF_RUS"));
					$arTrans_en = explode(",", GetMessage("MF_ENG"));
					$arTrans = array();
					for($pt=0; $pt < count($arTrans_ru); $pt++)
						$arTrans[$arTrans_ru[$pt]] = $arTrans_en[$pt];
					foreach($arParams['NEW_EXT_FIELDS'] as $ar_name => $ar_value) {
						if($ar_value[3] !== 0 && empty($arResult["ERROR_MESSAGE"])) {
							$arFile = $_FILES["file_".$ar_name];
							if(strlen($arFile["name"]) > 0) {
								if(intval($arFile["size"]) <= ($arParams["MAX_SIZE_FILE"] * 1000)) {
									if(preg_match('#^['.GetMessage("MF_RUSSIAN_REG").'a-z0-9 [\](){}&=+$;%\#@~!\'^_-]+\.('.implode("|", $arParams["FILE_FORMAT"]).')$#iu', $arFile["name"])) {
										$arFile["del"] = "Y";
										$arFile["MODULE_ID"] = "main";
										CheckDirPath($_SERVER["DOCUMENT_ROOT"].$arParams["FILE_DIR"]);
										$fid = CFile::SaveFile($arFile, $file_dir[1]);
										if(intval($fid) > 0) {
											$rsFile = CFile::GetByID($fid);
											$arFil = $rsFile->Fetch();
											preg_match('#^'.$arParams["FILE_DIR"].'(.*)$#', "/upload".$arFil["SUBDIR"], $ar_file_new);
											$file_new = $ar_file_new[1].'/'.strtr($arFil["ORIGINAL_NAME"], $arTrans);
											rename($_SERVER['DOCUMENT_ROOT']."/upload".$arFil["SUBDIR"].'/'.$arFil["FILE_NAME"], $_SERVER['DOCUMENT_ROOT'].$arParams["FILE_DIR"].$file_new);
											$arFiles[] = $file_new.'*'.$fid;
											$arParams['NEW_EXT_FIELDS'][$ar_name][3] = $arParams["FILE_DIR"].$file_new;
										}
									} elseif(preg_match('#^['.GetMessage("MF_RUSSIAN_REG").'a-z0-9 [\](){}&=+$;%\#@~!\'^_-]+\.(.+)$#iu', $arFile["name"]))
										$arResult["ERROR_MESSAGE"][] = GetMessage("MF_FILE_NAME_FORMAT_ERR")."&#58; ".$arFile["name"];
									else
										$arResult["ERROR_MESSAGE"][] = GetMessage("MF_FILE_NAME_ERR")."&#58; ".$arFile["name"];
								} else $arResult["ERROR_MESSAGE"][] = GetMessage("MF_FILE_SIZE_ERR")." ".$arParams["MAX_SIZE_FILE"].GetMessage("MF_FILE_SIZE_2_ERR").".";
							}
						}
					}
					$arFieldss["DIR_FILE"] = $file_dir[1];
					$arFieldss["AR_FILE"] = implode(",", $arFiles);
					$arFieldss["DEL_FILE"] = $arParams['SAVE_FILE'] == "Y" ? "N" : "Y";
				}
				$theme_let = strlen(trim($arParams["EM_THEME"])) <= 0 ? GetMessage("MF_ET_THEME_DEF") : trim($arParams["EM_THEME"]);
				if($field_for_theme !== "iu_none" && isset($_POST["custom"][$field_for_theme]))
					$theme_let = str_replace('#THEME#', (strlen(trim($_POST["custom"][$field_for_theme])) <= 0 ? GetMessage("MF_ET_THEME_NONE") : htmlspecialcharsEx($_POST["custom"][$field_for_theme])), $theme_let);
				$theme_let = str_replace('#SITE#',$arSite["NAME"],$theme_let);
				$event_id = "IU_FEEDBACK_FORM";
				$arFilter = array(
					"TYPE_ID" => $event_id,
					"LID"     => $lang_par,
				);
				$rsET = CEventType::GetList($arFilter);
				if (!($rsET->Fetch())) {
					$arFields = array(
						"EVENT_NAME"  => $event_id,
						"NAME"        => GetMessage("MF_ET_NAME"),
						"SITE_ID"     => $lang_par,
						"DESCRIPTION" => "#EMAIL_FROM# - ".GetMessage("MF_ET_EMAIL_FROM").
							"\n#EMAIL_TO# - ".GetMessage("MF_ET_EMAIL_TO").
							"\n#TEXT# - ".GetMessage("MF_ET_TEXT").
							"\n#E_THEME# - ".GetMessage("MF_ET_THEME").
							"\n#DIR_FILE# - ".GetMessage("MF_ET_DIR_FILE").
							"\n#AR_FILE# - ".GetMessage("MF_ET_AR_FILE").
							"\n#DEL_FILE# - ".GetMessage("MF_ET_DEL_FILE")
						);
					$obEventType = new CEventType;
					$obEventType->Add($arFields);
				}
				$arFilter = array(
					"TYPE_ID" => $event_id,
					"ACTIVE"  => "Y",
					"SITE_ID" => SITE_ID,
				);
				$rsMess = CEventMessage::GetList($by="id", $order="desc", $arFilter);
				if ($arMess = $rsMess->Fetch())
					$template_id = $arMess["ID"];
				else {
					$arFields = array(
						"ACTIVE"     => "Y",
						"EVENT_NAME" => $event_id,
						"LID"        => SITE_ID,
						"EMAIL_FROM" => '#EMAIL_FROM#',
						"EMAIL_TO"   => '#EMAIL_TO#',
						"SUBJECT"    => '#E_THEME#',
						"BODY_TYPE"  => 'text',
						"MESSAGE"    => '#TEXT#',
					);
					$obTemplate = new CEventMessage;
					$template_id = $obTemplate->Add($arFields);
				}
				$Email = array();
				foreach($arParams["EMAIL_TO"] as $mail_val)
					if (check_email($mail_val))
						$Email[] = trim($mail_val);
				$arFieldss["EMAIL_FROM"] = ($field_for_email !== "iu_none" && isset($arParams['USE_EMAIL_USER']) && $arParams['USE_EMAIL_USER'] == "Y") ? $_POST["custom"][$field_for_email] : COption::GetOptionString("main", "email_from");
				if($arParams["COPY_LETTER"] == "Y" && $field_for_email !== "iu_none" && $_POST["copy_letter"] == "Y") $copy_email = $_POST["custom"][$field_for_email];
				$arFieldss["EMAIL_TO"] = empty($Email) ? (isset($copy_email) ? $copy_email : COption::GetOptionString("main", "email_from")) : implode(",",$Email);
				if(isset($copy_email) && $arFieldss["EMAIL_TO"] !== $copy_email) $arFieldss["EMAIL_TO"] .= ",".$copy_email;
				$arFieldss["E_THEME"] = $theme_let;
				$arFieldss["TEXT"] = '';
				foreach($_POST["custom"] as $i => $custom_field)
					if($arParams['NEW_EXT_FIELDS'][$i][3] === 0)
						$arFieldss["TEXT"] .= "\n\n".$arParams['NEW_EXT_FIELDS'][$i][0].":\n".$custom_field;
			} else {
				$event_id = $arParams["EVENT_TYPE_ID"];
				$template_id = $arParams["EVENT_MESSAGE_ID"];
				foreach ($arParams["NEW_EXT_FIELDS"] as $fd_nam => $fd_arr)
					$arFieldss[$fd_nam] = $_POST["custom"][$fd_nam];
			}
			if ($arParams["USE_IU_IB"] == "Y") {
				CModule::IncludeModule("iblock");
				$ib_type = 'iu_feedback';
				$name_iblock = isset($arParams['IBLOCK_NAME']) ?
					(strlen(trim($arParams["IBLOCK_NAME"])) <= 0 ? GetMessage("MF_IBLOCK_NAME_DEF") : trim($arParams["IBLOCK_NAME"])) :
					(isset($theme_let) ? $theme_let : GetMessage("MF_IBLOCK_NAME_DEF"));
				if ($arParams["USE_IU_IBC"] == "N") {
					$ib_type = $arParams['IB_TYPE'];
					$ib_id = $arParams['IB_IB'];
				} else {
					$rsIB_T = CIBlockType::GetList(
						array("ID"=>"ASC"),
						array("ID"=>$ib_type)
					);
					if (!($rsIB_T->Fetch())) {
						$arFields = array(
							'ID'       => $ib_type,
							'SECTIONS' => 'Y',
							'SORT'     => 999,
							'LANG'     => array(
								'ru' => array(
									'NAME' => GetMessage("MF_CIBT_NAME_RU")
								),
								'en' => array(
									'NAME' => GetMessage("MF_CIBT_NAME_EN")
								)
							)
						);
						$obBlocktype = new CIBlockType;
						$DB->StartTransaction();
						$res = $obBlocktype->Add($arFields);
						if(!$res) $DB->Rollback();
						else $DB->Commit();
					}
					$res = CIBlock::GetList(
						array("ID" => "ASC"), 
						array(
							"TYPE" => $ib_type, 
							"SITE_ID" => SITE_ID,
							"CODE" => md5($name_iblock),
						)
					);
					if ($arIB = $res->Fetch())
						$ib_id = $arIB["ID"];
					else {
						$arFields = array(
							"SITE_ID"        => array(SITE_ID),
							"IBLOCK_TYPE_ID" => $ib_type,
							"NAME"           => $name_iblock,
							"CODE"           => md5($name_iblock),
							"ACTIVE"         => "Y",
							"RSS_ACTIVE"     => "N",
							"INDEX_ELEMENT"  => "N",
							"WORKFLOW"       => "Y",
						);
						$ib = new CIBlock;
						$ib_id = $ib->Add($arFields);
						if(!$ib_id) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_ERROR_CREATE_IB");
					}
				}
				if(empty($arResult["ERROR_MESSAGE"])) {
					if($arParams['IB_PARAM'] == 'Y') {
						$ar_name_prop = array();
						$ar_prop = array();
						$max_ind_prop = 0;
						$properties = CIBlockProperty::GetList(
							array("id"=>"asc"),
							array("IBLOCK_ID"=>$ib_id)
						);
						while($prop_fields = $properties->GetNext()) {
							$ar_prop[$prop_fields["NAME"]] = $prop_fields["CODE"];
							$ar_name_prop[] = $prop_fields["NAME"];
							if(preg_match('/^iu_(\d+)$/',$prop_fields["CODE"],$matches)) {
								if(intval($matches[1])>$max_ind_prop)
									$max_ind_prop = $matches[1];
							}
						}
						foreach($arParams['NEW_EXT_FIELDS'] as $f_code => $ar_f) {
							$IBpar_code = '';
							if($f_code !== $IB_DET && $f_code !== $IB_ANONS && ($ar_f[3] === 0 || ($ar_f[3] !== 0 && $arParams['SAVE_FILE'] == "Y"))) {
								if(!in_array($ar_f[0],$ar_name_prop)) {
									$max_ind_prop++;
									$arFields = Array(
										"IBLOCK_ID"     => $ib_id,
										"NAME"          => $ar_f[0],
										"CODE"          => ("iu_".$max_ind_prop),
										"PROPERTY_TYPE" => "S",
										"ACTIVE"        => "Y",
										"SORT"          => "100",
										"IS_REQUIRED"   => "N",
									);
									$ibp = new CIBlockProperty;
									$ibp->Add($arFields);
									$IBpar_code = 'iu_'.$max_ind_prop;
								} else 
									$IBpar_code = $ar_prop[$ar_f[0]];
							}
							$arParams['NEW_EXT_FIELDS'][$f_code][] = $IBpar_code;
						}
					}
					$EIB_name = (strlen($_POST["custom"][$IB_NAME_EL])>80)?(mb_substr($_POST["custom"][$IB_NAME_EL],0,77,'utf8').'..'):$_POST["custom"][$IB_NAME_EL];
					$el = new CIBlockElement;
					$arFields = array(
						"IBLOCK_ID" => $ib_id,
						"NAME"      => (($IB_NAME_EL == 'iu_none' || strlen($EIB_name) <= 0)?GetMessage("MF_LETTER"):$EIB_name),
						"ACTIVE"    => $arParams['IB_ACT'],
					);
					$ar_det_anons = array();
					if($IB_DET !== 'iu_none') {
						$arFields["DETAIL_TEXT"] = $_POST["custom"][$IB_DET];
						$arFields["DETAIL_TEXT_TYPE"] = "text";
						$ar_det_anons[] = $IB_DET;
					}
					if($IB_ANONS !== 'iu_none') {
						$arFields["PREVIEW_TEXT"] = $_POST["custom"][$IB_ANONS];
						$arFields["PREVIEW_TEXT_TYPE"] = "text";
						$ar_det_anons[] = $IB_ANONS;
					}
					if($arParams['IB_PARAM'] == 'Y') {
						$PROP = array();
						foreach($_POST["custom"] as $i => $custom_field)
							if(!in_array($i,$ar_det_anons))
								$PROP[$arParams['NEW_EXT_FIELDS'][$i][4]] = $custom_field;
						foreach($arField_files as $field_name_file)
							$PROP[$arParams['NEW_EXT_FIELDS'][$field_name_file][4]] = $arParams['NEW_EXT_FIELDS'][$field_name_file][3] === 1 ? "" : $arParams['NEW_EXT_FIELDS'][$field_name_file][3];
						$arFields["PROPERTY_VALUES"] = $PROP;
					}
					$IBEid = $el->Add($arFields);
					if($arParams['WRIT_A'] == 'Y') {
						$IBE_href = 'http://'.$_SERVER["SERVER_NAME"].'/bitrix/admin/iblock_element_edit.php?ID='.$IBEid.'&type='.$ib_type.'&lang='.$lang_par.'&IBLOCK_ID='.$ib_id;
						$arFieldss["TEXT"] .= "\n\n".GetMessage("MF_A_TO").":\n".$IBE_href;
					}
				}
			}
			if(empty($arResult["ERROR_MESSAGE"])) {
				if($arParams["USE_IU_PAT"] == "Y") {
					$after_text = trim($arParams["AFTER_TEXT"]);
					if(strlen($after_text) > 0)
						$arFieldss["TEXT"] .= "\n\n".str_replace('#SITE#',$arSite["NAME"],$after_text);
				}
				if(strlen($template_id) > 0)
					CEvent::Send($event_id, SITE_ID, $arFieldss, "N", $template_id);
				else
					CEvent::Send($event_id, SITE_ID, $arFieldss, "N");
				if($arParams["USE_IU_PAT"] == "Y") {
					if($field_for_name !== "iu_none" && in_array($field_for_name,$arParams["NEW_EXT_FIELDS"]) && strlen($_POST["custom"][$field_for_name]) > 1)
						$_SESSION["MF_NAME"] = htmlspecialcharsEx($_POST["custom"][$field_for_name]);
					if($field_for_email !== "iu_none" && in_array($field_for_email,$arParams["NEW_EXT_FIELDS"]) && strlen($_POST["custom"][$field_for_email]) > 1)
						$_SESSION["MF_EMAIL"] = htmlspecialcharsEx($_POST["custom"][$field_for_email]);
				}
				LocalRedirect($APPLICATION->GetCurPageParam("success=Y", Array("success")));
			} elseif($arParams['USE_ATTACH'] == "Y") {
				foreach($arFiles as $file_name) {
					$ar_file_name = explode('*', $file_name);
					if(preg_match('#^/(.+)/(.+)$#i', $ar_file_name[0], $arRes_f)) {
						DeleteDirFilesEx("/upload".$arFieldss["DIR_FILE"]."/".$arRes_f[1]);
						CFile::Delete($ar_file_name[1]);
					}
				}
			}
		}
		foreach($_POST["custom"] as $i => $custom_field)
			$arResult["custom_$i"] = htmlspecialcharsEx($custom_field);
	} else $arResult["ERROR_MESSAGE"][] = GetMessage("MF_SESS_EXP");
} elseif ($_REQUEST["success"] == "Y")
    $arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];
if(empty($arResult["ERROR_MESSAGE"])) {
	if($field_for_name !== "iu_none") {
		if($USER->IsAuthorized())
			$arResult["custom_".$field_for_name] = htmlspecialcharsEx($USER->GetFullName());
		elseif(isset($_SESSION["MF_NAME"]))
			$arResult["custom_".$field_for_name] = htmlspecialcharsEx($_SESSION["MF_NAME"]);
	}
	if($field_for_email !== "iu_none") {
		if($USER->IsAuthorized())
			$arResult["custom_".$field_for_email] = htmlspecialcharsEx($USER->GetEmail());
		elseif(isset($_SESSION["MF_EMAIL"]))
			$arResult["custom_".$field_for_email] = htmlspecialcharsEx($_SESSION["MF_EMAIL"]);
	}
}
if($arParams["USE_CAPTCHA"] == "Y")
	$arResult["capCode"] =  htmlspecialchars($APPLICATION->CaptchaGetCode());

$this->IncludeComponentTemplate();
?>