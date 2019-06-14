<?
	namespace MHT\Forms;

	use \Webprofy\Ajax as Ajax;
	use \Webprofy\Ajax\Fields as Fields;
	use \WP;
	
	class Forgot extends Ajax\Form{
		function __construct(){
			parent::__construct('forgot-form');

			$field = new Fields\Email('email', true);
			$this->addField($field);

			$field = new Fields\Captcha('captcha', true);	
			$this->addField($field);

			ob_start();
			?>
				<form id="faq_form" action="/ajax.php" method="POST" enctype="multipart/form-data" class="js-form" data-onsuccess="mht.notify('Пароль будет отправлен на вашу почту в ближайшее время');">
					<input type="hidden" name="act" value="%NAME"/>
					<input type="hidden" name="confirm" value="1"/>
				    <div class="registration_page">
			            <table>
			                <tr>
			                    <td><label>E-mail</label></td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td>%FIELD_email</td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td><label>Символы</label></td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td>%FIELD_captcha</td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td><a class="button" href="#" onclick="$(this).closest('form').submit(); return false;">Восстановить</a></td>
			                    <td></td>
			                </tr>
		               </table>
	               </div>
				</form>
			<?
			$template = ob_get_clean();
			$this->setTemplate($template);
		}

		function execute($f){
			$user = WP::bit(array(
				'of' => 'user',
				'f' => array(
					'email' => $f['email']
				),
				'one' => 'f'
			));

			if(!$user){
				$this->getField('email')->setError('login');
				return false;
			}

			$password = WP::randomString();

			global $USER;
			$USER->Update($user['ID'], array(
				'PASSWORD' => $password
			));

			\CEvent::SendImmediate('RECOVER_PASSWORD', SITE_ID, array(
				'LOGIN' => $user['LOGIN'],
				'NAME' => $user['NAME'],
				'PASSWORD' => $password,
				'EMAIL' => $f['email']
			));
		}
	}