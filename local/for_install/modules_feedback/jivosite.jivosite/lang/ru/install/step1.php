<?

	$MESS['SIGN_UP_FORM'] = "
	
		<p style='width: 500px'>Сейчас мы зарегистрируем новый, либо подключим существующий аккаунт JivoSite к вашему сайту ".COption::GetOptionString('main', 'server_name').". Если вам нужна помощь - пожалуйста, напишите нам на info@jivosite.ru или <a href='http://jivosite.copiny.com/' target='_blank'>задайте вопрос на форуме</a></p>

		<form method='post'>

		<p><b>Ваш e-mail (он же логин)</b> 
		<input type='text' name='email' value='".CUser::GetEmail()."'/>
		<p class='comment'>Введите адрес e-mail, который вы будете использовать для входа в панель управления JivoSite, а так же для входа в приложение агента и получения уведомлений от JivoSite. Если у вас уже есть аккаунт JivoSite - укажите ваш e-mail и пароль, который вы использовали при регистрации</p>

		<p><b>Пароль к JivoSite</b>
		<input type='password' name='password'/>
		<p class='comment'>Придумайте пароль для подключения к сервису JivoSite. В целях безопасности, этот пароль не должен совпадать с паролем от Битрикс. Если у вас уже есть аккаунт JivoSite - укажите пароль от него</p>

		<p><b>Ваше имя</b> 
		<input type='text' name='userDisplayName' value='".CUser::GetFullName()."'/>
		<p class='comment'>Ваше имя по-русски, которое будет отображаться посетителям сайта в чате</p>

		<input type='hidden' name='step' value='2'/>

		<p><input type='submit' value='Установить онлайн-консультант JivoSite!'>
		</form>
	
	";
	
	$MESS['BACK_TO_MODULE_LIST'] = "Вернуться к списку модулей";
?>
