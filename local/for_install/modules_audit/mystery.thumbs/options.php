<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/mystery.thumbs/prolog.php');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mystery.thumbs/include.php");

if ($_POST['DELETE_OLD_THUMBS'] == 'Y') {
    // delete old thumbs
    DeleteDirFilesEx ( '/upload/resize_cache/mystery.thumbs/' ); // old thumb images
    DeleteDirFilesEx ( '/thumb/' ); // new thumb images (from v1.0.1)
}

IncludeModuleLangFile ( __FILE__ );
$module_id = "mystery.thumbs";
$arAllOptions = $arDisplayOptions = array (
    array (
        "HEADING",
        GetMessage ( 'MYSTERY_THUMBS_HEADING_MAIN' )
    ),
    array (
        "JPG_QUALITY",
        GetMessage ( 'MYSTERY_THUMBS_JPG_QUALITY' ),
        array (
            "text",
            23
        )
    ),
    array (
        "BACKGROUND_COLOR",
        GetMessage ( 'MYSTERY_THUMBS_BACKGROUND_COLOR' ),
        array (
            "text",
            23,
            "colorpicker"
        )
    ),
    array (
        "MESSAGE",
        GetMessage ( 'MYSTERY_THUMBS_BACKGROUND_MESSAGE' )
    ),
    array (
        "PNG_TRANSPARENT",
        GetMessage ( 'MYSTERY_THUMBS_PNG_TRANSPARENT' ),
        array ( "checkbox" )
    ),
    array (
        "HEADING",
        GetMessage ( 'MYSTERY_THUMBS_HEADING_WATERMARK' )
    ),
    array (
        "WATERMARK_ENABLE",
        GetMessage ( 'MYSTERY_THUMBS_WATERMARK_ENABLE' ),
        array ( "checkbox" )
    ),
    array (
        "WATERMARK_POSITION",
        GetMessage ( 'MYSTERY_THUMBS_WATERMARK_POSITION' ),
        array (
            "select",
            array (
                "lt" => GetMessage ( 'MYSTERY_THUMBS_WATERMARK_POSITION_LT' ),
                "ct" => GetMessage ( 'MYSTERY_THUMBS_WATERMARK_POSITION_CT' ),
                "rt" => GetMessage ( 'MYSTERY_THUMBS_WATERMARK_POSITION_RT' ),
                "lm" => GetMessage ( 'MYSTERY_THUMBS_WATERMARK_POSITION_LM' ),
                "cm" => GetMessage ( 'MYSTERY_THUMBS_WATERMARK_POSITION_CM' ),
                "rm" => GetMessage ( 'MYSTERY_THUMBS_WATERMARK_POSITION_RM' ),
                "lb" => GetMessage ( 'MYSTERY_THUMBS_WATERMARK_POSITION_LB' ),
                "cb" => GetMessage ( 'MYSTERY_THUMBS_WATERMARK_POSITION_CB' ),
                "rb" => GetMessage ( 'MYSTERY_THUMBS_WATERMARK_POSITION_RB' ),
            ),
        )
    ),
    array (
        "WATERMARK_MIN_WIDTH_PICTURE",
        GetMessage ( 'MYSTERY_THUMBS_WATERMARK_MIN_WIDTH_PICTURE' ),
        array (
            "text",
            23
        )
    ),
    array (
        "WATERMARK_EXCEPTION",
        GetMessage ( 'MYSTERY_THUMBS_WATERMARK_EXCEPTION' ),
        array (
            "textarea",
            3,
            37,
            GetMessage ( 'MYSTERY_THUMBS_WATERMARK_EXCEPTION_DESC' )
        )
    ),
    array (
        "WATERMARK_IMG",
        GetMessage ( 'MYSTERY_THUMBS_WATERMARK_IMG' ),
        array (
            "hidden",
            "placeholder"   => GetMessage ( 'MYSTERY_THUMBS_WATERMARK_IMG_DESC' ),
            "needShowImage" => true
        ),
    ),
    array (
        "HEADING",
        GetMessage ( 'MYSTERY_THUMBS_HEADING_ADDITIONAL_PARAMS' )
    ),
    array (
        "DELETE_OLD_THUMBS",
        GetMessage ( 'MYSTERY_THUMBS_DELETE_OLD_THUMBS' ),
        array ( "checkbox" )
    ),
    array (
        "MESSAGE",
        GetMessage ( 'MYSTERY_THUMBS_DELETE_OLD_THUMBS_DESC' )
    ),
);

if ($REQUEST_METHOD == "POST" && strlen ( $Update ) > 0 && check_bitrix_sessid ()) {
    while (list($key, $name) = each ( $arAllOptions )) {
        $val = $$name[0];

        if ($name[2][0] == "checkbox" && $val != "Y") {
            $val = "N";
        } elseif (!array_key_exists ( $name[0],
                                      $_POST
        )
        ) {
            continue;
        }

        COption::SetOptionString ( $module_id,
                                   $name[0],
                                   $val
        );
    }
}

$aTabs = array (
    array (
        "DIV"   => "edit1",
        "TAB"   => GetMessage ( "MYSTERY_THUMBS_MAIN_TAB_SET" ),
        "TITLE" => GetMessage ( "MYSTERY_THUMBS_MAIN_TAB_SET" )
    ),
    array (
        "DIV"   => "edit2",
        "TAB"   => GetMessage ( "MYSTERY_THUMBS_DOC_TAB_SET" ),
        "TITLE" => GetMessage ( "MYSTERY_THUMBS_DOC_TAB_SET" )
    ),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$tabControl->Begin ();
$mid = htmlspecialchars ( $mid );
?>
<script type="text/javascript">
    function OnSelectBGColor(color, objColorPicker) {
        document.getElementById('i_BACKGROUND_COLOR').value = color;
    }
</script>
<form method="post" class="mystery-thumbs-form" action="<?= $APPLICATION->GetCurPage (
) ?>?mid=<?= $mid ?>&lang=<?= LANGUAGE_ID ?>">
    <?=bitrix_sessid_post ()?>
    <?$tabControl->BeginNextTab ();?>
    <?
    if (is_array ( $arDisplayOptions )) {
        foreach ($arDisplayOptions as $Option) {
            if ($Option[0] == 'MESSAGE') {
                ?>
                <tr>
                    <td align="center" colspan="2">
                        <div align="center" class="adm-info-message-wrap">
                            <div class="adm-info-message">
                                <?=htmlspecialchars ( $Option[1] )?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?
            } elseif ($Option[0] == 'HEADING') {
                ?>
                <tr class="heading">
                    <td colspan="2"><?=htmlspecialchars ( $Option[1] )?></td>
                </tr>
            <?
            } else {
                $val = COption::GetOptionString ( $module_id,
                                                  $Option[0]
                );
                $type = $Option[2];
                $Option[0] = htmlspecialchars ( $Option[0] );
                ?>
                <tr>
                    <td valign="top" width="50%">
                        <?
                        if ($type[0] == "checkbox" || $type[0] == "text") {
                            echo "<label for=\"i_".$Option[0]."\">".$Option[1]."</label>";
                        } else {
                            echo $Option[1];
                        }
                        ?>
                    </td>
                    <td valign="top" width="50%">
                        <?
                        if ($type[0] == "checkbox") {
                            if ($Option[0] == 'DELETE_OLD_THUMBS') {
                                $val = '';
                            }
                            ?>
                            <input type="checkbox" name="<?= $Option[0] ?>" id="i_<?= $Option[0] ?>" value="Y" <?=($val == "Y") ? " checked" : ''?>>
                        <?
                        } elseif ($type[0] == "text") {
                            $val = htmlspecialchars ( $val );
                            ?>
                            <input type="text" size="<?= $type[1] ?>" maxlength="255" value="<?= $val ?>" name="<?= $Option[0] ?>" id="i_<?= $Option[0] ?>" <?=($type[2] == 'colorpicker') ? 'style="float:left;"' : ''?>>
                            <?
                            if ($type[2] == 'colorpicker') {
                                echo '<div style="float:left; margin:0 0 0 5px;">';
                                $APPLICATION->IncludeComponent ( "bitrix:main.colorpicker",
                                                                 "",
                                                                 Array (
                                                                       "SHOW_BUTTON" => "Y",
                                                                       "ID"          => "color_bg_picker",
                                                                       "NAME"        => GetMessage ( 'MYSTERY_THUMBS_COLOR_PICKER'
                                                                       ),
                                                                       "ONSELECT"    => "OnSelectBGColor"
                                                                 ),
                                                                 false,
                                                                 array ( "HIDE_ICONS" => "Y" )
                                );
                                echo '</div><div style="clear:both;"></div>';
                            }
                        } elseif ($type[0] == "textarea") {
                            $val = htmlspecialchars ( $val );
                            ?>
                            <textarea rows="<?= $type[1] ?>" cols="<?= $type[2] ?>" name="<?= $Option[0] ?>"><?=$val?></textarea>
                            <?
                            if ($type[3] > '') {
                                ?>
                                <br />
                                <small><?=$type[3]?></small>
                            <?
                            }
                        } elseif ($type[0] == 'select') {
                            ?>
                            <select name="<?= $Option[0] ?>">
                                <?
                                foreach ($type[1] as $k => $v) {
                                    if ($val == $k) {
                                        $select = 'selected="selected"';
                                    } else {
                                        $select = '';
                                    }

                                    $v = htmlspecialchars ( $v );
                                    ?>
                                    <option value="<?= $k ?>" <?=$select?>><?=$v?></option>
                                <?
                                }
                                ?>
                            </select>
                        <?
                        } elseif ($type[0] == 'hidden') {
                            if ($type['placeholder'] > '') {
                                echo htmlspecialcharsBack ( $type['placeholder'] );
                            }
                            ?>
                            <input type="hidden" value="<?= $val ?>" name="<?= $Option[0] ?>" id="i_<?= $Option[0] ?>">
                        <?
                        }
                        if ($type['needShowImage']) {
                            $waterImage = MYSTERY_THUMBS_WATERMARK_IMG;

                            if (file_exists ( $_SERVER['DOCUMENT_ROOT'].$waterImage )) {
                                ?>
                                <br />
                                <img src="http://<?= $_SERVER['HTTP_HOST'] ?><?= $waterImage ?>" alt="<?= GetMessage ( 'MYSTERY_THUMBS_WATERMARK_ALT'
                                ) ?>">
                            <?
                            }
                        }
                        ?>
                    </td>
                </tr>
            <?
            }
        }
    }
    ?>
    <?$tabControl->BeginNextTab ();?>
    <?=GetMessage ( 'MYSTERY_THUMBS_DOCUMENTATION' )?>
    <?$tabControl->Buttons ();?>
    <script language="JavaScript">
        function RestoreDefaults() {
            if (confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
                window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?=LANGUAGE_ID?>&mid=<?echo urlencode($mid)?>";
        }
    </script>
    <input type="submit" name="Update" value="<?= GetMessage ( "MYSTERY_THUMBS_FORM_SAVE" ) ?>">
    <input type="hidden" name="Update" value="Y">
    <input type="reset" name="reset" value="<?= GetMessage ( "MYSTERY_THUMBS_FORM_RESET" ) ?>">
    <input type="button" title="<? echo GetMessage ( "MYSTERY_THUMBS_MAIN_HINT_RESTORE_DEFAULTS"
    ) ?>" OnClick="RestoreDefaults();" value="<? echo GetMessage ( "MYSTERY_THUMBS_MAIN_RESTORE_DEFAULTS" ) ?>">
    <?$tabControl->End ();?>
</form>
<style type="text/css">
    .mystery-thumbs-form ol li, .mystery-thumbs-form ul li {
        margin: 7px 0;
    }

    .mystery-thumbs-form ol li.noPoint, .mystery-thumbs-form ul li.noPoint {
        list-style: none;
    }

    .mystery-thumbs-form img.mysteryThumbsTest {
        border: 2px solid #E0E8EA;
        margin: 3px;
    }
</style>