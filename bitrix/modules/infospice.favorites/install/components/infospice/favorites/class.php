<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?

class CInfospiceFavorites extends CBitrixComponent {

	var $userSectionID = 0;

	public function GetFavoriteIBlockID() {
		$this->arParams["IBLOCK_ID"] = trim($this->arParams["IBLOCK_ID"]);
		$arIBlock = CIBlock::GetList(array(), array('ID' => $this->arParams['IBLOCK_ID']), false)->Fetch();
		if (!intval($arIBlock['ID'])) {
			$arIBlock = CIBlock::GetList(array(), array('CODE' => 'favorites'), false)->Fetch();
			if (intval($arIBlock['ID'])) {
				return $arIBlock['ID'];
			}
		}
		return $this->arParams["IBLOCK_ID"];
	}

	public function CheckUserSection() {
		global $USER;
		if ($arSection = CIBlockSection::GetList(array(), array(
					'IBLOCK_TYPE'	 => $this->arParams['IBLOCK_TYPE'],
					'IBLOCK_ID'		 => $this->arParams['IBLOCK_ID'],
					'NAME'			 => $USER->GetID()))->Fetch()) {
			$this->userSectionID = $arSection['ID'];
		} else {
			$obSection = new CIBlockSection;
			if (!$this->userSectionID = $obSection->Add(array(
				'IBLOCK_ID'	 => $this->arParams['IBLOCK_ID'],
				'NAME'		 => $USER->GetID()))) {

			}
		}
		return $this->userSectionID;
	}

	public function IsFavoriteUrl($sUrl = '') {
		if (str_replace(array('http://', $_SERVER['SERVER_NAME']), '', $sUrl) == $_SERVER['REQUEST_URI']) {
			return true;
		} else {
			return false;
		}
	}

}