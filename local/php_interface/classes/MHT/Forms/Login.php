<?
	namespace MHT\Forms;

	use \Webprofy\Ajax as Ajax;
	use \Webprofy\Ajax\Fields as Fields;
	
	class Login extends Ajax\Form{
		function __construct(){
			parent::__construct('login-form');

			$field = new Fields\Text('login', true);
			$this->addField($field);

			$field = new Fields\Checkbox('remember');
			$field->setLabel('Запомнить меня');
			$this->addField($field);

			$field = new Fields\Text('password', true);
			$field->setTemplate('<input type="password" name="%NAME" value="%VALUE"/>');
			$this->addField($field);

			$this->setTemplate('
				<form action="/ajax.php" method="POST" enctype="multipart/form-data" class="js-form login" data-onsuccess="location.reload();">
					<input type="hidden" name="act" value="%NAME"/>
					<input type="hidden" name="confirm" value="1"/>

					<table>
						<tr>
							<td>Логин/email/телефон:</td>
						</tr>
						<tr>
							<td>%FIELD_login</td>
						</tr>
						<tr>
							<td>Пароль:</td>
						</tr>
						<tr>
							<td>%FIELD_password</td>
						</tr>
						<tr>
							<td>
								%FIELD_remember
								<div class="righter">
									<a href="/personal/forgot/">Забыли пароль?</a><br/>
									<a href="/personal/register/">Регистрация</a>
								</div>
							</td>
						</tr>
					</table>
					<input type="submit" value="Войти">
				</form>
			');
		}

		function execute($f){
			global $USER;
			if($USER->login($f['login'], $f['password'],$f['remember'] ? 'Y' : 'N') === true ||
				$this->loginByPhone($f) === true ||
				$this->loginByEmail($f) === true
				){
				return true;			
			}

			foreach(array('login', 'password') as $name){
				$this->getField($name)->setError('login');
			}
			return false;
		}


		function loginByPhone($f) {
			global $USER;

			$phone = $f["login"];
			$firstChar = substr($phone, 0, 1);
			$lastCharNum = strlen($phone) - 1;
	 		if($firstChar=="+") {
				$phone = substr($phone, 2, $lastCharNum);
			}
			if($firstChar=="8") {
				$phone = substr($phone, 1, $lastCharNum);	
			}
			$phone = preg_replace('/[^0-9]/', '', $phone);

			$dbUser = \CUser::GetList(($by="timestamp_x"), ($order="asc"), array("PERSONAL_PHONE"=>$phone));
			if($arUser = $dbUser -> GetNext()) {
				return $USER->login(
					$arUser['LOGIN'],
					$f['password'],
					$f['remember'] ? 'Y' : 'N'
				);
			}
			return false;
		}

		function loginByEmail($f) {
			global $USER;
			$dbUser = \CUser::GetList(($by="timestamp_x"), ($order="asc"), array("EMAIL"=>$f["login"]));
			if($arUser = $dbUser -> GetNext()) {

				return $USER->login(
					$arUser['LOGIN'],
					$f['password'],
					$f['remember'] ? 'Y' : 'N'
				);
			}
			return false;
		}

	}