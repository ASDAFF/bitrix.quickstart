<?php
ini_set('zend.ze1_compatibility_mode', 0);
CModule::IncludeModule('iblock');
IncludeModuleLangFile(__FILE__);

class CExportVacancies{
    
	public static $VACANCIES_IBLOCK_ID;
	public static $phone; 
	public static $email;
	public static $host;
	public static $DESCRIPTION;
	public static $AGENCY_NAME;
	private $msg='';

	function _localEncode($str)
	{if (!(LANG_CHARSET=="UTF-8"))
		$str_out = iconv(LANG_CHARSET,'UTF-8',$str);
		return $str_out;
	}
	
	public function getMessage(){
		return $this->msg;
	}

	public function export_for_yandex(){
		 
		$file='vacancies.yml';
	
		$arSelect = Array("ID", 
							"NAME", 
							"DATE_CREATE", 
							"DATE_ACTIVE_FROM", 
							"DETAIL_PAGE_URL", 
							"PROPERTY_contact", 
							"DETAIL_TEXT",
							"PROPERTY_employment",
							"PROPERTY_shedule",
							"PROPERTY_salary");
		$arFilter = Array(
			"IBLOCK_ID" 	=> self::$VACANCIES_IBLOCK_ID, 
			"ACTIVE_DATE" 	=> "Y", 
			"ACTIVE" 		=> "Y",
			//"SECTION_GLOBAL_ACTIVE" => "Y"
			
		);
						
		$time=date("Y-m-d H:i:s \G\M\TO");
		$imp = new DOMImplementation;
		$dom = $imp->createDocument("", "");
		$dom->encoding = 'UTF-8';
		$dom->formatOutput = true;
		$source = $dom->createElement('source');  $dom->appendChild($source); 		
		$attr_time=$dom->createAttribute('creation-time'); $source->setAttributeNode($attr_time); 
		$attr_time->appendChild($dom->createTextNode($time));
		$attr_host=$dom->createAttribute('host'); $source->setAttributeNode($attr_host); 
		$attr_host->appendChild($dom->createTextNode(self::$host));		
		$xml_vacancies=$dom->createElement('vacancies');  $source->appendChild($xml_vacancies);
		
	    $dbElement = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		while($arFields = $dbElement->GetNext()){
		
			$this->createVacancies($dom, $xml_vacancies, $arFields);
		} 
		$DIRNAME = $_SERVER['DOCUMENT_ROOT']."/_export";
		{if (!(is_dir($DIRNAME)))
			mkdir($DIRNAME, 0700);
			}
		$dom->save($_SERVER['DOCUMENT_ROOT']."/_export/".$file); // сохранить в файл *.xml
		if(file_exists($_SERVER['DOCUMENT_ROOT']."/_export/".$file)){
			$this->msg.='<strong style="color:green">'.GetMessage("VACANCY_EXPORT_SUCCESS").'<br>
				<a href="/_export/'.$file.'">'.$file.'</a><br></strong>';
		}else{ 
			$this->msg.="<strong style=\"color:red\">".GetMessage("VACANCY_EXPORT_ERROR")."</strong>";  
		}
	}
	
	public function createVacancies(&$dom, &$xml_vacancies, $arFields){
	     	
		// создать элемент vacancy и добавить его к vacancies
	    $xml_vacancy=$dom->createElement('vacancy');  $xml_vacancies->appendChild($xml_vacancy);
	    			    	    
			// создать элемент url и добавить его к vacancy
		    $xml_url=$dom->createElement('url');  $xml_vacancy->appendChild($xml_url);
		    //создать узел значения и присоединить его к элементу url
			$str_out = self::_localEncode($arFields['DETAIL_PAGE_URL']);
			$xml_url->appendChild($dom->createTextNode(htmlspecialchars($_SERVER["HTTP_HOST"].$str_out, ENT_QUOTES)));
						
			$creation_date=(empty($arFields['DATE_ACTIVE_FROM'])? $arFields['DATE_CREATE'] : $arFields['DATE_ACTIVE_FROM']);	
			$creation_date=ConvertDateTime($creation_date, "YYYY-MM-DD HH:MI:SS").' GMT'.date("O");		
			// создать элемент creation-date и добавить его к vacancy
		    $xml_creation_date=$dom->createElement('creation-date');  $xml_vacancy->appendChild($xml_creation_date);
		    //создать узел значения и присоединить его к элементу creation-date
			$xml_creation_date->appendChild($dom->createTextNode($creation_date));
			//salary
			$xml_salary=$dom->createElement('salary');  $xml_vacancy->appendChild($xml_salary);
			$str_out = self::_localEncode($arFields['PROPERTY_SALARY_VALUE']);
			$xml_salary->appendChild($dom->createTextNode($str_out));
			
		    $rsIBlocks = CIBlockSection::GetList(array('NAME'=>"ASC"), array('IBLOCK_ID'=>self::$VACANCIES_IBLOCK_ID, 'ID'=>$arFields['IBLOCK_SECTION_ID']));
		
			while($arIBlock = $rsIBlocks->Fetch()){
		
				$xml_category=$dom->createElement('category');  $xml_vacancy->appendChild($xml_category);
				$xml_category_industry=$dom->createElement('industry');  $xml_category->appendChild($xml_category_industry);
				
				$str_out = self::_localEncode($arIBlock['NAME']);
				$xml_category_industry->appendChild($dom->createTextNode($str_out));
			}
			// создать элемент job-name и добавить его к vacancy
		    $xml_jobname=$dom->createElement('job-name');  $xml_vacancy->appendChild($xml_jobname);
		    //создать узел значения и присоединить его к элементу job-name
			$str_out = self::_localEncode($arFields['NAME']);
			$xml_jobname->appendChild($dom->createTextNode(htmlspecialchars($str_out, ENT_QUOTES)));
			// занятость и график работы
			if (!empty($arFields['PROPERTY_EMPLOYMENT_VALUE']))
			{
				$xml_employment=$dom->createElement('employment');  $xml_vacancy->appendChild($xml_employment);
				$str_out = self::_localEncode($arFields['PROPERTY_EMPLOYMENT_VALUE']);
		   		$xml_employment->appendChild($dom->createTextNode($str_out));	
			}
			
			if (!empty($arFields['PROPERTY_SHEDULE_VALUE']))
			{
				$xml_shedule=$dom->createElement('schedule');  $xml_vacancy->appendChild($xml_shedule);
				$str_out = self::_localEncode($arFields['PROPERTY_SHEDULE_VALUE']);
		   		$xml_shedule->appendChild($dom->createTextNode($str_out));	
			}
		
			$xml_description=$dom->createElement('description');  $xml_vacancy->appendChild($xml_description);
			$str_out = self::_localEncode($arFields['DETAIL_TEXT']);
			$xml_description->appendChild($dom->createTextNode(htmlspecialchars($str_out,ENT_QUOTES)));	
			$xml_company=$dom->createElement('company');  $xml_vacancy->appendChild($xml_company);
		    
				// создать элемент name и добавить его к company
			    $xml_company_name=$dom->createElement('name');  $xml_company->appendChild($xml_company_name);
			    //создать узел значения и присоединить его к элементу name
				$str_out = self::_localEncode(self::$AGENCY_NAME);
				$xml_company_name->appendChild($dom->createTextNode(htmlspecialchars($str_out, ENT_QUOTES)));
							    
				// создать элемент description и добавить его к company
			    $xml_company_description=$dom->createElement('description');  $xml_company->appendChild($xml_company_description);
			    //создать узел значения и присоединить его к элементу description
				$str_out = self::_localEncode(self::$DESCRIPTION);
				$xml_company_description->appendChild($dom->createTextNode(htmlspecialchars(
				$str_out, ENT_QUOTES)));
				
				// создать элемент logo и добавить его к company
			    $xml_company_logo=$dom->createElement('logo');  $xml_company->appendChild($xml_company_logo);
			    //создать узел значения и присоединить его к элементу logo
				//$xml_company_logo->appendChild($dom->createTextNode(self::$site.'/gfx/logo.png'));
							    
				// создать элемент site и добавить его к company
			    $xml_company_site=$dom->createElement('site');  $xml_company->appendChild($xml_company_site);
			    //создать узел значения и присоединить его к элементу site
				$xml_company_site->appendChild($dom->createTextNode($_SERVER["HTTP_HOST"]));
				

				
				$arUserContact=array(
										'phone'=>'',
										'mail'=>'',
										'name'=>'');
										
				$rsUser = CUser::GetByID($arFields['PROPERTY_CONTACT_VALUE']);
				if($arUser = $rsUser->GetNext())
				{$contact_name = self::_localEncode($arUser['NAME']);
				$contact_name2 = self::_localEncode($arUser['LAST_NAME']);
						$arUserContact=array(
										'phone'=>$arUser['WORK_PHONE'],
										'mail'=>$arUser['EMAIL'],
										'name'=>trim($contact_name . " " . $contact_name2));
				}
				//общий email
				if (!empty(self::$email))
				{
					$xml_company_email=$dom->createElement('email');  $xml_company->appendChild($xml_company_email);
					$xml_company_email->appendChild($dom->createTextNode(self::$email));
				}
				if (!empty($arUserContact['mail']))
						{		
							
							$xml_company_email=$dom->createElement('email');  $xml_company->appendChild($xml_company_email);
							$xml_company_email->appendChild($dom->createTextNode($arUserContact['mail']));
						}
				if (!empty(self::$phone))
				{
					$xml_company_phone=$dom->createElement('phone');  $xml_company->appendChild($xml_company_phone);
					$xml_company_phone->appendChild($dom->createTextNode(self::$phone));
				}
				if (!empty($arUserContact['phone']))
				{
					$xml_company_phone=$dom->createElement('phone');  $xml_company->appendChild($xml_company_phone);
					$xml_company_phone->appendChild($dom->createTextNode($arUserContact['phone']));
				}
				if (!empty($arUserContact['name']))
				{$contact_name = self::_localEncode($arUserContact['name']);
					$xml_company_contactname=$dom->createElement('contact-name');  $xml_company->appendChild($xml_company_contactname);
					$xml_company_contactname->appendChild($dom->createTextNode($contact_name));
				}
				// создать элемент hr-agency и добавить его к company
			    $xml_company_hragency=$dom->createElement('hr-agency');  $xml_company->appendChild($xml_company_hragency);
			    //создать узел значения и присоединить его к элементу name
				$xml_company_hragency->appendChild($dom->createTextNode('true'));							    
				}
}
?>