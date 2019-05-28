<?
	namespace MHT\Forms;

	use \Webprofy\Ajax as Ajax;
	use \Webprofy\Ajax\Fields as Fields;
	
	class Register extends Ajax\Form{
		function __construct(){
			parent::__construct('register-form');

			foreach(array(
				array('f', false),
				array('i', false),
				array('login', true),
				array('email', true),
				array('password', true),
				array('password_2', true)
			) as $a){
				list($name, $req) = $a;
				$field = new Fields\Text($name, $req);
				if(strpos($name, 'password') === 0){
					$field->setTemplate('<input type="password" name="%NAME">');
				}
				$this->addField($field);
			}

			$field = new Fields\Captcha('captcha', true);	
			$this->addField($field);
			ob_start();
			?>
				<form id="complain_form" action="/ajax.php" method="POST" enctype="multipart/form-data" class="js-form" data-onsuccess="location.href = '/personal/';">
					<input type="hidden" name="act" value="%NAME"/>
					<input type="hidden" name="confirm" value="1"/>
				    <div class="registration_page">
			            <table>
			                <tr>
			                    <td><label>Фамилия</label></td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td>%FIELD_f</td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td><label>Имя</label></td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td>%FIELD_i</td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td><label>Логин <span>*</span></label></td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td>%FIELD_login</td>
			                    <td><span class="notis">минимум<br/>3 символа</span></td>
			                </tr>
			                <tr>
			                    <td><label>Пароль <span>*</span></label></td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td>%FIELD_password</td>
			                    <td><span class="notis">минимум<br/>6 символов</span></td>
			                </tr>
			                <tr>
			                    <td><label>Подтверждение пароля <span>*</span></label></td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td>%FIELD_password_2</td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td><label>E-mail <span>*</span></label></td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td id="email_input_container">%FIELD_email</td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td><label>Введите слово на картинке <span>*</span></label></td>
			                    <td></td>
			                </tr>
			                <tr>
			                    <td>%FIELD_captcha</td>
			                    <td></td>
			                </tr>
			                <tr>
                                <td>
                                    <? GLOBAL $APPLICATION;
                                    $APPLICATION->IncludeComponent(
                                        "itsfera:agreement",
                                        ".default",
                                        Array()
                                    );?>
                                <td>
                                <td><td>
			                </tr>
			                <tr>
			                    <td><a class="button" href="#" onclick="$(this).closest('form').submit(); (window['rrApiOnReady'] = window['rrApiOnReady'] || []).push(function() { rrApi.setEmail($('#email_input_container input').val()); }); return false;">регистрация</a></td>
			                    <td></td>
								
								
  



			                </tr>
			                <tr>
			                    <td><span class="notis"><span>*</span> Обязательные поля</span></td>
			                    <td></td>
			                </tr>
			            </table>
			        </div>
			        <a href="/personal/auth/" data-hayhop="#auth_holder" data-title="Войти">Авторизация</a>
				</form>
			<?
			$this->setTemplate(ob_get_clean());
		}

		function execute($f){			
			global $USER;
			\COption::SetOptionString("main","captcha_registration","N");
			$r = $USER->Register(
				$f['login'],
				$f['i'],
				$f['f'],
				$f['password'],
				$f['password_2'],
				$f['email']
			);

			//добавляем купон за регистрацию
            $coupon = \Bitrix\Sale\Internals\DiscountCouponTable::generateCoupon(true);

            $addDb = \Bitrix\Sale\Internals\DiscountCouponTable::add(array(
                'DISCOUNT_ID' => 4,
                'ACTIVE' => 'Y',
                'COUPON' => $coupon,
                'TYPE' => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_ONE_ORDER,
                'MAX_USE' => 1,
                'USER_ID' => $USER->GetID(),
                'DESCRIPTION' => 'Скидка за регистрацию на сайте',
            ));
            if($addDb->isSuccess()) {
                $arEventFields = array( "COUPON"=>$coupon, "EMAIL"=>$USER->GetEmail(), "USER_ID"=>$USER->GetID() );
                if ( ! \CEvent::Send("ITSFERA_COUPON_FOR_REGISTRATION", 'el', $arEventFields, 'Y', 170)) {
                    \CEventLog::Add(array(
                        "SEVERITY"      => "INFO",
                        "AUDIT_TYPE_ID" => "DEBUG",
                        "MODULE_ID"     => "main",
                        "ITEM_ID"       => 123,
                        "DESCRIPTION"   => "Ошибка отправки сообщения. Скидка за регистрацию на сайте, шаблон 170",
                    ));
                }
            }

			\COption::SetOptionString("main","captcha_registration","Y");
			if($r["TYPE"] == "ERROR"){
				echo json_encode(
					array(
						'ok'=>'0',
						'fields'=>array(
							array(
								"type"=>"required",
								"ru"=>$r["MESSAGE"],
								"name"=>"login"
							)
						)
					)
				);				
				exit();
				return false;				
			}
		}
	}