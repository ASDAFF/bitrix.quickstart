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
    <h3>��� ������� ������?</h3>
    <p>�������� ������ �� �����. ����������� �� ��������, � ������� ����� �� ������, ��� ����� � ����� ���� ��������</p>
    <form action="">
        <div class="name-cont"><input type="text" placeholder="���� ���"></div>
        <div class="email-cont"><input type="text" placeholder="���� E-mail"></div>
        <div class="submit-cont">
            <button type="button" name="button">���������</button>
        </div>
        <div class="clearfix"></div>
    </form>
</div>
*/?>

<p><?=$arResult['DETAIL_TEXT'];?></p>

<?/*
<div class="subscibe-form1">
    <h3>����������� �� �������� ������ �����</h3>
    <p>����������� �� ��������, � ������� ����� �� ������, ��� ����� � ����� ���� ��������</p>
    <form action="">
        <div class="name-cont"><input type="text" placeholder="���� ���"></div>
        <div class="email-cont"><input type="text" placeholder="���� E-mail"></div>
        <div class="submit-cont">
            <button type="button" name="button">�����������</button>
        </div>
        <div class="clearfix"></div>
    </form>
</div>
*/?>

<div class="share-block">
    <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/share.php", "EDIT_TEMPLATE" => "" ), false );?>
</div>