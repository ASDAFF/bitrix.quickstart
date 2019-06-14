<?
$MESS["KHAYR_MODULEINSTALLER_NEED_MODULES"] = "Для установки данного решения необходимо наличие модуля #MODULE#.";
$MESS["KHAYR_MODULEINSTALLER_NEED_RIGHT_VER"] = "Для установки данного решения необходима версия главного модуля #NEED# или выше.";
$MESS["KHAYR_MODULEINSTALLER_INSTALL_OK"] = "Модуль успешно установлен.";
$MESS['KHAYR_MODULEINSTALLER_INSTALL_DEL'] = "Модуль успешно удален из системы.";
$MESS['KHAYR_MODULEINSTALLER_GOTO_SETTINGS_BUTTON'] = "Настройки";

$MESS["KHAYR_COMMENT"] = "Комментарии";
$MESS["KHAYR_COMMENT_MODULE_DESC"] = "Модуль, позволяющий организовать комментарии к элементам инфоблоков.";

$MESS["KHAYR_COMMENT_CEVENT_NAME"] = "Добавлен новый комментарий";
$MESS["KHAYR_COMMENT_CEVENT_DESCRIPTION"] = "#OBJECT_ID# - ID объекта комментирования
#NAME# - имя и e-mail автора комментария
#NONUSER# - имя автора комментария
#EMAIL# - e-mail автора комментария
#MESSAGE# - текст комментария
#URL# - адрес страницы администрирования комментариев
#EMAIL_TO# - e-mail администратора";
$MESS["KHAYR_COMMENT_CEVENT_SUBJECT"] = "#SITE_NAME#: Добавлен новый комментарий";
$MESS["KHAYR_COMMENT_CEVENT_MESSAGE"] = '<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
	</head>
	<body style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 14px; color: #000000;">
		<table style="background-color: #d1d1d1; border-radius: 4px; border:1px solid #d1d1d1; margin: 0 auto; width: 850px">
			<tbody>
				<tr>
					<td style="border: none; padding-top: 23px; padding-right: 17px; padding-bottom: 24px; padding-left: 17px; height: 83px;">
						<p style="border: 2px solid #ffffff; margin-top: 0px; margin-bottom: 0px; font-weight: bold; text-align: center; font-size: 26px; color: #0b3961; padding-top: 23px; padding-bottom: 24px;">Информационное сообщение сайта #SITE_NAME#</p>
					</td>
				</tr>
				<tr>
					<td style="border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;">
						<p style="margin-top: 30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;">Добавлен новый комментарий</p>
						<p style="margin-top: 0; margin-bottom: 20px; line-height: 20px;">#NAME#</p>
						<p style="margin-top: 0; margin-bottom: 20px; line-height: 20px;">#MESSAGE#</p>
						<p style="margin-top: 0; margin-bottom: 20px; line-height: 20px;"><a href="#URL#">Перейти на страницу управления комментариями</a></p>
					</td>
				</tr>
				<tr>
					<td style="border: none; padding-top: 0; padding-right: 44px; padding-bottom: 30px; padding-left: 44px;">
						<p style="margin-top: 20px; margin-bottom: 5px; line-height:21px; color: rgb(190, 190, 190);"><span style="font-weight: bold;">Внимание!</span> Письмо создано автоматической системой оповещения #SITE_NAME#.</p>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>';
?>