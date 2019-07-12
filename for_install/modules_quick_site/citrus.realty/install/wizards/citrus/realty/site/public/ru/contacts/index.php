<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?><p>
	Обратитесь к нашим специалистам и получите профессиональную консультацию по вопросам покупки, продажи, аренды или другим сделкам с недвижимостью.
</p>
<p>
	Вы можете обратиться к нам по телефону, по электронной почте или договориться о встрече в нашем офисе. Будем рады помочь вам и ответить на все ваши вопросы.
</p>
<?$APPLICATION->IncludeComponent(
	"citrus:realty.contacts",
	"offices",
	array()
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>