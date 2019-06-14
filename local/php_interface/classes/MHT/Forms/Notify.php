<?
	namespace MHT\Forms;

	use \Webprofy\Ajax as Ajax;
	use \Webprofy\Ajax\Fields as Fields;
	
	class Notify extends Ajax\Form{
		function __construct(){
			parent::__construct('notify-form');

			$field = new Fields\Text('name', true);
			$this->addField($field);

			$field = new Fields\Text('phone', true);
			$this->addField($field);

			$field = new Fields\Text('product');
			$field->setTemplate('<input type="hidden" name="%NAME" value="%VALUE"/>');
			$this->addField($field);

			$field = new Fields\Text('price');
			$field->setTemplate('<input type="hidden" name="%NAME" value="%VALUE"/>');
			$this->addField($field);
			
			$field = new Fields\Text('code');
			$field->setTemplate('<input type="hidden" name="%NAME" value="%VALUE"/>');
			$this->addField($field);
			
			$field = new Fields\Text('productname');
			$field->setTemplate('<input type="hidden" name="%NAME" value="%VALUE"/>');
			$this->addField($field);
			
			$this->setTemplate('
				<form id="faq_form" action="/ajax.php" method="POST" enctype="multipart/form-data" class="js-form" data-onsuccess="'."mht.notify('Наш оператор свяжется с вами, как только товар появится в наличии.<br/>Обращаем ваше внимание, что обработка заказов осуществляется ежедневно с 9:00 до 21:00.'); mht.modal({hide : true})".'">
					<input type="hidden" name="act" value="%NAME"/>
					<input type="hidden" name="confirm" value="1"/>

					<table>
						<tr>
							<td>Ваше имя:</td>
						</tr>
						<tr>
							<td>%FIELD_name</td>
						</tr>
						<tr>
							<td>Телефон:</td>
						</tr>
						<tr>
							<td>%FIELD_phone</td>
						</tr>
					</table>
					%FIELD_product
					%FIELD_price
					%FIELD_code
					%FIELD_productname
					<input type="submit" value="Отправить">
				</form>
			');
		}

		function execute($f){
			$elementId = \WP::addElement(array(
				'f' => array(
					'IBLOCK_ID' => 297,
					'ACTIVE' => 'N',
					'NAME' => $f['name']
				),
				'p' => array(
					'PRODUCT' => $f['product'],
					'PHONE' => $f['phone']
				)
			));
			$arEventFields = array(
				"RS_FORM_ID" => 297,
				"RS_FORM_RESULT_ID" => $elementId,
				"RS_FORM_NAME" => $f['name'],
				"RS_FORM_PHONE" => $f['phone'],
				"RS_FORM_PRODUCT_ID" => $f['product'],
				
				"RS_FORM_PRODUCT_NAME" => $f['productname'],
				"RS_FORM_PRODUCT_CODE" => $f['code'],
				"RS_FORM_PRODUCT_PRICE" => $f['price']
			);
			\CModule::IncludeModule("main");
			$arrSites = array();
			$objSites = \CSite::GetList();
			while ($arrSite = $objSites->Fetch())
			    $arrSites[] = $arrSite["ID"];
			\CEvent::Send("WP_FORM_NOTIFY", $arrSites, $arEventFields);
		}
	}