<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?foreach ($arResult["ITEMS"] as $id => $item) : ?>
<?
$this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_EDIT"));
$this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
?>
<h3><?=$item["NAME"]?>&nbsp;
    <img src="/images/vakanciLable.png"/>&nbsp;
    <span class="vakanciPrice"><?=$item["ZP"]?></span>&nbsp;
    <sup class="vakanciNominal">руб.</sup>
</h3>
<table class="vakancyTable" id="<?=$this->GetEditAreaId($item['ID']);?>">
    <tr>
        <td class="vakancyTableTitle">Требования </td>
        <td class="vakancyTableDesc"><?=$item["A_REQURIES"]?>
            <div class="detail" style="display: none;"><?=$item["P_REQURIES"]?></div>
        </td>
    </tr>
    <tr>
        <td class="vakancyTableTitle">Обязаности </td>
        <td class="vakancyTableDesc"><?=$item["A_RESPONSIBILITY"]?>
            <div class="detail" style="display: none;"><?=$item["P_RESPONSIBILITY"]?> </div>
        </td>
    </tr>
    <tr>
        <td class="vakancyTableTitle">Условия </td>
        <td class="vakancyTableDesc"><?=$item["A_CONDITIONS"]?>
            <div class="detail" style="display: none;"><?=$item["P_CONDITIONS"]?></div>
        </td>
    </tr>
</table>
<ul class="tableNav">
    <li><a class="vacancy_slider">ПОКАЗАТЬ&nbsp;<img src="/images/tableShow.png"/></a></li>
    <li><a rel="sendResume" name="signup" href="#sendResume">ОТПРАВИТЬ РЕЗЮМЕ</a></li>
</ul>
<hr class="dottedLine"/>
<?endforeach;?>

<div id="order" style="display: none; position: fixed; opacity: 1; z-index: 11000; left: 50%; margin-left: -202px; top: 100px;">
    <div class="sendResumeBody">
        <img class="modal_close" src="//images/closeModal.png"/>
        <div class="modal_header">
            <h3>ПРЕДВАРИТЕЛЬНЫSЙ ЗАКАЗ ТОВАРА</h3>
        </div>
        <form method="post" action="index.php">
        <table>
            <tr>
                <td>Название компании :</td>
                <td><input type="text" name="" placeholder=""/></td>
            </tr>
            <tr>
                <td>Контактное лицо (Ф.И.О.)<span>*</span>:</td>
                <td><input type="text" name="" placeholder=""/></td>
            </tr>
            <tr>
                <td>Откуда вы узнали о нас?:</td>
                <td><div class="styled-select"><select ><option>Интернет</option><option>Друг</option></select></div></td>
            </tr>
            <tr>
                <td>Какую технику вы хотите приобрести?<span>*</span>:</td>
                <td><div class="styled-select"><select ><option>Погрузчик</option><option>Трактор</option></select></div></td>
            </tr>
            <tr>
                <td>Являлись ли вы нашим клиентом?:</td>
                <td class="radioCeil">
                    <input type="radio"id="c1" name="cc" />
                    <label for="c1"><span></span>Да</label>
                    <input type="radio"id="c2" name="cc" checked="checked"/>
                    <label for="c2"><span></span>Нет, нохотел бы стать</label>
                </td>
            </tr>
            <tr>
                <td>Телефон<span>*</span>:</td>
                <td><input type="text" name=""/></td>
            </tr>
            <tr>
                <td>Email<span>*</span>:</td>
                <td><input type="text" name=""/></td>
            </tr>
            <tr>
                <td>Комментарий к заказу :</td>
                <td><textarea name=""></textarea></td>
            </tr>
        </table>
        <div class="nesesField"><span>*</span>&nbsp;- Поля обязательны к заполнению</div>
        <div><a href="#" class="sliderButton">ЗАКАЗАТЬ</a></div> 
        </form>
     </div>
</div>
<div id="sendResume" style="display: none; position: fixed; opacity: 1; z-index: 11000; left: 50%; margin-left: -202px; top: 100px;">
    <div class="sendResumeBody">
        <img class="modal_close" src="/images/closeModal.png"/>
        <div class="modal_header">
            <h3>ОТПРАВКА РЕЗЮМЕ</h3>
        </div>
        <form method="post" action="index.php">
        <table>
            <tr class="active">
                <td>Ф.И.О.<span>*</span>:</td>
                <td><input type="text" name="" placeholder=""/></td>
            </tr>
            <tr class="mustFill">
                <td>Телефон<span>*</span>:</td>
                <td><input type="text" name="" value="Обязательно к заполнению!"/></td>
            </tr>
            <tr>
                <td>Кратко о себе :</td>
                <td><textarea name=""></textarea></td>
            </tr>
            <tr>
                <td>Когда сможете приступить к работе?<span>*</span>:</td>
                <td><input type="text" name="" placeholder=""/></td>
            </tr>
        </table>
        <a href="#" class="resumeFix">Прикрепить резюме&nbsp;<img src="/images/resumeFix.png"/></a>
        <div class="nesesField"><span>*</span>&nbsp;- Поля обязательны к заполнению</div>
        <div><a href="#" class="sliderButton">ОТПРАВИТЬ</a></div> 
        </form>
     </div>
</div>