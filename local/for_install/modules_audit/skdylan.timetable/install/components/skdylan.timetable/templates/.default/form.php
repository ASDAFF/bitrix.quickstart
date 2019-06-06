<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>

<style>
    .timetable_sk .comment{
        font-size: 10pt;
        padding-top: 5px;
    }
    .timetable_sk h3{
        text-align: center;
        color: #669;
    }

    .timetable_sk table {
        background: white;
        width: 100%;
        border-collapse: separate;
        text-align: left;
        border-spacing: 3px 3px;
        font-size: 11pt;
        padding-bottom: 10px
    }
    .timetable_sk th {
        font-weight: normal;
        color: #039;
        border-bottom: 2px solid #6678b1;
        padding: 10px 8px;
    }
    .timetable_sk td {
        color: <?=$arParams["COLOR_TABLE_TEXT"]?>;
        padding: 9px 8px;
        transition: .3s linear;
        background: <?=$arParams["COLOR_TABLE"]?>;
    }
    .timetable_sk tr:hover td {color: <?=$arParams["COLOR_TABLE_TEXT_H"]?>;}

    .timetable_sk .first {
        width: 10%;
        text-align: center;
    }

    .timetable_sk .second {
        width: 20%;
        text-align: center;
    }

    .form-style-1 {
        margin:10px auto;
        max-width: 400px;
        padding: 20px 12px 10px 20px;
        font: 13px "Lucida Sans Unicode", "Lucida Grande", sans-serif;
    }
    .form-style-1 li {
        padding: 0;
        display: block;
        list-style: none;
        margin: 10px 0 0 0;
    }
    .form-style-1 label{
        margin:0 0 3px 0;
        padding:0px;
        display:block;
        font-weight: bold;
    }
    .form-style-1 input[type=text],
    .form-style-1 input[type=date],
    .form-style-1 input[type=datetime],
    .form-style-1 input[type=number],
    .form-style-1 input[type=search],
    .form-style-1 input[type=time],
    .form-style-1 input[type=url],
    .form-style-1 input[type=email],
    textarea,
    select{
        box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        border:1px solid #BEBEBE;
        padding: 7px;
        margin:0px;
        -webkit-transition: all 0.30s ease-in-out;
        -moz-transition: all 0.30s ease-in-out;
        -ms-transition: all 0.30s ease-in-out;
        -o-transition: all 0.30s ease-in-out;
        outline: none;
    }
    .form-style-1 input[type=text]:focus,
    .form-style-1 input[type=date]:focus,
    .form-style-1 input[type=datetime]:focus,
    .form-style-1 input[type=number]:focus,
    .form-style-1 input[type=search]:focus,
    .form-style-1 input[type=time]:focus,
    .form-style-1 input[type=url]:focus,
    .form-style-1 input[type=email]:focus,
    .form-style-1 textarea:focus,
    .form-style-1 select:focus{
        -moz-box-shadow: 0 0 8px #516d7b;
        -webkit-box-shadow: 0 0 8px #516d7b;
        box-shadow: 0 0 8px #516d7b;
        border: 1px solid #516d7b;
    }
    .form-style-1 .field-divided{
        width: 49%;
    }

    .form-style-1 .field-long{
        width: 100%;
    }
    .form-style-1 .field-select{
        width: 100%;
    }
    .form-style-1 .field-textarea{
        height: 100px;
    }
    .form-style-1 input[type=submit], .form-style-1 input[type=button]{
        background:  #516d7b;
        padding: 8px 15px 8px 15px;
        border: none;
        color: #fff;
    }
    .form-style-1 input[type=submit]:hover, .form-style-1 input[type=button]:hover{
        background: #516d7b;
        box-shadow:none;
        -moz-box-shadow:none;
        -webkit-box-shadow:none;
    }
    .form-style-1 .required{
        color:red;
    }

    .popup-window{
        width: 400px;
    }

    #overlay {
        position: fixed;
        top: 0;
        left: 0;
        display: none;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.65);
        z-index: 999;
        -webkit-animation: fade .6s;
        -moz-animation: fade .6s;
        animation: fade .6s;
        overflow: auto;
    }

    .popup {
        top: 25%;
        left: 0;
        right: 0;
        font-size: 14px;
        margin: auto;
        width: 85%;
        min-width: 300px;
        max-width: 300px;
        position: absolute;
        padding: 15px 20px;
        border: 1px solid #383838;
        background: #fefefe;
        z-index: 1000;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        -ms-border-radius: 4px;
        border-radius: 4px;
        font: 14px/18px 'Tahoma', Arial, sans-serif;
        -webkit-box-shadow: 0 15px 20px rgba(0,0,0,.22),0 19px 60px rgba(0,0,0,.3);
        -moz-box-shadow: 0 15px 20px rgba(0,0,0,.22),0 19px 60px rgba(0,0,0,.3);
        -ms-box-shadow: 0 15px 20px rgba(0,0,0,.22),0 19px 60px rgba(0,0,0,.3);
        box-shadow: 0 15px 20px rgba(0,0,0,.22),0 19px 60px rgba(0,0,0,.3);
        -webkit-animation: fade .6s;
        -moz-animation: fade .6s;
        animation: fade .6s;
    }

    .close {
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        position: absolute;
        border: none;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        -ms-border-radius: 50%;
        -o-border-radius: 50%;
        border-radius: 50%;
        background-color: rgba(0, 0, 0, 0.65);
        -webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        -moz-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        cursor: pointer;
        outline: none;

    }
    .close:before {
        color: rgba(255, 255, 255, 0.9);
        content: "X";
        font-family:  Arial, Helvetica, sans-serif;
        font-size: 14px;
        font-weight: normal;
        text-decoration: none;
        text-shadow: 0 -1px rgba(0, 0, 0, 0.9);
        -webkit-transition: all 0.5s;
        -moz-transition: all 0.5s;
        transition: all 0.5s;
    }
    /* ������ �������� ��� ��������� */
    .close:hover {
        background-color: rgba(252, 20, 0, 0.8);
    }

    .form-style-1 {
        margin:10px auto;
        max-width: 400px;
        padding: 0px 12px 10px 20px;
        font: 13px "Lucida Sans Unicode", "Lucida Grande", sans-serif;
    }
    .form-style-1 li {
        padding: 0;
        display: block;
        list-style: none;
        margin: 10px 0 0 0;
    }
    .form-style-1 label{
        margin:0 0 3px 0;
        padding:0px;
        display:block;
        font-weight: bold;
    }
    .form-style-1 input[type=text],
    .form-style-1 input[type=date],
    .form-style-1 input[type=datetime],
    .form-style-1 input[type=number],
    .form-style-1 input[type=search],
    .form-style-1 input[type=time],
    .form-style-1 input[type=url],
    .form-style-1 input[type=email],
    textarea,
    select{
        box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        border:1px solid #BEBEBE;
        padding: 7px;
        margin:0px;
        -webkit-transition: all 0.30s ease-in-out;
        -moz-transition: all 0.30s ease-in-out;
        -ms-transition: all 0.30s ease-in-out;
        -o-transition: all 0.30s ease-in-out;
        outline: none;
    }
    .form-style-1 input[type=text]:focus,
    .form-style-1 input[type=date]:focus,
    .form-style-1 input[type=datetime]:focus,
    .form-style-1 input[type=number]:focus,
    .form-style-1 input[type=search]:focus,
    .form-style-1 input[type=time]:focus,
    .form-style-1 input[type=url]:focus,
    .form-style-1 input[type=email]:focus,
    .form-style-1 textarea:focus,
    .form-style-1 select:focus{
        -moz-box-shadow: 0 0 8px <?=$arParams['COLOR_I']?>;
        -webkit-box-shadow: 0 0 8px <?=$arParams['COLOR_I']?>;
        box-shadow: 0 0 8px <?=$arParams['COLOR_I']?>;
        border: 1px solid <?=$arParams['COLOR_I']?>;
    }
    .form-style-1 .field-divided{
        width: 49%;
    }

    .form-style-1 .field-long{
        width: 100%;
    }
    .form-style-1 .field-select{
        width: 100%;
    }
    .form-style-1 .field-textarea{
        height: 100px;
    }
    .form-style-1 input[type=submit], .form-style-1 input[type=button]{
        background:  <?=$arParams['COLOR_B']?>;
        padding: 8px 15px 8px 15px;
        border: none;
        color: #fff;
    }
    .form-style-1 input[type=submit]:hover, .form-style-1 input[type=button]:hover{
        background: <?=$arParams['COLOR_B']?>;
        box-shadow:none;
        -moz-box-shadow:none;
        -webkit-box-shadow:none;
    }
    .form-style-1 .required{
        color:red;
    }

    .errorInput p{
        color: red;
        margin-top: 2px;
        margin-bottom: 5px;
    }
</style>

<div class="timetable_sk">
    <h3> <?=$arResult["EVENTS"][0]["DAY_OF_START"]?> </h3>
    <table>
    <tr><td class="first"><?=$arResult["EVENTS"][0]["TIME_OF_START"]?> <br>-<br> <?=$arResult["EVENTS"][0]["TIME_OF_END"]?></td><td><ins><?=$arResult["EVENTS"][0]["NAME"]?></ins><br> <div class="comment"><?=$arResult["EVENTS"][0]["DETAIL_TEXT"]?></div> </td></tr>
    </table>

</div>
<div class="errorInput">
    <?if($arResult["ErrorInput"]["FullName"]):?>
    <p><?=GetMessage("SKDYLAN_TIMETABLE_VVEDITE_VASE_FIO")?></p>
    <?endif;?>
    <?if($arResult["ErrorInput"]["Email"]):?>
        <p><?=GetMessage("SKDYLAN_TIMETABLE_VVEDITE_VASE")?></p>
    <?endif;?>
    <?if($arResult["ErrorInput"]["Phone"]):?>
        <p><?=GetMessage("SKDYLAN_TIMETABLE_VVEDITE_VASE_NOMER_T")?></p>
    <?endif;?>
    <?if($arResult["ErrorInput"]["Limit"]):?>
        <p><?=GetMessage("SKDYLAN_TIMETABLE_LIMIT")?></p>
    <?endif;?>
    <?if($arResult["ErrorInput"]["PhoneIsUse"]):?>
        <p><?=GetMessage("SKDYLAN_TIMETABLE_PHONEISUSE")?></p>
    <?endif;?>
    <?if($arResult["ErrorInput"]["EmailIsUse"]):?>
        <p><?=GetMessage("SKDYLAN_TIMETABLE_EMAILISUSE")?></p>
    <?endif;?>
</div>
<?

?>
<?if(!$arResult["LIMIT"]):?>
<form action="<?=POST_FORM_ACTION_URI?>" method="POST">
    <ul class="form-style-1">
        <li><label><?=GetMessage("SKDYLAN_TIMETABLE_FIO")?><span class="required"><?if(array_search('FullName', $arParams['FIELDS']) !== false):?>*<?endif;?></span></label>
            <input type="text" name="FullName" class="field-long" placeholder="" value="<?=$arResult["fullName"]?>" /></li>
        <li>
            <label>Email <span class="required"><?if(array_search('Email', $arParams['FIELDS']) !== false):?>*<?endif;?></span></label>
            <input type="email" name="Email" class="field-long" value="<?=$arResult["Email"]?>" />
        </li>
        <li>
            <label><?=GetMessage("SKDYLAN_TIMETABLE_TELEFON")?><span class="required"><?if(array_search('Phone', $arParams['FIELDS']) !== false):?>*<?endif;?></span></label>
            <input type="text" name="Phone" class="field-long" value="<?=$arResult["Phone"]?>"/>
        </li>
        <li>
            <label><?=GetMessage("SKDYLAN_TIMETABLE_KOMMENTARIY")?><span class="required"><?if(array_search('Comment', $arParams['FIELDS']) !== false):?>*<?endif;?></span></label>
            <textarea name="Comment" id="Comment" class="field-long field-textarea"><?=$arResult["Comment"]?></textarea>
        </li>
        <li>
            <input type="submit" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>"/>
        </li>
    </ul>
    <input type="hidden" name="EID" value="<?=$arResult['EID']?>">
    <input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
</form>
<?else:?>
    <div class="errorInput">
        <p><?=GetMessage("LIMIT_IS_OVER")?></p>
    </div>
<?endif;?>