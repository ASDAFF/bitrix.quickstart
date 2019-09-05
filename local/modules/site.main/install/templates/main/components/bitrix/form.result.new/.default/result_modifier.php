<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arResult['REQUIRED_STAR'] = '*';

foreach ($arResult['QUESTIONS'] as $questionID => &$question) {
	//Поле содержит ошибку
	$question['HAS_ERROR'] = is_array($arResult['FORM_ERRORS']) && array_key_exists($questionID, $arResult['FORM_ERRORS']);
	
	//Уникальный DOM ID
	$question['DOM_ID'] = 'form-' . $arResult['arForm']['ID'] . '-' . $questionID;
	
	foreach ($question['STRUCTURE'] as $structIndex => &$structItem) {
		$isCbOrRadio = in_array($structItem['FIELD_TYPE'], array('checkbox', 'radio'));
		
		//Свойства поля
		$structItem['FIELD_NAME'] = sprintf(
			'form_%s_%s',
			$structItem['FIELD_TYPE'],
			$isCbOrRadio ? $questionID : $structItem['ID']
		);
		$structItem['FIELD_MULTI'] = $structItem['FIELD_TYPE'] == 'checkbox';
		$structItem['FIELD_VALUE'] = isset($arResult['arrVALUES'][$structItem['FIELD_NAME']]) ? $arResult['arrVALUES'][$structItem['FIELD_NAME']] : null;
		
		//CSS-класс поля
		$question['HTML_CODE'] = preg_replace('/class="[^"]*"/', '', $question['HTML_CODE']);
		if(!$isCbOrRadio) {
			$question['HTML_CODE'] = str_replace(
				array(
					'<input',
					'<select',
					'<textarea',
				),
				array(
					'<input class="form-control field-' . $questionID . '"',
					'<select class="form-control field-' . $questionID . '"',
					'<textarea class="form-control field-' . $questionID . '"',
				),
				$question['HTML_CODE']
			);
		}
		
		//Подгружаем валидаторы
		if ($structIndex == 0) {
			$question['VALIDATORS'] = array();
			$validators = CFormValidator::GetList(
				$structItem['FIELD_ID'],
				array('TYPE' => $structItem['FIELD_TYPE']),
				$by = 'C_SORT',
				$order = 'ASC'
			);
			while ($validator = $validators->Fetch())
			{
				$question['VALIDATORS'][$validator['NAME']] = $validator;
			}
		}
		
		//Анализируем тип поля
		switch ($structItem['FIELD_TYPE']) {
			case 'checkbox':
			case 'radio':
				if ($structIndex == 0) {
					$question['HTML_CODE'] = '';
				}
				
				if (is_array($structItem['FIELD_VALUE'])) {
					$structItem['FIELD_CHECKED'] = in_array($structItem['ID'], $structItem['FIELD_VALUE']);
				} else {
					$structItem['FIELD_CHECKED'] = $structItem['FIELD_PARAM'] == 'checked';
				}
				
				$question['HTML_CODE'] .= sprintf(
					'<label class="%s"><input type="%s" name="%s" value="%s"%s/><span>%s</span></label>',
					$structItem['FIELD_TYPE'] . '-inline',
					$structItem['FIELD_TYPE'],
					$structItem['FIELD_NAME'] . ($structItem['FIELD_MULTI'] ? '[]' : ''),
					$structItem['ID'],
					$structItem['FIELD_CHECKED'] ? ' checked' : '',
					$structItem['MESSAGE']
				);
				break;
			
			case 'email':
				if ($structIndex == 0) {
					$question['HTML_CODE'] = str_replace('type="text"', 'type="email"', $question['HTML_CODE']);
				}
				break;
			
			case 'date':
				if ($structIndex == 0) {
					$question['HTML_CODE'] = '';
				}
				
				$question['HTML_CODE'] .= sprintf(
					'<input class="widget datepicker" type="text" name="%s" value="%s"/>',
					$structItem['FIELD_NAME'],
					$structItem['FIELD_VALUE']
				);
				break;
			
			case 'file':
				if ($structIndex == 0) {
					$question['HTML_CODE'] = '';
				}
				
				$validateExt = array();
				if ($question['VALIDATORS']['file_type']) {
					$validateExt = explode(',', $question['VALIDATORS']['file_type']['PARAMS']['EXT']);
					foreach($validateExt as &$validateExtItem) {
						$validateExtItem = '.' . $validateExtItem;
					}
					unset($validateExtItem);
				}
				
				$question['HTML_CODE'] .= sprintf(
					'<input class="widget uploadpicker" type="file" name="%s" value="%s"%s/>',
					$structItem['FIELD_NAME'],
					$structItem['VALUE'],
					$validateExt ? ' accept="' . implode(', ', $validateExt) . '"' : ''
				);
				break;
			
			case 'url':
				if ($structIndex == 0) {
					$question['HTML_CODE'] = str_replace('type="text"', 'type="url"', $question['HTML_CODE']);
				}
				break;
		}
		
		//Аналазируем служебный комментарий поля
		switch ($arResult['arQuestions'][$questionID]['COMMENTS']) {
			case 'phone':
				if ($structIndex == 0) {
					$question['HTML_CODE'] = str_replace(
						'type="text"',
						'type="tel" maxlength="20" pattern="[0-9-+ ()]+"',
						$question['HTML_CODE']
					);
				}
				break;
		}
		
		if ($structIndex == 0 && !$isCbOrRadio) {
			//Устанавливаем атрибут "required"
			if ($question['REQUIRED'] == 'Y') {
				$question['HTML_CODE'] = str_replace(
					array(
						'<input',
						'<select',
						'<textarea',
					),
					array(
						'<input required=""',
						'<select required=""',
						'<textarea required=""',
					),
					$question['HTML_CODE']
				);
			}
			
			//Устанавливаем атрибут "placeholder"
			/*$placeHolder = $question['CAPTION'] . ($question['REQUIRED'] == 'Y' ? $arResult['REQUIRED_STAR'] : '');
			$question['HTML_CODE'] = str_replace(
				array(
					'<input',
					'<textarea',
				),
				array(
					'<input placeholder="' . $placeHolder . '"',
					'<textarea placeholder="' . $placeHolder . '"',
				),
				$question['HTML_CODE']
			);*/
			
			//Устанавливаем атрибут "id"
			$question['HTML_CODE'] = str_replace(
				array(
					'<input',
					'<select',
					'<textarea',
				),
				array(
					'<input id="' . $question['DOM_ID'] . '"',
					'<select id="' . $question['DOM_ID'] . '"',
					'<textarea id="' . $question['DOM_ID'] . '"',
				),
				$question['HTML_CODE']
			);
			
			//Устанавливаем атрибут "for" для <label>
			$question['HTML_CODE'] = preg_replace('/<label for="([^"]*)"/', '<label for="' . $question['DOM_ID'] . '"', $question['HTML_CODE']);
		}
	}
	
	unset($structItem);
}
unset($question);

//Своё сообщение в случае успешного заполнения для разных форм
if ($arResult['isFormNote'] == 'Y') {
	switch ($arResult['WEB_FORM_NAME']) {
		case 'SEND_RESUME':
			$arResult['FORM_NOTE'] = 'Спасибо, что рассказали нам о себе. В случае заинтересованности мы обязательно свяжемся с вами.';
			break;
		case 'FEEDBACK':
			$arResult['FORM_NOTE'] = 'Спасибо, ваш вопрос успешно отправлен. Мы ответим на него в ближайшее время.';
			break;
	}
}