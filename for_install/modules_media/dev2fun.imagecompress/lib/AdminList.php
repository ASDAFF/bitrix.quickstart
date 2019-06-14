<?php
/**
 * @author darkfriend <hi@darkfriend.ru>
 * @version 0.1.8
 */
namespace Dev2fun\ImageCompress;

//\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

IncludeModuleLangFile(__FILE__);

class AdminList {

	public $generalKey = 'ID';
	private $module = '';
	private $file = '';
	private $file_edit = false;
	private $file_edit_params = array();
	private $tableID = '';
	private $oFilter = null;
	private $arFilter = array();
	private $title = '';
	private $lAdmin = null;
	private $oSort = null;
	private $arHedaers = array();
	private $arVisibleHedaers = array();
	private $arEditable = array();
	private $rsRec = null;
	private $d7class = false;
	private $moduleName = null;

	public $topNote = '';
	public $bottomNote = '';

	public function __construct($module, $by='ID') {
		\CUtil::JSPostUnescape();
		$this->moduleName = $module;
		$debug_backtrace = debug_backtrace();

		$this->module = str_replace('.', '_', $module);
		$this->file = basename($debug_backtrace[0]['file']);
//		$this->file_edit = str_replace('list.php', 'edit.php', $this->file);
		$this->tableID = 'tbl_d2f_imagecompress_'.substr($this->file, 0, -4);

		$this->oSort = new \CAdminSorting($this->tableID, $by, 'DESC');
		$this->lAdmin = new \CAdminList($this->tableID, $this->oSort);
	}

	public function setD7class($class) {
		$this->d7class = $class;
		$this->setTitle(call_user_func(array($class, 'getTableTitle')));
	}

	private function application() {
		return $GLOBALS['APPLICATION'];
	}

	private function user() {
		return $GLOBALS['USER'];
	}

	private function fieldShowed($field) {
		return in_array($field, $this->arVisibleHedaers);
	}

	public function getlAdmin() {
		return $this->lAdmin;
	}

	public function getTableID() {
		return $this->tableID;
	}

	public function setTitle($title) {
		$this->title = $title;
		$this->Application()->SetTitle($this->title);
	}

	public function setRights() {
		if ($this->Application()->GetGroupRight($this->moduleName) < 'R') {
			$this->Application()->AuthForm(GetMessage('ACCESS_DENIED'));
			require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
			die();
		}
	}

	public function setDetailPage($url, $arGet=array()) {}

	public function setGroupAction($arActions) {
		if (($arID = $this->lAdmin->GroupAction()) && check_bitrix_sessid()) {
			$request = $_REQUEST;
			if (isset($arActions[$request['action']])) {
				foreach($arID as $ID) {
					call_user_func($arActions[$request['action']], $ID);
				}
			}
		}
		if ($this->lAdmin->EditAction() && isset($arActions['edit']) && check_bitrix_sessid()) {
			foreach ($GLOBALS['FIELDS'] as $ID => $arFields) {
				call_user_func($arActions['edit'], $ID, $arFields);
			}
		}
	}

	public function setContextMenu($arAddContext=array()) {
		$arContext = array();
		if ($arAddContext !== false) {
			$arContext['add'] = array(
				'TEXT' => GetMessage('MAIN_ADD'),
				'ICON' => 'btn_new',
				'LINK' => $this->module.'_'.$this->file_edit.'?lang='.LANG,
			);
			if (!empty($arAddContext)) {
				$arContext = array_merge($arContext, $arAddContext);
			}
			foreach ($arContext as $k => $v) {
				if (empty($v)) {
					unset($arContext[$k]);
				}
			}
		}
        $arContext['compress_all'] = array(
            'TEXT' => Loc::getMessage('D2F_COMPRESS_BTN_ALL'),
            'LINK' => '/bitrix/admin/dev2fun_imagecompress_files.php?compress_all=Y',
        );
		$this->lAdmin->AddAdminContextMenu($arContext);
	}

	public function getMapHeaders($fieldsMap=array()) {
		$headers = array();
		foreach ($fieldsMap as $key => $item) {
			if (array_key_exists('admin_editable', $item) && $item['admin_editable']===true) {
				$this->arEditable[] = $key;
			}
			if (array_key_exists('admin_page', $item) && $item['admin_page']===true) {
				$headers[$key] = array(
					'id' => $key,
					'sort' => $key,
					'content' => array_key_exists('title', $item) ? $item['title'] : $key,
					'default' => array_key_exists('admin_page_default', $item) && $item['admin_page']===true,
				);
			}
		}
		return $headers;
	}

	public function setHeaders($arHeaders=array()) {
		if ($this->d7class !== false) {
			$arHeaders = $this->getMapHeaders(call_user_func(array($this->d7class, 'getMap')));
		}
		if (!empty($arHeaders) && is_array($arHeaders)) {
			foreach ($arHeaders as $code => $header) {
				if (is_array($header)) {
					$header['id'] = $code;
					$this->arHedaers[$code] = $header;
				} else {
					$this->arHedaers[$code] = array(
						'id' => $code,
						'content' => $header,
						'sort' => $code,
						'default' => true,
					);
				}
			}
			$this->lAdmin->AddHeaders(array_values($this->arHedaers));
			$this->arVisibleHedaers = $this->lAdmin->GetVisibleHeaderColumns();
		}
	}

	private function getOrderBy() {
		$by = $GLOBALS['by'];
		if (array_key_exists($by, $this->arHedaers)) {
			return is_array($this->arHedaers[$by]) ? $this->arHedaers[$by]['sort'] : $this->arHedaers[$by];
		} else {
			return $this->generalKey;
		}
	}

	private function getOrderOrd() {
		$order = strtolower($GLOBALS['order']);
		return $order=='desc'||$order=='asc' ? $order : 'desc';
	}

	public function setList($rsRec=null, $arVisual=array(), $arAddActions=array(), $arEditable=array()) {
		if ($rsRec === null && $this->d7class !== false) {
			$rsRec = call_user_func(array($this->d7class, 'getList'), array('order' => array($this->getOrderBy() => $this->getOrderOrd())));
			$arEditable = $this->arEditable;
		}
		$this->rsRec = new \CAdminResult($rsRec, $this->tableID);
		$this->rsRec->NavStart();
		$this->lAdmin->NavText($this->rsRec->GetNavPrint($this->title));
		while($arRes = $this->rsRec->Fetch()) {
			$f_ID = $arRes[$this->generalKey];

			if ($this->file_edit !== false) {
				if (!empty($this->file_edit_params)) {
					$editFile = $this->module.'_'.$this->file_edit.'?';
					foreach ($this->file_edit_params as $get) {
						$editFile .= $get.'='.$arRes[$get].'&amp;';
					}
					$editFile .= 'lang='.LANG;
				} else {
					$editFile = $this->module.'_'.$this->file_edit.'?'.$this->generalKey.'='.$f_ID.'&amp;lang='.LANG;
				}
			} else {
				$editFile = false;
			}

			$row =& $this->lAdmin->AddRow($f_ID, $arRes, $editFile);

            $dirUpload = Option::get("main", "upload_dir", "upload");
            if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$dirUpload.'/'.$arRes['SUBDIR'].'/'.$arRes['FILE_NAME'])) {
                $row->bReadOnly = true;
            }

			if (!empty($arEditable)) {
				foreach ($arEditable as $editCode) {
					if (is_array($editCode)) {
						if ($editCode['TYPE'] == 'textarea') {
							//$row->AddTextField($editCode['CODE']);
						} else {
							$row->AddInputField($editCode['CODE'], array('size' => 30));
						}
					} else {
						$row->AddInputField($editCode, array('size' => 30));
					}
				}
			}

			if (!empty($arVisual)) {
				foreach ($arVisual as $code => $action) {
					if ($this->FieldShowed($code)) {
						$row->AddViewField($code, call_user_func($action, $arRes[$code], $arRes));
					}
				}
			}

			if ($arAddActions !== false) {
				$arActions = array();
				$arActions['edit'] = array(
					'ICON' => 'edit',
					'DEFAULT' => true,
					'TEXT' => GetMessage('MAIN_ADMIN_MENU_EDIT'),
					'ACTION' => $this->lAdmin->ActionRedirect($editFile)
				);
				$arActions['delete'] = array(
					'ICON' => 'delete',
					'TEXT' => GetMessage('MAIN_ADMIN_MENU_DELETE'),
					'ACTION' => "if(confirm('" . GetMessage('MAIN_ADMIN_MENU_DELETE') . "?')) " . $this->lAdmin->ActionDoGroup($f_ID, 'delete')
				);
				if (!empty($arAddActions)) {
					foreach ($arAddActions as $k => $arAction) {
						if ($arAction === false && isset($arActions[$k])) {
							unset($arActions[$k]);
							continue;
						}
						if (isset($arAction['LINK'])) {
							$arAction['LINK'] = str_replace(array('#EDIT_URL#', '#ID#'), array(htmlspecialcharsback($editFile), $f_ID), $arAction['LINK']);
						} elseif ($arAction['ACTION'] == 'group') {
							if (isset($arAction['ACTION_ALERT'])) {
								$arAction['ACTION'] = "if(confirm('".$arAction['ACTION_ALERT']."')) ".$this->lAdmin->ActionDoGroup($f_ID, $arAction['ACTION_VAR']);
							} else {
								$arAction['ACTION'] = $this->lAdmin->ActionDoGroup($f_ID, $arAction['ACTION_VAR']);
							}
						} elseif (isset($arAction['ACTION'])) {
							$arAction['ACTION'] = str_replace('#ID#', $arRes['ID'], $arAction['ACTION']);
						}
						$arActions[$k] = $arAction;
					}
					//$arActions = array_merge($arActions, $arAddActions);
				}
				$row->AddActions($arActions);
			}
		}
	}

	public function setFooter($arActions=array('delete' => '')) {
		$this->lAdmin->AddFooter(
			array(
				array('title' => GetMessage('MAIN_ADMIN_LIST_SELECTED'), 'value' => $this->rsRec->SelectedRowsCount()),
				array('counter' => true, 'title' => GetMessage('MAIN_ADMIN_LIST_CHECKED'), 'value' => '0'),
			)
		);
		$this->lAdmin->AddGroupActionTable($arActions, array('disable_action_target' => true));
	}

	public function makeFilter() {
		$arFilter = array();

		foreach ($this->arFilter as $k => $arItem) {
			if ($arItem['TYPE'] == 'calendar') {
				if (strlen($arItem['VALUE1'])) {
					$arFilter[strtoupper($k) . '1'] = $arItem['VALUE1'];
				}
				if (strlen($arItem['VALUE2'])) {
					$arFilter[strtoupper($k) . '2'] = $arItem['VALUE2'];
				}
			} elseif($k == "content_type") {
				if(strlen($arItem["VALUE"]) <= 0){
					$arItem["VALUE"] = array_keys($arItem["VARIANTS"]);
				}
				$arFilter[(isset($arItem['OPER']) ? $arItem['OPER'] : '').strtoupper($k)] = $arItem['VALUE'];
			} else {
				if (strlen($arItem['VALUE'])) {
					$arFilter[(isset($arItem['OPER']) ? $arItem['OPER'] : '').strtoupper($k)] = $arItem['VALUE'];
				}
			}
		}

		return $arFilter;
	}

	public function setFilter($arFilter) {
		$this->arFilter = $arFilter;
		$arTitles = array();
		$arInit = array();
		foreach ($arFilter as $k => $arItem) {
			$arTitles[$k] = $arItem['TITLE'];
			if ($arItem['TYPE'] == 'calendar') {
				$arInit[] = 'find_'.$k.'1';
				$arInit[] = 'find_'.$k.'2';
			} else {
				$arInit[] = 'find_'.$k;
			}
		}
		$this->lAdmin->InitFilter($arInit);
		$this->oFilter = new \CAdminFilter($this->tableID.'_filter', $arTitles);

		$arSessionVars = $_SESSION['SESS_ADMIN'][$this->tableID];
		foreach ($this->arFilter as $k => &$arItem) {
			if ($arItem['TYPE'] == 'calendar') {
				$value1 = isset($_REQUEST['find_'.$k.'1'])&&!isset($_REQUEST['del_filter']) ? $_REQUEST['find_'.$k.'1'] : (isset($arSessionVars['find_'.$k.'1']) ? $arSessionVars['find_'.$k.'1'] : '');
				$arItem['VALUE1'] = $value1;
				$value2 = isset($_REQUEST['find_'.$k.'2'])&&!isset($_REQUEST['del_filter']) ? $_REQUEST['find_'.$k.'2'] : (isset($arSessionVars['find_'.$k.'2']) ? $arSessionVars['find_'.$k.'2'] : '');
				$arItem['VALUE2'] = $value2;
			} else {
				$value = isset($_REQUEST['find_'.$k]) && !isset($_REQUEST['del_filter']) ? $_REQUEST['find_'.$k] : (isset($arSessionVars['find_'.$k]) ? $arSessionVars['find_'.$k] : '');
				$arItem['VALUE'] = $value;
			}
		}
	}

	private function outputFilter() {
		$url = $this->Application()->GetCurPage();
		?><form name="find_form" method="get" action="<?= $url;?>"><?
		$this->oFilter->Begin();
		foreach ($this->arFilter as $k => $arItem) {
			if (isset($arItem['TYPE'])) {
				$type = $arItem['TYPE'];
			} else {
				$type = 'text';
			}
			if ($type=='select' && !isset($arItem['VARIANTS']) && is_array($arItem['VARIANTS'])) {
				$type = 'text';
			}
			?>
			<tr>
				<td><?= $arItem['TITLE']?>:</td>
				<td>
					<?if ($type == 'select'):?>
						<select <?if($arItem["MULTIPLE"] == "Y"){?>multiple="multiple"<?}?> name="find_<?= $k?>">
							<option  value="">(not set)</option>
								<?foreach ($arItem['VARIANTS'] as $kv => $vv):?>
									<option value="<?= $kv?>"<?if ($arItem['VALUE'] == $kv){?> selected="selected"<?}?>><?= $vv?></option>
								<?endforeach;?>
						</select>
					<?elseif ($type == 'calendar'):?>
						<?= CalendarPeriod('find_'.$k.'1', $arItem['VALUE1'], 'find_'.$k.'2', $arItem['VALUE2'], 'find_form', 'Y');?>
					<?elseif($type == 'checkbox'):?>
						<input name="find_<?= $k?>" type="checkbox" value="Y" <? if($arItem["VALUE"] == "Y"){ echo "checked"; } ?>>
					<?else:?>
						<input type="text" name="find_<?= $k?>" value="<?echo htmlspecialcharsbx($arItem['VALUE'])?>" />
					<?endif;?>
				</td>
			</tr>
			<?
		}
		$this->oFilter->Buttons(
		        array('table_id' => $this->tableID, 'url' => $url, 'form' => 'find_form')
        );
		$this->oFilter->End();
		?></form><?
	}

	public function output() {
		global $APPLICATION, $adminPage, $USER, $adminMenu, $adminChain, $recCompress;
		$this->lAdmin->CheckListMode();
		require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
		if ($this->oFilter !== null) {
			$this->OutputFilter();
		}
		$this->compressAll();
		$this->compressResult();
//		$rs = ImageCompressTable::getList();
//		var_dump($rs->fetchAll());
        if($recCompress===false) {
            \CAdminMessage::ShowMessage(array(
                "MESSAGE"=> Compress::getInstance()->LAST_ERROR,
                "TYPE"=> "ERROR",
            ));
        }
		echo $this->topNote;
		$this->lAdmin->DisplayList();
		if (strlen($this->bottomNote)) {
			echo BeginNote();
			echo $this->bottomNote;
			echo EndNote();
		}
		require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
	}

	public function compressResult() {
//        compress_result=Y&status=success
        if($_REQUEST['compress_result']) {
            if($_REQUEST['status']=='success') {
                \CAdminMessage::ShowMessage(array(
                    "MESSAGE"=> Loc::getMessage('D2F_IMAGECOMPRESS_COMPRESS_IMAGE_STATUS_SUCCESS'),
                    "TYPE"=> "OK",
                ));
            }
        }
    }

	public function compressAll() {
	    global $APPLICATION, $recCompress;
        if($_REQUEST['compress_all']) {
            \CJSCore::Init(array('ajax'));
            echo '<div id="compressAllStatus">';
            if($_REQUEST['AJAX_IC']) {
                $APPLICATION->RestartBuffer();
                ob_start();
                $rsRes = Compress::getInstance()->getFileList(array(), array(
                    'COMRESSED' => 'N',
                    '@CONTENT_TYPE' => array(
                        'image/jpeg',
                        'image/png',
                    )
                ));
                $rsRes->NavStart(30,false,$_REQUEST['PAGEN_1']);
                $stepOnPage = 0;
                while($arFile = $rsRes->NavNext(true)) {
                    $strFilePath = \CFile::GetPath($arFile["ID"]);
                    if(!file_exists($_SERVER['DOCUMENT_ROOT'].$strFilePath)) {
                        Compress::getInstance()->addCompressTable($arFile['ID'],Array(
                            'FILE_ID' => $arFile['ID'],
                            'SIZE_BEFORE' => 0,
                            'SIZE_AFTER' => 0,
                        ));
                        continue;
                    }
                    $recCompress = Compress::getInstance()->compressImageByID($arFile['ID']);
                    $stepOnPage++;
//                    $recCompress = true;
                }
                $progressValue = (100/$rsRes->NavPageCount)*$rsRes->NavPageNomer;
            }
            if($recCompress===false) {
                $msgError = Compress::getInstance()->LAST_ERROR;
                if(!$msgError) $msgError = Loc::getMessage('D2F_IMAGECOMPRESS_DEFAULT_TEXT_ERRROR');
                \CAdminMessage::ShowMessage(array(
                    "MESSAGE"=> $msgError,
                    "TYPE"=> "ERROR",
                ));
            } else {
                $admFields = array(
                    "MESSAGE" => Loc::getMessage('D2F_IMAGECOMPRESS_COMPRESS_IMAGE_PROGRESSBAR'),
                    "DETAILS" => "#PROGRESS_BAR#",
                    "HTML" => true,
                    "TYPE" => "PROGRESS",
                    "PROGRESS_TOTAL" => $rsRes->NavPageCount,
                    "PROGRESS_VALUE" => $rsRes->NavPageNomer,
                );
                if($rsRes->NavPageCount) {
                    $admFields['PROGRESS_TEMPLATE'] = '<span>Шаг: #PROGRESS_VALUE# из #PROGRESS_TOTAL# (#PROGRESS_PERCENT#)</span>';
                }
                \CAdminMessage::ShowMessage($admFields);
            }
            if($_REQUEST['AJAX_IC']) {
                $html = ob_get_clean();
                echo \CUtil::PhpToJSObject(array(
                    'html' => $html,
                    'error' => (($recCompress===false)?true:false),
                    'step' => ($rsRes->NavPageNomer+1),
                    'allStep' => $rsRes->NavPageCount,
                    'pageCount' => $stepOnPage,
                    'count' => $rsRes->SelectedRowsCount(),
                    'progressValue' => $progressValue,
                ));
                die();
            }
            echo '</div>';
            echo '<script type="text/javascript">';
            include_once( __DIR__.'/script.js');
            echo '</script>';
        }
    }
}