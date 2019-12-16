<?
IncludeModuleLangFile(__FILE__);

class rsActiveLife
{
    function InstallModuleOptions()
    {
        COption::SetOptionInt("redsign.activelife", "click_protection_delay", 1500 );
        COption::SetOptionInt("redsign.activelife", "request_delay", 250 );
        COption::SetOptionString("redsign.activelife", "show_mouse_loading", "Y" );
        COption::SetOptionString("redsign.activelife", "propcode_color", "SKU_COLOR" );
        COption::SetOptionString("redsign.activelife", "propcode_brands", "MAKER" );
        COption::SetOptionString("redsign.activelife", "propcode_brands_img", "MAKER_LOGO" );
        COption::SetOptionString("redsign.activelife", "propcode_size", "SKU_SIZE" );
        COption::SetOptionString("redsign.activelife", "filter_price_view", "slider" );
        
        $arrColors = array(
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_00AEEF"),
                "RGB" => "#00AEEF",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_FFF100"),
                "RGB" => "#FFF100",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_56685C"),
                "RGB" => "#56685C",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_D91557"),
                "RGB" => "#D91557",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_EEEADE"),
                "RGB" => "#EEEADE",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_D3ACA5"),
                "RGB" => "#D3ACA5",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_EF6139"),
                "RGB" => "#EF6139",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_2A2F43"),
                "RGB" => "#2A2F43",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_4A4444"),
                "RGB" => "#4A4444",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_93BD59"),
                "RGB" => "#93BD59",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_1E4EA2"),
                "RGB" => "#1E4EA2",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_B9A216"),
                "RGB" => "#B9A216",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_00A650"),
                "RGB" => "#00A650",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_EDACBF"),
                "RGB" => "#EDACBF",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_3B251A"),
                "RGB" => "#3B251A",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_EE1D24"),
                "RGB" => "#EE1D24",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_000000"),
                "RGB" => "#000000",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_004A80"),
                "RGB" => "#004A80",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_ACACAC"),
                "RGB" => "#ACACAC",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_FFFFFF"),
                "RGB" => "#FFFFFF",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_CCB9AA"),
                "RGB" => "#CCB9AA",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_EBEBEB"),
                "RGB" => "#EBEBEB",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_1E1E20"),
                "RGB" => "#1E1E20",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_3C230D"),
                "RGB" => "#3C230D",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_91278F"),
                "RGB" => "#91278F",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_81CA9D"),
                "RGB" => "#81CA9D",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_7A0026"),
                "RGB" => "#7A0026",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_00A86B"),
                "RGB" => "#00A86B",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_A1410D"),
                "RGB" => "#A1410D",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_EBDAC6"),
                "RGB" => "#EBDAC6",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_E0B0FF"),
                "RGB" => "#E0B0FF",
            ),
            array(
                "NAME" => GetMessage("RSBS_COLOR_NAME_9B8793"),
                "RGB" => "#9B8793",
            ),
        );

        $c = 0;
        foreach($arrColors as $arColor)
        {
            COption::SetOptionString("redsign.activelife", "color_table_name_".$c, $arColor["NAME"] );
            COption::SetOptionString("redsign.activelife", "color_table_rgb_".$c, $arColor["RGB"] );
            $c++;
        }
        COption::SetOptionString("redsign.activelife", "color_table_count", $c);
    }

    function ShowPanel()
    {
        if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "redsign.activelife")
        {
            $GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/wizards/redsign/activelife/css/panel.css"); 

            $arMenu = Array(
                Array(        
                    "ACTION" => "jsUtils.Redirect([], '".CUtil::JSEscape("/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardSiteID=".SITE_ID."&wizardName=redsign:activelife&".bitrix_sessid_get())."')",
                    "ICON" => "bx-popup-item-wizard-icon",
                    "TITLE" => GetMessage("STOM_BUTTON_TITLE_W1"),
                    "TEXT" => GetMessage("STOM_BUTTON_NAME_W1"),
                )
            );

            $GLOBALS["APPLICATION"]->AddPanelButton(array(
                "HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=redsign:activelife&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
                "ID" => "activelife_wizard",
                "ICON" => "bx-panel-site-wizard-icon",
                "MAIN_SORT" => 2500,
                "TYPE" => "BIG",
                "SORT" => 10,    
                "ALT" => GetMessage("SCOM_BUTTON_DESCRIPTION"),
                "TEXT" => GetMessage("SCOM_BUTTON_NAME"),
                "MENU" => $arMenu,
            ));
        }
    }
}
?>