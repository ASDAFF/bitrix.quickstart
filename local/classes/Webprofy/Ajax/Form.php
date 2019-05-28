<?
	namespace Webprofy\Ajax;

	
	class Form{
		private
			$name,
			$template = '
				<form action="/ajax.php" method="POST" enctype="multipart/form-data" class="js-form">
					<input type="hidden" name="act" value="%NAME"/>
					<input type="hidden" name="confirm" value="1"/>
					%FIELDS
					<input type="submit" name="send" value="Отправить"/>
				</form>
			';

		protected
			$fields = array();

		function __construct($name = null){
			if($name === null){
				$name = get_class($this);
			}
			$this->name = $name;
		}

		function getName(){
			return $this->name;
		}

		function addField(Field $field){
			$this->fields[$field->getName()] = $field;
		}

		function addFields(array $fields){
			foreach($fields as $field){
				$this->addField($field);
			}
		}

		function checkFields(){
			$ok = true;
			foreach($this->fields as $field){
				if(!$field->checkValue()){
					$ok = false;
				}
			}
			return $ok;
		}

		function getField($name){
			return empty($this->fields[$name]) ? null : $this->fields[$name];
		}

		function getFieldsValues(){
			$values = array();
			foreach($this->fields as $field){
				$values[$field->getName()] = $field->getValue();
			}
			return $values;
		}

		function getBadFieldsInfo(){
			$result = array();
			foreach($this->fields as $field){
				if($field->isBad()){
					$result[] = $field->getBadInfo();
				}
			}
			return $result;
		}

		private static $jsShown = false;

		function getJSHTML(){
			if(self::$jsShown){
				return;
			}
			self::$jsShown = true;

			ob_start();
			?>
				<script>
					$(function(){
						$('.js-form').each(function(){
							
							if($(this).is('.js-form-initialized'))
								return;
							$(this).addClass('js-form-initialized');

							var n = 0;
							var $form = $(this).ajaxForm({
									clearForm: false,
									resetForm: false,
									beforeSubmit : function(fields){
										$.each(fields, function(i, field){
											if(field.name == 'confirm'){
												field.value = 0;
											}
										});
										$form.addClass('loading');
									},
									success : function(response){
										$form.removeClass('loading');
										eval('response = ' + response + ';');
										if(response.ok == '1' || !response.fields || response.fields.length == 0){

											$(document).trigger('formajax.success', [ $form.attr('id') , $form ]);

											$form.addClass('form-complete');
											var onsuccess = $form.attr('data-onsuccess');
											if(onsuccess && onsuccess.length){
												eval(onsuccess);
											}
											$form.find('input[type=text], textarea').val('');
											$form.find('.clear-file-input').trigger('click');
										}
										else{
											$form.addClass('error');
											if(response.fields && response.fields.length){
												$.each(response.fields, function(i, field){
													$form.find('[name=' + field.name + ']').addClass('error');
													
													if(
														field.ru
													){
														$form.find('[name=' + field.name + ']').prev(".error-notice").remove();
														$form.find('[name=' + field.name + ']').before('<span class="error-notice">'+field.ru+'</span>');
													}
												});
											}
										}
										captcha.update();
									}
								}),
								captcha = {
									ok : false,
									_init : function(){
										var self = this;
										this.e = {
											image : $form.find('.js-captcha-image'),
											text : $form.find('.js-captcha-text'),
											code : $form.find('.js-captcha-code')
										};
										if(!this.e.text.length || !this.e.image.length || !this.e.code.length){
											return;
										}
										this.ok = true;
										this.e.image.click(function(){
											self.update();
										})
									},
									update : function(){
										if(!this.ok){
											return;
										}
										var self = this;
										$.post('/ajax.php', {
											'get_captcha' : '1'
										}, function(id){
											self.e.text.val('');
											self.e.image.attr('src', '/bitrix/tools/captcha.php?captcha_sid=' + id);
											self.e.code.val(id);
										});
									}
								},
								onchange = function(){
									$(this).removeClass('error');
									$(this).prev(".error-notice").remove();
								};

							captcha._init();

							$form.find('input, textarea').change(onchange).keyup(onchange);
						});
					})
				</script>
			<?
			return ob_get_clean();
		}

		function html(){
			$fields = '';

			$replace = array(
				'%FIELDS' => '',
				'%NAME' => $this->name,
			);
			foreach($this->fields as $field){
				$html = $field->html();
				$replace['%FIELDS'] .= $html;
				$replace['%FIELD_'.$field->getName()] = $html;
			}
			
			
			
			return self::getJSHTML().strtr($this->template, $replace);
		}

		function setTemplate($template){
			$this->template = $template;	
		}

		function execute(){/* ... */}
	}