<?
	namespace MHT\Forms;

	use \Webprofy\Ajax as Ajax;
	use \Webprofy\Ajax\Fields as Fields;
	
	class Complain extends Ajax\Form{
		function __construct(){
			parent::__construct('complain-form');

			$field = new Fields\Text('name', true);
			$this->addField($field);

			$field = new Fields\Text('contact', true);
			$this->addField($field);

			$field = new Fields\Text('text', true);
			$field->setTemplate('<textarea name="%NAME"></textarea>');
			$this->addField($field);

			$field = new Fields\Captcha('captcha', true);
			$this->addField($field);
			
			ob_start();
			?>
				<form id="complain_form" action="/ajax.php" method="POST" enctype="multipart/form-data" class="js-form">
					<input type="hidden" name="act" value="%NAME"/>
					<input type="hidden" name="confirm" value="1"/>
					<div class="complaint">
					    <div class="title">Написать жалобу</div>
					    <div class="close"></div>
					    <div class="steps">
					      <div class="step active" data-step="0">
					          <span class="notis">Вы не соощили, как к вам обращаться</span>
					            <label>Как вас зовут?</label>
					            %FIELD_name
					        </div>
					        <div class="step" data-step="1">
					          <span class="notis">Вы не соощили, как с вами связаться</span>
					          <label>Как с вами связаться?</label>
					          %FIELD_contact
					        </div>
					        <div class="step" data-step="2">
					          <span class="notis">Вы не соощили, какая у вас жалоба</span>
					          <label>Ваша жалоба</label>
					          %FIELD_text
					        </div>
					        <div class="step" data-step="3">
					          <div class="result process">
					              Ваша заявка обрабатывается
					            </div>
					            <div class="result success">
					              Ваша заявка принята.<br/><span>Мы рассмотрим её и свяжемся с вами в ближайшее время.</span>
					            </div>
					            <div class="result feild">
					              Произошел сбой.<br/><span>Во время отправки произошел сбой. Попробуйте повторить отправку позже.</span>
					            </div>
					        </div>
					    </div>
					    <div class="points">
					      <ul>
					          <li class="active"></li>
					            <li></li>
					            <li></li>
					            <li></li>
					        </ul>
					    </div>
					    <div class="button_block">
					      <div class="next">Далее</div>
					        <div class="notis">или нажмите enter</div>
					    </div>
					</div>
				</form>
			<?
			$this->setTemplate(ob_get_clean());
		}

		function execute($f){
			$newComplainId = \WP::addElement(array(
				'f' => array(
					'NAME' => $f['name'],
					'IBLOCK_ID' => 75
				),
				'p' => array(
					'MESSAGE:text' => $f['text'],
					'CONTACTS' => $f['contact'],
				)
			));

			if($newComplainId > 0) {

				$arFields = array(
					"AUTHOR" => $f['name'],
					"CONTACTS" => $f['contact'],
					"TEXT" => $f['text'],
					"ELEMENT_ID" => $newComplainId,
					"IBLOCK_ID" => 75
					);
				\CEvent::SendImmediate("WP_COMPLAIN_FORM", "el", $arFields);
			}
		}
	}