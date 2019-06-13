<?
class CComingsoon{

    function MyOnBeforePrologHandler()
    {
        if(defined("ADMIN_SECTION") || ADMIN_SECTION === true)
            return;

        $site_on_off = COption::GetOptionString("asdaff.comingsoon", "CS_checkbox_".SITE_ID);

        if($site_on_off == 'Y'){

            $time = COption::GetOptionString("asdaff.comingsoon", "CS_date_".SITE_ID);
            $auto = COption::GetOptionString("asdaff.comingsoon", "CS_auto_".SITE_ID, 'N');
            global $USER;

            if(strlen($time) > 0){

                // $date = "07.04.2005 11:32:00";
                // преобразуем ее в Unix-timestamp
                $open_time = MakeTimeStamp($time, "DD.MM.YYYY HH:MI:SS");
                if(time() > $open_time && $auto == 'Y'){
                    //

                }else{

                    $allow_group_string = COption::GetOptionString("asdaff.comingsoon", "CS_allow_user_".SITE_ID);
                    $arGroupAvalaible = explode(',', $allow_group_string); // массив групп, которые в которых нужно проверить доступность пользователя

                    $arGroups = CUser::GetUserGroup($USER->GetID()); // массив групп, в которых состоит пользователь
                    $result_intersect = array_intersect($arGroupAvalaible, $arGroups);// далее проверяем, если пользователь вошёл хотя бы в одну из групп, то позволяем ему что-либо делать
                    if(sizeof($result_intersect) <= 0 && !$USER->IsAdmin()){
                        include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/asdaff.comingsoon/site_closed.php");
                        die();
                    }
                }
            }
        }
    }




}