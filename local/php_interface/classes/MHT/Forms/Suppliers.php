<?
	namespace MHT\Forms;

	use \Webprofy\Ajax as Ajax;
	use \Webprofy\Ajax\Fields as Fields;
	
	class Suppliers extends Ajax\Form{
		function __construct(){
			parent::__construct('suppliers_form');

			$field = new Fields\Text('company', true);
			$this->addField($field);
			
			$field = new Fields\Text('location', true);
			$this->addField($field);
			
			$field = new Fields\Select('status');
			$field->addOption('производитель','129582');
			$field->addOption('дилер','129583');
			$field->addOption('оф. представитель','129584');
			$field->addOption('дистрибьютор','129585');			
			$this->addField($field);
			
			$field = new Fields\Text('website', true);
			$this->addField($field);
			
			$field = new Fields\Text('info', true);
			$field->setTemplate('<textarea name="%NAME"></textarea>');
			$this->addField($field);
			
			$field = new Fields\Text('shops', true);
			$field->setTemplate('<textarea name="%NAME"></textarea>');
			$this->addField($field);
			
			$field = new Fields\Text('fio', true);
			$this->addField($field);
			
			$field = new Fields\Text('mylo', true);
			$this->addField($field);
								
			$field = new Fields\Text('phone', true);
			$this->addField($field);
			
			$field = new Fields\Text('email', true);
			$this->addField($field);

			$field = new Fields\File('attach', true);
			$this->addField($field);
            GLOBAL $APPLICATION;
            ob_start();
            $APPLICATION->IncludeComponent(
                "itsfera:agreement",
                "2018_supply",
                Array()
            );
            $agreement = ob_get_clean();

			$this->setTemplate('
				<form id="suppliers_form" action="/ajax_its.php" method="POST" enctype="multipart/form-data" class="js-form" data-onsuccess="'."mht.notify('Ваше предложение успешно отправлено.')".'">
					<input type="hidden" name="act" value="%NAME"/>
					<input type="hidden" name="confirm" value="1"/>
					
	                <table>
	                    <tr>
	                        <td><label for="company">Название компании</label></td>
							<td><label for="location">Местонахождение</label></td>
	                    </tr>
	                    <tr>
	                        <td>%FIELD_company</td>
	                        <td>%FIELD_location</td>
	                    </tr>
						
						<tr>
	                        <td><label for="status">Статус</label></td>
							<td><label for="website">Сайт компании</label></td>
	                    </tr>
	                    <tr>
	                        <td>%FIELD_status</td>
	                        <td>%FIELD_website</td>
	                    </tr>
						
						<tr>
	                        <td colspan="2">
								<label for="info">Краткая информация о компании</label>
							</td>							
	                    </tr>
	                    <tr>
	                        <td colspan="2">%FIELD_info</td>	                        
	                    </tr>
						
						<tr>
	                        <td colspan="2">
								<label for="shops">Точки продаж (список)</label>
							</td>
	                    </tr>
	                    <tr>
	                        <td colspan="2">%FIELD_shops</td>
	                    </tr>
						
						<tr>
	                        <td><label for="status">Как к Вам обращаться</label></td>
							<td><label for="email">Адрес электронной почты</label></td>
	                    </tr>
	                    <tr>
	                        <td>%FIELD_fio</td>
	                        <td>%FIELD_mylo</td>
	                    </tr>
						
						<tr id="e-mail">
	                        <td colspan="2"><label for="email">E-mail</label></td>
						</tr>
						<tr id="e-mail_input">
							<td colspan="2">%FIELD_email</td>
	                    </tr>
						
						<tr>
	                        <td><label for="status">Контактный телефон</label></td>
							<td><label for="email">Прикрепить файл</label></td>
	                    </tr>
	                    <tr>
	                        <td>%FIELD_phone</td>
	                        <td class="valigntop">%FIELD_attach</td>
	                    </tr>
						<tr>
						    <td colspan="2">
						    '.$agreement.'
                            </td>
						    
                        </tr>
	                    <tr>
	                        <td><a href="#" name="send" class="submit" onclick="if( !$(this).hasClass(\'disabled\') ) { $(\'#suppliers_form\').submit(); } return false;">отправить</a></td>
	                        <td></td>
							
							
							
	                    </tr>
	                </table>
				</form>
                <div class="top-notification">
                    <div class="wrapper">
                        <div class="image">
                        </div>
                        <div class="text">
                            Данные успешно отправлены.
                        </div>
                    </div>
                </div>
			');
		}

		function execute($f){
			//echo ('exec');
			\ITS::addElement(array(
				'f' => array(
					'NAME' => $f['fio'],
					'IBLOCK_ID' => 506
				),
				'p' => array(
					'LOCATION:text' => $f['location'],
					'STATUS' => $f['status'],
					'NAME' => $f['fio'],
					'EMAIL' => $f['email']
				)
			));
		}
	}