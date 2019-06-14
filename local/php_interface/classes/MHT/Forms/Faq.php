<?
	namespace MHT\Forms;

	use \Webprofy\Ajax as Ajax;
	use \Webprofy\Ajax\Fields as Fields;
	
	class Faq extends Ajax\Form{
		function __construct(){
			parent::__construct('faq-form');

			$field = new Fields\Text('name', true);
			$this->addField($field);

			$field = new Fields\Email('email', true);
			$this->addField($field);

			$field = new Fields\Text('text', true);
			$field->setTemplate('<textarea name="%NAME"></textarea>');
			$this->addField($field);

			$this->setTemplate('
				<form id="faq_form" action="/ajax.php" method="POST" enctype="multipart/form-data" class="js-form" data-onsuccess="'."mht.notify('Ваш вопрос успешно отправлен.')".'">
					<input type="hidden" name="act" value="%NAME"/>
					<input type="hidden" name="confirm" value="1"/>
	                <table>
	                    <tr>
	                        <td><label for="name">Имя</label></td>
	                        <td><label for="emal">E-mail</label></td>
	                    </tr>
	                    <tr>
	                        <td>%FIELD_name</td>
	                        <td>%FIELD_email</td>
	                    </tr>
	                    <tr>
	                        <td colspan="2"><label for="question">Ваш вопрос</label></td>
	                    </tr>
	                    <tr>
	                        <td colspan="2">
	                            %FIELD_text
	                        </td>
	                    </tr>
	                    <tr>
	                        <td><a href="#" name="send" class="submit" onclick="$(\'#faq_form\').submit(); return false;">отправить</a></td>
	                        <td></td>
	                    </tr>
	                </table>
				</form>
');
		}

		function execute($f){
			\WP::addElement(array(
				'f' => array(
					'NAME' => $f['name'],
					'IBLOCK_ID' => 57,
					'IBLOCK_SECTION_ID' => 16719
				),
				'p' => array(
					'ANSWER:text' => '',
					'QUESTION:text' => $f['text'],
					'NAME' => $f['name'],
					'EMAIL' => $f['email']
				)
			));
		}
	}