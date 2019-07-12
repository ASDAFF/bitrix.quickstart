    <section id="contact" class="caption-margin">
      <div class="container">
        
          <!-- SECTION TITLE -->
          <h2 class="anim-fade-down">
            
            <!-- TITLE -->
            Форма обратной связи<br/>
            
            <!-- SUBTITLE -->
            <span>напишите нам</span>
            
          </h2>
          
          <!-- CONTACT FORM -->
          <?$APPLICATION->IncludeComponent("akropol:feedback.main", "feedback", Array(
	"COMPONENT_TEMPLATE" => ".default",
		"FORM_TITLE" => "",	// Заголовок формы
		"FORM_TITLE_TYPE" => "H2",	// Тип заголовка
		"FORM_TITLE_SIZE" => "5",	// Величина заголовка
		"USE_CAPTCHA" => "N",	// Использовать защиту от автоматических сообщений (CAPTCHA) для неавторизованных пользователей
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",	// Сообщение, выводимое пользователю после отправки
		"EMAIL_TO" => "#MAIL#",	// E-mail, на который будет отправлено письмо
		"USED_FIELDS" => array(	// Выводить поля
			0 => "NAME",
			1 => "PHONE",
			2 => "EMAIL",
			3 => "MESSAGE",
		),
		"REQUIRED_FIELDS" => array(	// Обязательные поля для заполнения
			0 => "NAME",
			1 => "EMAIL",
			2 => "MESSAGE",
		),
		"EVENT_MESSAGE_ID" => array(	// Почтовые шаблоны для отправки письма
			0 => "#EMAIL_TEMPLATE_ID#",
		),
		"USE_IN_COMPONENT" => "Y",	// Используется внутри другого компонента (или включаемой области)
		"MESSAGE_HIDTH" => "10",	// Высота поля "Сообщение" (число строк)
		"BUTTON_MESSAGE" => "Написать",	// Текст кнопки отправки
		"PROPERTY_CODE_BUTTON_COLOR" => "btn-primary",	// Цвет кнопоки
		"NAME_HINT_TITLE" => "Ваше имя",	// Заголовок для поля ИМЯ
		"NAME_HINT_TEXT" => "Ваше имя",	// Подсказка в поле ИМЯ
		"EMAIL_HINT_TITLE" => "Ваш e-mail",	// Заголовок в поле email
		"EMAIL_HINT_TEXT" => "email",	// Подсказка в поле email
		"PHONE_HINT_TITLE" => "Ваш телефон",	// Заголовок в поле телефон
		"PHONE_HINT_TEXT" => "+7(926)123-45-67",	// Подсказка в поле телефон
		"MESSAGE_HINT_TITLE" => "Сообщение",	// Заголовок в поле Сообщение
	),
	false
);?>
      </div>