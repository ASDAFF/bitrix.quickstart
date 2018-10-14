<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Main {
	
	protected $params = null;
	protected $filter = array();
	protected $adminList = false;
	protected $adminResult = false;
	
	public function __construct($params) {
		//загрузка языковых сущности
		$entity = $params["ENTITY"];
		Loc::loadMessages($entity::getFilePath());
		
		if(!isset($params["PRIMARY"])) {
			$params["PRIMARY"] = "ID";
		}
		
		if(!isset($params["LANG_CODE"])) {
			$params["LANG_CODE"] = strtoupper(str_replace(array("Table","\\"),array("","_"),$params["ENTITY"]))."_LIST_";
			if(substr($params["LANG_CODE"],0,1)=="_") $params["LANG_CODE"] = substr($params["LANG_CODE"],1);
		}
		
		if(!isset($params["TABLEID"])) $params["TABLEID"] = strtolower(str_replace("_LIST_","",$params["LANG_CODE"]));
		
		//сортировка по умолчанию
		if(!isset($params["ORDER"])){
			
			//костыль с перегоном сортировки из сессии в глобальную переменную
			$oSort = new \CAdminSorting($params["TABLEID"], $params["PRIMARY"], "desc");
			
			//echo'<pre>';print_r($oSort);echo'</pre>';
			
			$by = $GLOBALS['by'];
			$setBy = false;
			if(!empty($params["COLS"])){
				foreach($params["COLS"] as $col){
					if(is_array($col) && $col['id']==$by){
						$setBy = true;
						break;
					}else{
						if($col == $by) {
							$setBy = true;
							break;
						}
					}
				}
			}
			if (!$setBy && !array_key_exists($by, $entity::getEntity()->getFields())) $by = $params["PRIMARY"];
			
			$order = strtolower($GLOBALS['order']);
			$order = $order=='desc'||$order=='asc' ? $order : 'desc';
			$params["ORDER"] = array($by => $order);
		}
		
		$this->setParams($params);
	}
	
	public function getMlifeRowListAdmin($arRes){
		$editFile = $this->getParam("FILE_EDIT").'?'.$this->getParam("PRIMARY").'='.$arRes[$this->getParam("PRIMARY")].'&amp;lang='.LANG;
		
		$row =& $this->getAdminList()->AddRow($arRes[$this->getParam("PRIMARY")], $arRes);
		
		$this->getMlifeRowListAdminCustomRow($row);
		
		$arT = $this->getParam("LIST");
		if(!empty($arT["ACTIONS"])){
			$arActions = array();
			foreach($arT["ACTIONS"] as $val){
				if($val=='delete'){
					$arActions[] = array(
						"ICON" => "delete",
						"TEXT" => Loc::getMessage("MAIN_ADMIN_MENU_DELETE"),
						"TITLE" => Loc::getMessage("MAIN_ADMIN_MENU_DELETE"),
						"ACTION" => "if(confirm('".GetMessageJS("MAIN_ADMIN_MENU_DELETE")."')) ".$this->getAdminList()->ActionDoGroup($arRes[$this->getParam("PRIMARY")], "delete"),
					);
				}elseif($val=='edit'){
					$arActions[] = array(
						"ICON"=>"edit",
						"DEFAULT"=>true,
						"TEXT"=>Loc::getMessage("MAIN_ADMIN_MENU_EDIT"),
						"TITLE"=>Loc::getMessage("MAIN_ADMIN_MENU_EDIT"),
						"ACTION"=>$this->getAdminList()->ActionRedirect($editFile)
						);
				}
			}
			$row->AddActions($arActions);
		}
	}
	
	public function addFilter($filter){
		$this->filter = array_merge($this->filter,$filter);
	}
	
	public function getFilter(){
		return $this->filter;
	}
	
	//получение параметра
	public function getParam($param){
		
		if(!isset($this->params[$param])) return false;
		
		return $this->params[$param];
		
	}
	
	//установка параметров
	public function setParams($params,$key=false){
		
		if($key) {
			$this->params[$key] = $params;
		}else{
			if(is_array($params)) {
				$this->params = $params;
			}else{
				throw new Main\ArgumentException(sprintf(
					'Incorrect parameters, should be an array'
				));
			}
		}
		
	}
	
	public function defaultGetSort(){
		return new \CAdminSorting($this->getParam("TABLEID"), $this->getParam("PRIMARY"), "ASC");
	}
	
	public function getAdminList(){
		if(!$this->adminList)
			$this->adminList = new \CAdminList($this->getParam("TABLEID"), $this->defaultGetSort());
		return $this->adminList;
	}
	
	public function defaultGetActionId($arID,$orderAr=false){
		
		$entity = $this->getParam("ENTITY");
		
		if(!$orderAr) $orderAr = $this->getParam("ORDER");
		
		if($_REQUEST['action_target']=='selected')
		{
			$rsData = $entity::getList(
				array(
					'order' => $orderAr,
					'select' => array($this->getParam("PRIMARY")),
					'filter' => $this->getFilter()
				)
			);
			while($arRes = $rsData->Fetch())
			  $arID[] = $arRes[$this->getParam("PRIMARY")];
		}
		return $arID;
		
	}
	
	public function defaultGetAction($arID){
		
		$entity = $this->getParam("ENTITY");
		
		$act = $this->getParam("CALLBACK_ACTIONS");
		
		if($_REQUEST['action']=="delete") {
			foreach($arID as $ID)
			{
				if(strlen($ID)<=0)
					continue;
					$ID = IntVal($ID);
				
				if(isset($act[$_REQUEST['action']])){
					call_user_func($act[$_REQUEST['action']], $ID);
				}else{
					$res = $entity::delete(array($this->getParam("PRIMARY")=>$ID));
				}
			}
		}else{
			if(isset($act[$_REQUEST['action']])){
				call_user_func($act[$_REQUEST['action']], $arID);
			}
			return false;
		}
		return true;
		
	}
	
	public function checkActions($right){
		
		// обработка одиночных и групповых действий
		if(($arID = $this->getAdminList()->GroupAction()) && $right=="W")
		{
			$arID = $this->defaultGetActionId($arID);
			$resActions = $this->defaultGetAction($arID);
		}
		
		// сохранение отредактированных элементов
		if($this->getAdminList()->EditAction() && $right=="W")
		{
			global $FIELDS;
			
			$act = $this->getParam("CALLBACK_ACTIONS");
			
			// пройдем по списку переданных элементов
			foreach($FIELDS as $ID=>$arFields)
			{
				if(!$this->getAdminList()->IsUpdated($ID))
				continue;

				$ID = IntVal($ID);
				
				$entity = $this->getParam("ENTITY");
				
				foreach($arFields as $key=>$value){
					$obField = $entity::getEntity()->getField($key);
					if($obField instanceof \Bitrix\Main\Entity\DatetimeField){
						$arData[$key]=\Bitrix\Main\Type\DateTime::createFromUserTime($value);
					}else{
						$arData[$key]=$value;
					}
				}

				
				if(isset($act["edit"])){
					call_user_func($act["edit"], $ID, $arData);
				}else{
					$entity::update(array($this->getParam("PRIMARY")=>$ID),$arData);
				}

			}
		}
		
	}
	
	public function getAdminResult(){
		
		if(!$this->adminResult){
			$entity = $this->getParam("ENTITY");
//print_r($this->getAdminList()->GetVisibleHeaderColumns());
			$colsVisible = $this->getAdminList()->GetVisibleHeaderColumns();
			$res = $entity::getList(
				array(
					'select' => $colsVisible,
					'order' => $this->getParam("ORDER"),
					'filter' => $this->getFilter()
				)
			);
			$ob = new \CAdminResult($res, $this->getParam("TABLEID"));
			$ob->NavStart();
			
			$this->adminResult = $ob;
		}
		
		return $this->adminResult;
		
	}
	
	public function setNavText(){
		$this->getAdminList()->NavText($this->getAdminResult()->GetNavPrint(Loc::getMessage($this->getParam("LANG_CODE")."NAV_TEXT")));
	}
	
	public function AddHeaders(){
		
		$entity = $this->getParam("ENTITY");
		$cols = $entity::getEntity()->getFields();
		$colHeaders = array();
		//$arKeys = array();
		if($ar = $this->getParam("COLS")){
			foreach($ar as $valCol){
				if(is_array($valCol)) {
					$colHeaders[] = $valCol;
					//$arKeys[] = $valCol['id'];
				}
			}
			foreach ($cols as $col){
				
				$name = $col->getName();
				$setCol = false;
				
				if(is_array($ar)){
					if(in_array($name,$ar)){
						$setCol = true;
					}
				}else{
					$setCol = true;
				}
				if($setCol){
					$colHeaders[] = array(
						"id" => $name,
						"content" => $col->getTitle(),
						"sort" => $name,
						"default" => true,
					);
					//$arKeys[] = $name;
				}
				
				
			}
		}
		
		$this->getAdminList()->AddHeaders($colHeaders);
		
		/*if(empty($colHeaders)) {
			$this->setParams(array("*"),"SELECT");
		}else{
			$this->setParams($arKeys,"SELECT");
		}*/
		
		/*$visibleHeaderColumns = $this->getAdminList()->GetVisibleHeaderColumns();
		$this->setParams($visibleHeaderColumns,"SELECT");*/
		
	}
	
	public function addFooter(){
		
		$this->getAdminList()->AddFooter(
			array(
				array(
					"title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
					"value" => $this->getAdminResult()->SelectedRowsCount()
				),
				array(
					"counter" => true,
					"title" => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
					"value" => "0"
				),
			)
		);
		
	}
	
	public function AddAdminContextMenu(){
		
		if ($this->getParam("BUTTON_CONTECST") !== false) {
			$arContext['add'] = array(
				'TEXT' => Loc::getMessage($this->getParam("LANG_CODE")."BUTTON_CONTECST_".strtoupper("btn_new")),
				'ICON' => 'btn_new',
				'LINK' => $this->getParam("FILE_EDIT").'?lang='.LANG,
			);
			if (!empty($arAddContext)) {
				$arContext = array_merge($arContext, $arAddContext);
			}
			foreach ($arContext as $k => $v) {
				if (empty($v)) {
					unset($arContext[$k]);
				}
			}
			
			$this->getAdminList()->AddAdminContextMenu($arContext);
		}
		
	}
	
	public function getFindHtml(){
		
		if(!$this->getParam("FIND")) return;
		
		$title = array();
		$entity = $this->getParam("ENTITY");
		$arGroups = array();
		
		$arFields = array();
		
		foreach($this->getParam("FIND") as $val){
			if(is_array($val)){
				if(isset($val["GROUP"])){
					if(!in_array($val["GROUP"], $arGroups)) {
						$arGroups[] = $val["GROUP"];
						if(isset($val["TITLE"])){
							$title[] = $val["TITLE"];
						}else{
							$val["TITLE"] = $entity::getEntity()->getField($val["NAME"])->getTitle();
							$title[] = $val["TITLE"];
						}
					}
					$arFields[$val["GROUP"]][] = $val;
				}else{
					$val = array("NAME"=>$val,"KEY"=>$val);
					$val["TITLE"] = $entity::getEntity()->getField($val["NAME"])->getTitle();
					$title[] = $val["TITLE"];
					$arFields[$val["NAME"]][] = $val;
				}
			}else{
				$title[] = $entity::getEntity()->getField($val)->getTitle();
				$arFields[$val][] = $val;
			}
		}
		
		// создадим объект фильтра
		$oFilter = new \CAdminFilter(
		  $this->getParam("TABLEID")."_filter", $title
		);
		?>
		<form name="find_form" method="get" action="<?echo $GLOBALS["APPLICATION"]->GetCurPage();?>">
		<?$oFilter->Begin();?>
		<?
		$key = -1;
		foreach($arFields as $group=>$val){
		$key++;
			?>
			<tr>
			  <td><?=$title[$key]?>:</td>
			  <td>
				<?foreach($val as $field){
					$type = false;
					if(is_array($field)){
						$val_n = $field["NAME"];
						$row = "find_".$field["KEY"];
						if($field["TYPE"]) $type = $field["TYPE"];
					}else{
						$row = "find_".$field;
						$val_n = $field;
					}
					$obField = $entity::getEntity()->getField($val_n);
					
					if($obField instanceof \Bitrix\Main\Entity\IntegerField){
						if(!$type) $type = "INT";
					}elseif($obField instanceof \Bitrix\Main\Entity\StringField){
						if(!$type) $type = "STRING";
					}elseif($obField instanceof \Bitrix\Main\Entity\BooleanField){
						if(!$type) $type = "BOOL";
					}
					global ${$row};
					if($type=="INT"){
						?>
						<input type="text" name="<?=$row?>" value="<?echo ${$row}?>">
						<?
					}elseif($type=="STRING"){
						?>
						<input type="text" name="<?=$row?>" value="<?echo htmlspecialchars(${$row})?>">
						<?
					}elseif($type=="BOOL"){
						if(is_array($field["VALUES"])){
							$values = $field["VALUES"];
						}else{
							$values = array(
								"reference" => array(
									Loc::getMessage("MLIFE_ADMIN_LIST_SELECT_Y"),
									Loc::getMessage("MLIFE_ADMIN_LIST_SELECT_N"),
								),
								"reference_id" => array(
									"Y",
									"N",
								)
							);
						}
						?>
						<?echo SelectBoxFromArray($row, $values, ${$row}, Loc::getMessage("MLIFE_ADMIN_LIST_SELECT_EMPTY"), "");?>
						<?
					}elseif($type=="LIST"){
						$values = array();
						if(is_array($field["VALUES"])){
							$values = $field["VALUES"];
						}else{
							//TODO - добавить получение полей списка с описания сущности
						}
					?>
						<?echo SelectBoxFromArray($row, $values, ${$row}, Loc::getMessage("MLIFE_ADMIN_LIST_SELECT_EMPTY"), "");?>
					<?
					}elseif($type=="CALENDAR"){
					?>
					<input type="text" name="<?=$row?>" value="<?echo ${$row}?>">
					<?
					}
				}?>
			  </td>
			</tr>
		<?}?>
		<?
		$oFilter->Buttons(array("table_id"=>$this->getParam("TABLEID"),"url"=>$GLOBALS["APPLICATION"]->GetCurPage(),"form"=>"find_form"));
		$oFilter->End();
		?>
		</form>
		<?
		
	}
	
	public function initFilter(){
		
		if(!$this->getParam("FIND")) return;
		
		$title = array();
		
		foreach($this->getParam("FIND") as $val){
			if(is_array($val)){
				$FilterArr[] = "find_".$val["KEY"];
			}else{
				$FilterArr[] = "find_".$val;
			}
		}
		
		$this->getAdminList()->InitFilter($FilterArr);
		
	}
	
	public function checkFilter(){
		
		if(!$this->getParam("FIND")) return;
		
		$title = array();
		$entity = $this->getParam("ENTITY");
		
		$arFilter = array();
		$error = false;
		
		foreach($this->getParam("FIND") as $val){
			$keyFilterAdd = "";
			if(is_array($val)){
				$row = "find_".$val["KEY"];
				global ${$row};
				$tmp = ${$row};
				if(isset($val["FILTER_TYPE"])) $keyFilterAdd = $val["FILTER_TYPE"];
				$validate = false;
				$val_n = $val["NAME"];
			}else{
				$row = "find_".$val;
				global ${$row};
				$tmp = ${$row};
				$validate = false;
				$val_n = $val;
			}
			
			$obField = $entity::getEntity()->getField($val_n);
			if($obField instanceof \Bitrix\Main\Entity\IntegerField){
				$tmp = intval($tmp);
			}elseif($obField instanceof \Bitrix\Main\Entity\StringField){
				if(!$keyFilterAdd) $keyFilterAdd = "?";
			}elseif($obField instanceof \Bitrix\Main\Entity\BooleanField){
				$validate = (is_array($val) && isset($val["VALIDATE"])) ? $val["VALIDATE"] : true;
			}
			if($tmp && $validate){
				$res = new \Bitrix\Main\Entity\Result();
				$obField->validateValue($tmp, $this->getParam("PRIMARY"), array($val_n=>$tmp), $res);
				if($res->isSuccess()){
					$arFilter[$keyFilterAdd.$val_n] = $tmp;
				}else{
					foreach($res->getErrorMessages() as $err)
						$this->getAdminList()->AddFilterError($err);
				}
			}elseif($tmp){
				$arFilter[$keyFilterAdd.$val_n] = $tmp;
			}
			
		}
		
		if(!empty($arFilter)) $this->addFilter($arFilter);
		return $error;
		
	}
	
	public function AddGroupActionTable(){
		
		if(!$this->getParam("ADD_GROUP_ACTION")) return;
		
		$arActions = array();
		foreach($this->getParam("ADD_GROUP_ACTION") as $val){
			if(is_array($val)){
				$arActions[$val['key']] = $val['title'];
			}else{
				$arActions[$val] = Loc::getMessage($this->getParam("LANG_CODE")."GROUP_".strtoupper($val));
			}
			
		}
		
		$this->getAdminList()->AddGroupActionTable($arActions);
		
	}
	
	public function getAdminRow(){
		while ($arRes = $this->getAdminResult()->GetNext())
		{
			$this->getMlifeRowListAdmin($arRes);
		}
	}
	
	//вывод заметки в подвале
	public function getNote(){
		if(Loc::getMessage($this->getParam("LANG_CODE")."NOTE")){
			echo BeginNote();
			echo Loc::getMessage($this->getParam("LANG_CODE")."NOTE");
			echo EndNote();
		}
		return;
	}
	
	public function defaultInterface(){
		
		global $APPLICATION, $adminPage, $USER, $adminMenu, $adminChain, $POST_RIGHT;
		
		//инициализация фильтра
		$this->initFilter();
		//добавление фильтра
		$this->checkFilter();
		//проверка действий
		$this->checkActions($POST_RIGHT);
		
		//доступные колонки
		$this->AddHeaders();
		//устанавливает только нужные поля в выборку
		
		//формирование списка
		$this->getAdminRow();
		
		//текст навигации
		$this->setNavText();
		
		//групповые действия
		$this->AddGroupActionTable();
		//навигация и подсчет
		$this->addFooter();
		//кнопка на панели
		$this->AddAdminContextMenu();
		//непонятно
		$this->getAdminList()->CheckListMode();
		//заголовок
		$APPLICATION->SetTitle(Loc::getMessage($this->getParam("LANG_CODE")."TITLE"));
		
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
		
		$this->getFindHtml();
		$this->getAdminList()->DisplayList();
		$this->getNote();
		
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
		
	}
	
}