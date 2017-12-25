<?
	if (ereg("\/en\/", $GLOBALS['APPLICATION']->GetCurDir()))
	{	
		$MESS ['MFT_NAME'] = "Name";
		$MESS ['MFT_EMAIL'] = "E-mail";
		$MESS ['MFT_MESSAGE'] = "Message";
		$MESS ['MFT_CAPTCHA'] = "Protection from automated messages";
		$MESS ['MFT_CAPTCHA_CODE'] = "Enter a word in the image";
		$MESS ['MFT_SUBMIT'] = "Send";
		$MESS ['COUNTRY'] = 'Select country';
		$MESS ['TEMA'] = 'Enter the subject of';
		$MESS ['TEMA_'] = "Theme";
	}
	elseif (ereg("\/ua\/", $GLOBALS['APPLICATION']->GetCurDir()))
	{
		$MESS ['MFT_NAME'] = "Ваше ім'я";
		$MESS ['MFT_EMAIL'] = "Ваш E-mail";
		$MESS ['MFT_MESSAGE'] = "Повідомлення";
		$MESS ['MFT_CAPTCHA'] = "Захист від автоматичних повідомлень";
		$MESS ['MFT_CAPTCHA_CODE'] = "Введіть слово на картинці";
		$MESS ['MFT_SUBMIT'] = "Відправити";
		$MESS ['COUNTRY'] = 'Виберіть країну';
		$MESS ['TEMA'] = "Вкажіть тему";
		$MESS ['TEMA_'] = "Тема";
	}
	else
	{
		$MESS ['MFT_NAME'] = "Ваше имя";
		$MESS ['MFT_EMAIL'] = "Ваш E-mail";
		$MESS ['MFT_MESSAGE'] = "Сообщение";
		$MESS ['MFT_CAPTCHA'] = "Защита от автоматических сообщений";
		$MESS ['MFT_CAPTCHA_CODE'] = "Введите слово на картинке";
		$MESS ['MFT_SUBMIT'] = "Отправить";
		$MESS ['COUNTRY'] = 'Выберите страну';
		$MESS ['TEMA'] = 'Укажите тему';
		$MESS ['TEMA_'] = "Тема";
	}
?>