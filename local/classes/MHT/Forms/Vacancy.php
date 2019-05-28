<?
	namespace MHT\Forms;

	use \Webprofy\Ajax as Ajax;
	use \Webprofy\Ajax\Fields as Fields;
	use \WP;
	
	class Vacancy extends Ajax\Form{
		function __construct(){
			parent::__construct('vacancy-form');

			$field = new Fields\Text('name', true);
			$this->addField($field);

			$field = new Fields\Text('job', true);
			$this->addField($field);

			$field = new Fields\Email('email', true);
			$this->addField($field);

			$field = new Fields\Text('phone', true);
			$this->addField($field);

			$field = new Fields\File('file', true);
			$this->addField($field);

			// $field = new Fields\Captcha('captcha', true);
			// $this->addField($field);

			$field = new Fields\Text('text', true);
			$field->setTemplate('<textarea name="%NAME"></textarea>');
			$this->addField($field);

			$this->setTemplate('
				<form action="/ajax.php" id="faq_form" method="POST" enctype="multipart/form-data" class="js-form js-vacancy-form" data-onsuccess="mht.notify(\'Ваше резюме было успешно доставлено.\', 8000)">
					<input type="hidden" name="act" value="%NAME"/>
					<input type="hidden" name="confirm" value="1"/>
		                <table>
		                    <tr>
		                        <td><label for="name">Имя</label></td>
		                        <td><label for="name">Должность</label></td>
		                    </tr>
		                    <tr>
		                        <td>%FIELD_name</td>
		                        <td>%FIELD_job</td>
		                    </tr>
		                    <tr>
		                        <td><label for="name">Телефон</label></td>
		                        <td><label for="emal">E-mail</label></td>
		                    </tr>
		                    <tr>
		                        <td>%FIELD_phone</td>
		                        <td>%FIELD_email</td>
		                    </tr>
		                    <tr>
		                        <td colspan="2"><label for="question">Сообщение</label></td>
		                    </tr>
		                    <tr>
		                        <td colspan="2">
		                            %FIELD_text
		                        </td>
		                    </tr>
		                    <tr>
		                        <td colspan="2"><label for="name">Файл резюме</label></td>
		                        '/*<td><label for="emal">Введите текст с картинки</label></td>*/.'
		                    </tr>
		                    <tr>
		                        <td colspan="2">%FIELD_file</td>
		                        './*<td class="captcha-holder">%FIELD_captcha</td>*/'
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
			$newElementId = WP::addElement(array(
				'f' => array(
					'IBLOCK_ID' => 60,
					'NAME' => $f['name']
				),
				'p' => array(
					'JOB' => $f['job'],
					'EMAIL' => $f['email'],
					'PHONE' => $f['phone'],
					'RESUME:file' => $f['file'],
					'TEXT:text' => $f['text'],
				)
			));

			if($newElementId > 0) {
				$arFields = array(
					"AUTHOR" => $f['name'],
					"JOB" => $f['job'],
					"EMAIL" => $f['email'],
					"PHONE" => $f['phone'],
					"MESSAGE" => $f['text'],
					"ELEMENT_ID" => $newElementId,
					"IBLOCK_ID" =>  60 // инфоблок откликов на вакансию
				);
				\CEvent::SendImmediate("WP_VACANCY_FORM", "el", $arFields);
			}

			ob_start();
			?>
				<div>Сообщение</div>
				<?
					foreach(array(
					) as $a){

					}
				?>
				<div>Отправитель: <b><?=$f['name']?></b></div>
				.
			<?
			$text = ob_get_clean();
			WP::mail(array(
				'to'=>'leemright@bk.ru',
				'name' => 'MHT: сообщение в FAQ',
				'text' => array(
					'title' => 'Сообщение в FAQ',
					'description' => 'Пользователь задал вопрос.',
					'fields' => array(
						array('Отправитель', $f['name'], 'text'),
						array('Должность', $f['job'], 'text'),
						array('Телефон', $f['phone'], 'phone'),
						array('Email', $f['email'], 'email'),
						array('Текст', $f['text'], 'text'),
					)
				)
			));
		}
	}