<?
/*
	Это пример шаблона печати акта приема-передачи и заказов.
	Его можно использовать для создания своего шаблона, однако стоит учитывать, что он не расчитан на большой список заказов (может просто не уместиться на одном листе).
	Печать заказов заключается в печати штрихкодов для каждого заказа.
	
	Перед использованием проверьте, чтобы файл был в той же кодировке, что и сайт, иначе русские символы будут искажены. В первую очередь это относится к сайтам с UTF-кодировкой.
	
	В рамках техподдержки не рассматриваются вопросы по верстке, программированию и подстраиванию этого шаблона под сайты.
*/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$SALE_RIGHT = $APPLICATION->GetGroupRight("sale");
if($SALE_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$module_id = "ipol.sdek";
CModule::IncludeModule($module_id);

$shpName=COption::GetOptionString($module_id,'strName','');

if(CModule::IncludeModule("sale")){
	extract(sdekOption::formActArray());

	if(is_array($arOrders) && count($arOrders) > 0){
		$arDate = array(
			1  => ' января ',
			2  => ' февраля ',
			3  => ' марта ',
			4  => ' апреля ',
			5  => ' мая ',
			6  => ' июня ',
			7  => ' июля ',
			8  => ' августа ',
			9  => ' сентября ',
			10 => ' октября ',
			11 => ' ноября ',
			12 => ' декабря ',
		);
		$actDate = date("d").$arDate[date("m")].date("Y");
	?>
		<!-- Будущий шаблон акта --> 					
			<style type="text/css">
			/* акт */
			<!--
				@page { size: 21cm 29.7cm; margin-left: 1cm; margin-right: 1cm; margin-top: 1cm; margin-bottom: 1cm }
				P { margin-bottom: 0.21cm; direction: ltr; color: #000000; widows: 2; orphans: 2 }
			-->
			div.block {
				width: 100%;
				clear: right;
				min-height: 100px;
				page-break-after:always;
			}
			div.block:last-child{
				page-break-after:auto;
			}
			.header{
				text-align:center;
				font-weight:bold;
			}
			.right{
				text-align:right;
			}
			.text8{
				font-size: 8pt;
			}
			.breaker{
				height: 1cm;
			}
			</style>
			<div class="block">			
				<p class='header'>АКТ ПРИЕМА-ПЕРЕДАЧИ №_______ от <?=$actDate?> г.</p>
				<p><br></p>
				<p>__________, именуемое в дальнейшем ИСПОЛНИТЕЛЬ, в лице __________, действующего на основании ____ от ____  г., приняло в рамках исполнения Договора №___ от ____ г. приняло от __________, именуемого в дальнейшем Клиент, в лице __, действующего на основании ___, следующие Отправления для последующей доставки получателям:</p>
				<p><br></p>
				<table BORDER='1' BORDERCOLOR="#00000a" CELLPADDING='2' CELLSPACING='0'>
					<tr VALIGN='TOP'>
						<td><p>№</p></td>
						<td><p>Номер Заказа</p></td>
						<td><p>Номер Отправления</p></td>
						<td><p>Вес Отправления (кг)</p></td>
						<td><p>Стоимость Отправления (руб.)</p></td>
						<td><p>Итого к оплате (руб.)</p></td>
					</tr>
					<?foreach($arOrders as $key => $arOrder){?>
						<TR VALIGN='TOP'>
							<td><p><?=$key+1?></p></td>
							<td><p><?=$arOrder['ID']?></p></td>
							<td><p><?=$arOrder['SDEKID']?></p></td>
							<td><p><?=$arOrder['WEIGHT']?></p></td>
							<td><p><?=$arOrder['PRICE']?></p></td>
							<td><p><?=$arOrder['TOPAY']?></p></td>
						</TR>
					<?}?>
				</TABLE>
				<p><strong>Общее количество отправлений <?=count($arOrders)?> единиц.</strong></p>
				<p><strong>Общая сумма объявленной стоимости <?=$ttlPay?> руб.</strong></p>
				<p><br></p>
				<table BORDER=0 CELLPADDING=9 CELLSPACING=0>
					<tr VALIGN=TOP>
						<td WIDTH=381><p class='text8'>Сдал:</p></td>
						<td WIDTH=381><p class='text8'>Принял:</p></td>
					</tr>
					<tr VALIGN=TOP>
						<td>
							<p class='text8'>Должность ____________________________</p>
							<p class='text8'>ФИО _________________________________</p>
							<p class='text8'>Подпись ______________________________</p>
							<p><BR></p>
						</td>
						<td>
							<p class='text8'>Должность ____________________________</p>
							<p class='text8'>ФИО _________________________________</p>
							<p class='text8'>Подпись ______________________________</p>
							<p><BR></p>
						</td>
					</tr>
				</table>
				<p class='breaker'></p>
			</div>
		<?}
	}
?>