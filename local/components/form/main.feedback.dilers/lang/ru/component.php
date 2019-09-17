<?
	if (ereg("\/en\/", $GLOBALS['APPLICATION']->GetCurDir()))
	{
		$MESS ['MF_OK_MESSAGE'] = "Thank you, your message is accepted.";
		$MESS ['MF_REQ_NAME'] = "Enter your name.";
		$MESS ['MF_REQ_EMAIL'] = "Enter E-mail, which you want to get an answer.";
		$MESS ['MF_REQ_MESSAGE'] = "You have not written a message.";
		$MESS ['MF_REQ_COUNTRY'] = "Select your country";
		$MESS ['MF_REQ_TEMA'] = "Indicate the";
		$MESS ['MF_EMAIL_NOT_VALID'] = "The above E-mail is incorrect.";
		$MESS ['MF_CAPTCHA_WRONG'] = "Invalid security code from the automated message.";
		$MESS ['MF_CAPTHCA_EMPTY'] = "Not specified security code from the automatic messages.";
		$MESS ['MF_SESS_EXP'] = "Your session has expired. Send the message again.";
	}
	elseif (ereg("\/ua\/", $GLOBALS['APPLICATION']->GetCurDir()))
	{
		$MESS ['MF_OK_MESSAGE'] = "Спасибі, ваше повідомлення прийнято.";
		$MESS ['MF_REQ_NAME'] = "Вкажіть Ваше ім'я.";
		$MESS ['MF_REQ_EMAIL'] = "Вкажіть E-mail, на який хочете отримати відповідь.";
		$MESS ['MF_REQ_MESSAGE'] = "Ви не написали повідомлення.";
		$MESS ['MF_REQ_COUNTRY'] = "Вкажіть свою країну";
		$MESS ['MF_REQ_TEMA'] = "Вкажіть тему";
		$MESS ['MF_EMAIL_NOT_VALID'] = "Зазначений E-mail некоректний.";
		$MESS ['MF_CAPTCHA_WRONG'] = "Невірно вказаний код захисту від автоматичних повідомлень.";
		$MESS ['MF_CAPTHCA_EMPTY'] = "Не вказаний код захисту від автоматичних повідомлень.";
		$MESS ['MF_SESS_EXP'] = "Ваша сесія закінчилася. Надіслати повідомлення повторно.";
	}
	else
	{
		$MESS ['MF_OK_MESSAGE'] = "Спасибо, ваше сообщение принято.";
		$MESS ['MF_REQ_NAME'] = "Укажите ваше имя.";
		$MESS ['MF_REQ_EMAIL'] = "Укажите E-mail, на который хотите получить ответ.";
		$MESS ['MF_REQ_MESSAGE'] = "Вы не написали сообщение.";
		$MESS ['MF_REQ_COUNTRY'] = "Укажите свою страну";
		$MESS ['MF_REQ_TEMA'] = "Укажите тему";
		$MESS ['MF_EMAIL_NOT_VALID'] = "Указанный E-mail некорректен.";
		$MESS ['MF_CAPTCHA_WRONG'] = "Неверно указан код защиты от автоматических сообщений.";
		$MESS ['MF_CAPTHCA_EMPTY'] = "Не указан код защиты от автоматических сообщений.";
		$MESS ['MF_SESS_EXP'] = "Ваша сессия истекла. Отправьте сообщение повторно.";
	}
?>