<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?if (!empty($arResult['DETAIL_PICTURE']['ID'])){?>
    <?$res_detail = CFile::ResizeImageGet($arResult['DETAIL_PICTURE']['ID'], array("width" => 300, "height" => 218), BX_RESIZE_IMAGE_EXACT, true);?>
    <img class="detail_img" src="<?=$res_detail['src']?>" alt="<?=$arResult['NAME']?>"/>
<?}?>

<?/*
<br/>
<br/>
<br/>

<div class="subscibe-form1">
    <h3>Нет времени читать?</h3>
    <p>Отправте статью на почту. Подпишитесь на рассылку, и станьте одним из первых, кто будет в курсе всех новостей</p>
    <form action="">
        <div class="name-cont"><input type="text" placeholder="Ваше имя"></div>
        <div class="email-cont"><input type="text" placeholder="Ваше E-mail"></div>
        <div class="submit-cont">
            <button type="button" name="button">Отправить</button>
        </div>
        <div class="clearfix"></div>
    </form>
</div>
*/?>

<p><?=$arResult['DETAIL_TEXT'];?></p>

<?/*
<div class="subscibe-form1">
    <h3>Подпишитесь на рассылку нашего сайта</h3>
    <p>Подпишитесь на рассылку, и станьте одним из первых, кто будет в курсе всех новостей</p>
    <form action="">
        <div class="name-cont"><input type="text" placeholder="Ваше имя"></div>
        <div class="email-cont"><input type="text" placeholder="Ваше E-mail"></div>
        <div class="submit-cont">
            <button type="button" name="button">Подписаться</button>
        </div>
        <div class="clearfix"></div>
    </form>
</div>
*/?>

<div class="share-block">
    <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/share.php", "EDIT_TEMPLATE" => "" ), false );?>
</div>