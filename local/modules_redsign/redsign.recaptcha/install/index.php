<?
global $MESS;
IncludeModuleLangFile(__FILE__);

Class redsign_recaptcha extends CModule
{
    var $MODULE_ID = "redsign.recaptcha";
    var $MODULE_NAME;
	var $MODULE_VERSION;
	var $MODULE_DESCRIPTION;
	var $MODULE_VERSION_DATE;
	var $MODULE_GROUP_RIGHTS;

	function redsign_recaptcha(){
		include(__DIR__.'/version.php');
		$this->PARTNER_NAME				= GetMessage('RSGC_PART_NAME');
		$this->PARTNER_URI				= GetMessage("RSGC_PART_URL");
		$this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = "N";
		$this->MODULE_DESCRIPTION		= GetMessage("RSGC_DESCRIPTION");
		$this->MODULE_VERSION			= $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE		= $arModuleVersion['VERSION_DATE'];
		$this->MODULE_GROUP_RIGHTS		= "N";
		$this->MODULE_NAME				= GetMessage('RSGC_MOD_NAME');
		$this->MODULE_ID				 = "redsign.recaptcha"; 
	}
	
	function InstallDB(){
		
	}
	
    function DoInstall(){
        global $DB, $APPLICATION;
        redsign_recaptcha::InstallDB();
		//install files
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.recaptcha/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/redsign.recaptcha", true, true);
		//Install events
		RegisterModuleDependences("main", "OnBeforeProlog", "redsign.recaptcha", "redsign_recaptcha", "onBeforeProlog");
		RegisterModuleDependences("main", "OnPageStart", "redsign.recaptcha", "redsign_recaptcha", "OnPageStart");
		//Register
		RegisterModule("redsign.recaptcha");
	}
	
	function DoUninstall(){
		//uninstall Events
		unRegisterModuleDependences("main", "OnBeforeProlog", "redsign.recaptcha", "redsign_recaptcha", "onBeforeProlog");
		unRegisterModuleDependences("main", "OnPageStart", "redsign.recaptcha", "redsign_recaptcha", "OnPageStart");
		//uninstall files
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.recaptcha/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/redsign.recaptcha");
		try{
			rmdir($_SERVER["DOCUMENT_ROOT"]."/bitrix/images/redsign.recaptcha");
		}catch(Exception $e){
		
		}
		//unregister
		unRegisterModule("redsign.recaptcha");
	}
	
	
	function getWaitedCode($sid){
		global $DB;
		$res = $DB->Query("SELECT CODE FROM b_captcha WHERE ID = '".$DB->ForSQL($sid,32)."' ");
		if (!$ar = $res->Fetch()){
			return "";
		}else{
			return $ar['CODE'];
		}		
	}
	
	public function getSettings($LID){
		$opt	= COption::GetOptionString('redsign.recaptcha', "settings", false, $LID);
		if($opt != ""){
			$ret			= unserialize($opt);
			return $ret;
		}else{
			return false;
		}
	}
	
	public function searchCaptchaSid($arRequest){
		$ret	= '';
		foreach($arRequest as $key=>$val){
			if(is_array($val)){
				$ret = redsign_recaptcha::searchCaptchaSid($val);
			}else{ 
				if (preg_match('/captcha_sid/', $key) > 0) $ret	= $val;
				if (preg_match('/captcha_code/', $key) > 0) $ret = $val;
			}	
		}
		return $ret;
	}
	
	public function setNativeCaptchaCode($arRequest, $CODE){
		$arRet	= array();
		foreach($arRequest as $key=>$val){
			if(is_array($val)){
				$arRet[$key]	= redsign_recaptcha::setNativeCaptchaCode($val, $CODE);
			}else{ 
				if (preg_match('/captcha_word/', $key) > 0){
					$arRet[$key]	= $CODE;
				}else{
					$arRet[$key]	= $val;
				}				
			}	
		}
		return $arRet;
	}
	
	public function checkRequests($LID){
		global $DBType, $DB, $MESS, $APPLICATION, $SESSION;
			
		$SETTINGS		= redsign_recaptcha::getSettings($LID);	
		$CAPTCHA_SID	= redsign_recaptcha::searchCaptchaSid($_REQUEST);

		if($CAPTCHA_SID != ''){
			$SESSION['RSRECAPTCHA'][$CAPTCHA_SID]	= $_REQUEST['g-recaptcha-response'];
		}
		if($CAPTCHA_SID != '' && !isset($_REQUEST['g-recaptcha-response'])){
			$_REQUEST['g-recaptcha-response']	= $SESSION['RSRECAPTCHA'][$CAPTCHA_SID];
		}

		if (isset($_REQUEST['g-recaptcha-response'])){
			$curl	= false;
			if ($curl){
				$myCurl = curl_init();
				curl_setopt_array($myCurl, array(
					CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => http_build_query(array(
						'secret'=>$SETTINGS['secretkey'],
						'response'=>$_REQUEST["g-recaptcha-response"],
						'remoteip'=>$_SERVER['REMOTE_ADDR'],
						))
				));
				$response = curl_exec($myCurl);
				curl_close($myCurl);
			}else{
				$response = QueryGetData(
					"www.google.com", 
					443, 
					"/recaptcha/api/siteverify", 
					"secret=".$SETTINGS['secretkey']."&response=".$_REQUEST["g-recaptcha-response"]."&remoteip=".$_SERVER['REMOTE_ADDR'], 
					$error_number, 
					$error_text,
					"POST",
					"ssl://"
					);
				if (strlen($response)<=0)
				{
				   return false; //google error!
				}
			}			
			$oResp = json_decode($response);

			if ($oResp->success == "true"){
                //native caption insertion
                $CAPTCHA_CODE	= redsign_recaptcha::getWaitedCode($CAPTCHA_SID);
				
				if($_SERVER['REQUEST_METHOD'] == "POST"){
					$_POST	= redsign_recaptcha::setNativeCaptchaCode($_POST, $CAPTCHA_CODE);
				}else{	
					$_GET	= redsign_recaptcha::setNativeCaptchaCode($_POST, $CAPTCHA_CODE);
				}	
				$_REQUEST	= redsign_recaptcha::setNativeCaptchaCode($_POST, $CAPTCHA_CODE);
			}				
		}
	}	
	
	function onBeforeProlog(){
		global $APPLICATION, $USER;
		$SETTINGS	= redsign_recaptcha::getSettings(SITE_ID);
		
        //-- update (b) --
        if(isset($SETTINGS['use_hided_class']) && $SETTINGS['use_hided_class']=='on'){
            $use_hc_js    = 'true';
        }else{
            $use_hc_js    = 'false';
        }
        //-- update (e) --
        
		//** connect script for adding captcha in forms
		$script = '
			<script type="text/javascript" defer="defer">
			var FORMS		= new Object();
            var IDS         = [];
			var captchaOK	= false;
			//if(BX){
				var RSGC_init = function(){

					var frms =	BX.findChildren(BX(document), {"tag":"FORM"}, true);
					
					//clear ID\'s
					var divs_4_clean	= BX.findChildren(document, {"tag":"div","class":"g-recaptcha"}, true);
					
					[].forEach.call(divs_4_clean, function(dfc){
						if (dfc.id.indexOf("capt_")+1 > 0){
							BX(dfc).removeAttribute("id");
						}	
					});			
					
					//find form
					for(var i = 0; i<= frms.length - 1; i++){
						var inner	= frms[i].innerHTML;	
						var find	= undefined;

						//if contains keywords, then found
						if((inner.indexOf("captcha") + 1 > 0) && (inner.indexOf("g-recaptcha") == -1) && (inner.indexOf("re-captcha") == -1)){
							var frm_childs	= BX.findChildren(frms[i],{},true);
							var ins_before	= false;
							
							//find CAPTCHA_WORD input and fill it
							var cw_el	= BX.findChild(frms[i], {"tag":"INPUT","attribute":{"name":"captcha_word"}}, true);
							if(typeof(cw_el) != "undefined" && cw_el != null){
								//console.log("CAPTCHA WORD"); console.log(typeof(cw_el));
								cw_el.setAttribute("value", "EMPTY");
							}
							
							//find CPATCHA image and hide it if it hasnt proper class
							var imgs	= BX.findChildren(frms[i], {"tag":"IMG"}, true);
							[].forEach.call(imgs, function(img){
								if (img.src.indexOf("captcha") + 1 > 0){
									BX.hide(BX(img));
								}	
							});
							
							//find capt elements
							[].forEach.call(frm_childs, function(frm_child){
								if((frm_child.nodeName != "div") && (frm_child.nodeName != "form")){
								
									//if contains keywords, then found
									var fc_Name		= frm_child.getAttribute("name")?frm_child.getAttribute("name"):"";
									var fc_Class	= typeof(frm_child.className)=="string"?frm_child.className:"";
									if(((fc_Class.indexOf("captcha") + 1 > 0) || (fc_Name.indexOf("captcha") + 1 > 0)) && (frm_child.className.indexOf("recaptcha") == -1)){
										BX.hide(frm_child);
										if(!find){
											//marking element to insert before
											ins_before	= frm_child;
										}
										find	= true;
									}	
								}
							});
							
							//insertion
							if(!!ins_before){
                                //--- update (b) ---
                                var classes_to_catch    = ins_before.className;
                                var addon_classes       = "";
                                [].forEach.call(classes_to_catch.split(" "), function(item){
                                    if(item.indexOf("captcha")+1 == 0){
                                        addon_classes += " " + item;
                                    }
                                });
                                var use_hided_classes = '.$use_hc_js.';
                                if(use_hided_classes){
                                    new_class   = "g-recaptcha" + addon_classes;
                                }else{
                                    new_class   = "g-recaptcha";
                                }
                                //--- update (e) ---
                            
								var dt		= {"tag":"DIV","style":{"margin-top":"5px", "margin-bottom":"10px"},"props":{"className":new_class,"id":"capt_"+i.toString()}};
								try{
									var id			= "capt_"+i.toString();
									var isElement	= BX.findChildren(frms[i], {"attribute":{"id":id}}, true);
									if (!isElement.length){									
										var parent	= BX.findParent(ins_before);										
										var nOb		= BX.create(dt);
										parent.insertBefore(nOb, ins_before);										
										FORMS[Object.keys(FORMS).length] = "capt_"+i.toString();
									}	
								}catch(e){
									//console.log(e);
								};
							}
						}
					};
				};
			//}
			</script>
		';
		
		if(!$USER->IsAuthorized() && $SETTINGS['on'] == 'on' && strpos($_SERVER['REQUEST_URI'], '/bitrix/admin/')===false){
			$APPLICATION->addHeadString($script);
			$APPLICATION->addHeadString("<script type='text/javascript' defer='defer'>
				var verifyCallback = function(response) {
					captchaOK	= true;
				};

				var onloadCallback = function() {
					var keys	= Object.keys(FORMS);
					keys.forEach(function(item, i, keys){
                        var plholder	= document.getElementById(FORMS[i]);
                        if (!!plholder){
                            if (plholder.innerHTML == '' && typeof(grecaptcha) != 'undefined' ){						
                                IDS[IDS.length] = grecaptcha.render(FORMS[i], {
                                  'sitekey' : '".$SETTINGS['key']."',
                                  'callback' : verifyCallback,
                                  'theme' : '".$SETTINGS['theme']."'
                                });
                            }
                        }
					});
				}; 
                
                function resizeOnAjax(){
                    if (window.jQuery){
                        //for pro serie (b)                       
                        if(typeof(RSGOPRO_SetHeight) !== undefined){
                           jQuery('div.fancybox-skin div.someform').css('max-width', '400px');
                        }//for pro serie (e)    
                        setTimeout(function(){
                            jQuery(window).trigger('resize');
                        }, 50);
                        setTimeout(function(){
                            jQuery(window).trigger('resize');
                        }, 100);
                    }  
                }
				</script>");
			
			
			$APPLICATION->addHeadString("<script type='text/javascript' defer='defer'>
				if (window.frameCacheVars !== undefined)
				{
					// for composite
					BX.addCustomEvent('onFrameDataReceived' , function(json) {
						RSGC_init();
						onloadCallback();
					});
				}
				else
				{
					// for all
					BX.ready(function() {
						RSGC_init();
						onloadCallback();
					});
				}	
				
				//for bx ajax
				BX.addCustomEvent('onAjaxSuccess' , function(json) {
					setTimeout(function(){
						RSGC_init();
						onloadCallback();
					}, 50);	
				});
					
				//for jq ajax
				addEvent(window, 'load', function(){
					if (window.jQuery){
						jQuery(document).bind('ajaxStop', function(){
							setTimeout(function(){
								RSGC_init();
								onloadCallback();
                                resizeOnAjax(); //only for PRO serie
							}, 50);	
						});	
					}
				});

				function RSGC_reset(){
                    for(frm in FORMS) if (FORMS.hasOwnProperty(frm)) {
                        grecaptcha.reset(IDS[FORMS[frm]]); 
                    }
                }

				addEvent(document, 'DOMNodeInserted', function (event) {
					if(typeof(event.target.getElementsByTagName) != 'undefined'){
						var isForms	= event.target.getElementsByTagName('form');
						if(isForms.length > 0){
							RSGC_reset();
							onloadCallback();
						}
					}
				}, false);

                
                function addEvent(element, eventName, fn) {
                    if (element.addEventListener)
                        element.addEventListener(eventName, fn, false);
                    else if (element.attachEvent)
                        element.attachEvent('on' + eventName, fn);
                }
				</script>");
				
			$APPLICATION->addHeadString('<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"></script>'); // async defer
		}
		
		redsign_recaptcha::checkRequests(SITE_ID);

	}

	function OnPageStart(){
		redsign_recaptcha::checkRequests(SITE_ID);
	}
}	