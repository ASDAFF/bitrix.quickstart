<?php

/**
 * Класс компонента
 */
class CLssoftCs extends CBitrixComponent {
	/**
	 * Обработка компонента
	 */
	public function executeComponent() {
		/**
		 * Получаем переменные
		 */
		$arDefaultVariableAliases = array('CS_PAGE'=>'CSP');
		$arComponentVariables = array('CS_PAGE');
		$arVariables = array();
		$arVariableAliases=CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases,$this->arParams["VARIABLE_ALIASES"]);
		CComponentEngine::InitComponentVariables(false,$arComponentVariables,$arVariableAliases,$arVariables);
		/**
		 * Валидация переменных
		 */
		$sPage=isset($arVariables['CS_PAGE']) ? $arVariables['CS_PAGE'] : 'show';
		if (!in_array($sPage,array('show','registration'))) {
			$sPage='show';
		}
		/**
		 * Подключаем шаблон
		 */
		$this->includeComponentTemplate($sPage);
    }
};