<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>

<style>
    .timetable_sk .comment{
        font-size: 10pt;
        padding-top: 5px;
    }
    .timetable_sk h3{
        text-align: center;
        color: <?=$arParams["COLOR_TABLE_H3"]?>;
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

    /*.eventName {*/
        /*text-decoration: underline;*/
    /*}*/
</style>

<?


if($arParams["DISPLAY_TOP_PAGER"]):?>
    <?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<div class="timetable_sk">
    <?foreach ($arResult["EVENTS"] as $item):?>
    <?if($item["DAY_OF_START"] != $lastDate):?>
        <?if($flg):?>
            </table>
        <?endif;?>
        <h3> <?=$item["DAY_OF_START"]?> </h3>

        <table>
            <?$flg = true?>
        <?endif;?>
            <tr><td class="first"><?=$item["TIME_OF_START"]?><br>-<br><?=$item["TIME_OF_END"]?></td><td><ins><?=$item["NAME"]?></ins><br> <div class="comment"><?=$item["DETAIL_TEXT"]?></div> </td><td class="second">
                <a href="<?echo $APPLICATION->GetCurPageParam("EID=".$item["ID"])?>"><?=GetMessage("SKDYLAN_TIMETABLE_ZAPISATQSA")?></a></td></tr>
        <?$lastDate = $item["DAY_OF_START"];?>
    <?endforeach;?>
     <?if($arResult["EVENTS"] != NULL):?>
        </table>
    <?endif;?>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
<?
    ?>
    <br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
<div id="overlay">
    <div class="popup">
        <?if($_COOKIE["success"] == true):?>
            <h2></h2>
            <p><?=GetMessage("SKDYLAN_TIMETABLE_VASA_ZAAVKA_USPESNO")?></p>
        <?endif;?>
        <?if($_COOKIE["success"] == 0):?>
            <h2><?=GetMessage("ERROR")?></h2>
            <p><?=GetMessage("SKDYLAN_ERROR_LIMIT")?></p>
        <?endif;?>
        <button class="close" title="<?=GetMessage("SKDYLAN_TIMETABLE_ZAKRYTQ")?>" onclick="document.getElementById('overlay').style.display='none';"></button>
    </div>
</div>

<?if(isset($_COOKIE["success"])):?>
<script type="text/javascript">
    var delay_popup = 1000;
    setTimeout("document.getElementById('overlay').style.display='block'", delay_popup);
</script>
<?endif;?>
