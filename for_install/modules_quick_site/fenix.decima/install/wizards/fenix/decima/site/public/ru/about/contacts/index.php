<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контактная информация");
?>
    <section class="row">
      <div class="col-md-8">
        <div class="section-header col-xs-12">
          <hr>
          <h2 class="strong-header">
            Свяжитесь с нами
          </h2>
        </div>
        <div class="col-xs-12">
          <p>Если у вас есть к нам вопросы, оставьте свои контактные данные и наш специалист свяжется с вами</p>

         <?$APPLICATION->IncludeComponent("bitrix:main.feedback", "feedback", array(
    "USE_CAPTCHA" => "N",
    "OK_TEXT" => "Спасибо, ваше сообщение принято.",
    "EMAIL_TO" => COption::GetOptionString("sale", "order_email", "order@".$_SERVER['HTTP_HOST']),
    "REQUIRED_FIELDS" => array(
        0 => "NONE",
    ),
    "EVENT_MESSAGE_ID" => array(
        0 => "7",
    )
    ),
    false
);?>
        </div>
      </div>
      <div class="col-md-4">
        <div class="space-30"></div>
        <div class="section-emphasis-3 page-info">
          <h3 class="strong-header">
            Контакты
          </h3>

          <div class="text-widget">
            <address>
              <a href="mailto:hello@decima.com">info@finixit.ru</a><br>
              (+7)920 322-47-17
            </address>
          </div>
          <br>
          <h3 class="strong-header">
            Адрес
          </h3>

          <div class="text-widget">
            <address>
              г. Москва, <br>
              ул. академика королева, д. 12
            </address>
          </div>

<br>
          <h3 class="strong-header">
            Режим работы
          </h3>

          <div class="text-widget">
            <address>
              понедельник–пятница: с 9:00 до 18:00<br>
              суббота–воскресенье: выходные
            </address>
          </div>
        </div>
      </div>
    </section>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>
