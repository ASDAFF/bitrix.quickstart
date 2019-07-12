<?
header("Content-type: text/html; charset=windows-1251");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if($_POST["name"] != "" && $_POST["phone"] != "" && $_POST["email"] != "" && $_POST["message"] != "")
{
    $rsSites                = CSite::GetByID(SITE_ID);
    $arSite                 = $rsSites->Fetch();
    $arSite["SERVER_NAME"]  = str_replace("http://", "", $arSite["SERVER_NAME"]);
    $arSite["SERVER_NAME"]  = str_replace("www.", "", $arSite["SERVER_NAME"]);
    
    if(CModule::IncludeModuleEx("v1rt.personal"))
    {
        $email = COption::GetOptionString("v1rt.personal", "v1rt_personal_email");
        if($email != "")
            $strEmail = $email;
        elseif($arSite["EMAIL"] != "")
            $strEmail = $arSite["EMAIL"];
        else
        {
            echo -1;
            return;
        }
    }
    
    $newline = "\r\n";
    $subject = "Форма обратной связи сайта";
    $header  = "Content-type: text/html; charset=windows-1251".$newline;
    $header .= "From: feedback@".$arSite["SERVER_NAME"].$newline;
    $header .= "Subject: $subject".$newline;
    $msg = '        <h1>Форма обратной связи</h1>
                    <table>
                        <tr>
                            <td><strong>Имя: </strong></td>
                            <td style="padding-left: 20px;">'.iconv("UTF-8", "windows-1251", strip_tags($_POST["name"])).'</td>
                        </tr>
                        <tr>
                            <td><strong>Телефон: </strong></td>
                            <td style="padding-left: 20px;">'.iconv("UTF-8", "windows-1251", strip_tags($_POST["phone"])).'</td>
                        </tr>
                        <tr>
                            <td><strong>Email: </strong></td>
                            <td style="padding-left: 20px;">'.iconv("UTF-8", "windows-1251", strip_tags($_POST["email"])).'</td>
                        </tr>
                        <tr>
                            <td><strong>Сообщение: </strong></td>
                            <td style="padding-left: 20px;">'.iconv("UTF-8", "windows-1251", nl2br(strip_tags($_POST["message"]))).'</td>
                        </tr>
                    </table>';
    if(mail($strEmail, $subject, $msg, $header))
        echo 1;
    else
        echo 0;
}
else
    echo -1;
?>