<?php echo "<?xml version=\"1.0\" encoding=\"windows-1251\"?".">"; ?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
global $APPLICATION;



$cp=$this->__component;

if(is_object($cp))
{

    if($_POST["flag"])
    {
        if(!empty($_POST["fphone"]))
        {
            $server=(string)$_SERVER['HTTP_HOST'];

            $linksrc=$server."{$arResult['DETAIL_PAGE_URL']}";

            $mainlink="<a  href={$linksrc} >".$arResult['NAME']."</a>";

            $arEventFieldsAdm = array(

                "USERNAME"=>htmlspecialchars(stripslashes($_POST['fname'])),
                "USEREMAIL"=>htmlspecialchars(stripslashes($_POST["femail"])),
                "NAMEITEM"=>$arResult["NAME"],
                "PHONE"=>htmlspecialchars(stripslashes($_POST["fphone"])),
                "PRICEITEM"=>$arResult["DISPLAY_PROPERTIES"]["PRICE"]["DISPLAY_VALUE"].GetMessage("MYCOMPANY_REMO_RUB"),
                "LINKITEM"=>$mainlink


            );




            $arFilter1=array(
                "TYPE_ID" => "eletro_mess",
                "SUBJECT"=>GetMessage("MYCOMPANY_REMO_ZAKAZ")

            );

            $resObj1=CEventMessage::GetList($by="site_id", $order="desc", $arFilter1);

            $result1=$resObj1->Fetch();



            CEvent::Send("eletro_mess", SITE_ID, $arEventFieldsAdm,'N',$result1['ID']);

            $arEventFieldsUser=array(

                "USERNAME"=>htmlspecialchars(stripslashes($_POST['fname'])),
                "USEREMAIL"=>htmlspecialchars(stripslashes($_POST["femail"])),
                "NAMEITEM"=>$arResult["NAME"],
                "PHONE"=>htmlspecialchars(stripslashes($_POST["fphone"])),
                "PRICEITEM"=>$arResult["DISPLAY_PROPERTIES"]["PRICE"]["DISPLAY_VALUE"].GetMessage("MYCOMPANY_REMO_RUB"),
                "LINKITEM"=>$mainlink


            );

            $arFilter=array(
                "TYPE_ID" => "eletro_mess",
                "SUBJECT"=>GetMessage("MYCOMPANY_REMO_PISQMO_POLQZOVATELU")

            );

            $resObj=CEventMessage::GetList($by="site_id", $order="desc", $arFilter);

            $result=$resObj->Fetch();





            CEvent::Send("eletro_mess", SITE_ID, $arEventFieldsUser,'N',$result['ID']);
            $cp->arResult['fstatus']=GetMessage("MYCOMPANY_REMO_VASA_ZAAVKA_PRINATA");


        }
        else
        {
            $cp->arResult['estatus']=GetMessage("MYCOMPANY_REMO_VY_NE_UKAZALI_TELEFO");
        }

    }
    /*
    else
     {
          $cp->arResult['estatus'].="Пост";
         }
        */

}
?>


