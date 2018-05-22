<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );
IncludeModuleLangFile(__FILE__);

$profileDefaults = $profileUtils->GetDefaults( $arProfile["IBLOCK_ID"], true );

$types = $profileUtils->GetTypes();

$obProfile = new CExportproProfileDB();
$dbProcessProfiles = $obProfile->GetProcessList(
    array(
        $by => $order
    ),
    array()
);

$exportTimeStamp = false;
if( !empty( $arProfile["SETUP"]["URL_DATA_FILE"] ) ){
    $arExportTime = explode( " ", $arProfile["SETUP"]["LAST_START_EXPORT"] );
    $exportTimeStamp = MakeTimeStamp( $arExportTime[0].".".date( "Y" )." ".$arExportTime[1] );
    $profileTimeStamp = MakeTimeStamp( $arProfile["TIMESTAMP_X"], "YYYY-MM-DD HH:MI:SS" );
}

$arActualProfileNames = array();
while( $arProcessProfile = $dbProcessProfiles->Fetch() ){
    $arActualProfileNames[] = $arProcessProfile["NAME"];
}

if( !in_array( $arProfile["TYPE"], $arActualProfileNames ) ){
    $profileDefaults["PROFILE_CODE"] = $arProfile["TYPE"];
}
else{
    $bCorrentProfileName = false;
    $iProfileNameIndex = 1;
    while( !$bCorrentProfileName ){
        if( !in_array( $arProfile["TYPE"].$iProfileNameIndex, $arActualProfileNames ) ){
            $profileDefaults["PROFILE_CODE"] = $arProfile["TYPE"].$iProfileNameIndex;
            $bCorrentProfileName = true;
        }
        $iProfileNameIndex++;
    }
}

if( strlen( $profileDefaults["PROFILE_CODE"] ) > 0 ){
    $exportFilePath = "/acrit.exportpro/".$profileDefaults["PROFILE_CODE"].".xml";
}

$bUseCompress = $arProfile["USE_COMPRESS"] == "Y" ? 'checked="checked"' : "";

if( $arProfile["USE_COMPRESS"] == "Y" ){
    $originalName = $_SERVER["DOCUMENT_ROOT"].$arProfile["SETUP"]["URL_DATA_FILE"];
    
    $zipPath == false;
    $fileZipPath == false;
    if( stripos( $arProfile["SETUP"]["URL_DATA_FILE"], "csv" ) !== false ){
        $zipPath = str_replace( "csv", "zip", $originalName );
        $fileZipPath = str_replace( "csv", "zip", $arProfile["SETUP"]["URL_DATA_FILE"] );
    }
    elseif( stripos( $arProfile["SETUP"]["URL_DATA_FILE"], "xml" ) !== false ){
        $zipPath = str_replace( "xml", "zip", $originalName );
        $fileZipPath = str_replace( "xml", "zip", $arProfile["SETUP"]["URL_DATA_FILE"] );
    }
    
    if( $zipPath ){
        $packarc = CBXArchive::GetArchive( $zipPath );
    }
}

$productsPerStep = intval( $arProfile["SETUP"]["EXPORT_STEP"] ) <= 0 ? 50 : intval( $arProfile["SETUP"]["EXPORT_STEP"] );
?>
<tr class="heading">
    <td colspan="2" valign="top"><?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_RUN" )?></td>
</tr>
<?if( $arProfile["SETUP"]["FILE_TYPE"] == "csv" ){?>
    <tr id="tr_csv_info">
        <td colspan="2">
            <?=BeginNote();?>
            <?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_CSV_INFO" );?>
            <?=EndNote();?>
        </td>
    </tr>
<?}?>
<tr>
    <td width="40%">
        <span id="hint_PROFILE[SETUP][EXPORT_STEP]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[SETUP][EXPORT_STEP]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_EXPORTP_STEP_HELP" );?>' );</script>
        <label for="PROFILE[SETUP][EXPORT_STEP]"><?=GetMessage( "ACRIT_EXPORTPRO_EXPORTP_STEP" )?></label>
    </td>
    <td width="60%" id="export_step_block">
        <?if( intval( $arProfile["ID"] ) > 0 ){?>
            <div style="float: left;">
                <?if( ( $arProfile["TYPE"] != "advantshop" ) ){?>
                    <input type="text" name="PROFILE[SETUP][EXPORT_STEP]" id="export_step_value" value="<?=( ( $arProfile["SETUP"]["FILE_TYPE"] == "csv" ) ? 50000 : $productsPerStep );?>">
                <?}
                else{?>
                    <input type="text" name="PROFILE[SETUP][EXPORT_STEP]" id="export_step_value" value="50000" disabled="disabled">
                <?}?>
            </div>
            <div style="margin-top: -3px;">
                <a class="adm-btn adm-btn-save" onclick="CalcExportStep( <?=$arProfile["ID"];?> )"><?=GetMessage( "ACRIT_EXPORTPRO_EXPORTP_STEP_CALC" )?></a>
            </div>
            <div style="clear: both"></div>
        <?}
        else{?>
            <?if( ( $arProfile["TYPE"] != "advantshop" ) ){?>
                <input type="text" name="PROFILE[SETUP][EXPORT_STEP]" id="export_step_value" value="<?=( ( $arProfile["SETUP"]["FILE_TYPE"] == "csv" ) ? 50000 : $productsPerStep );?>" <?if( $arProfile["SETUP"]["FILE_TYPE"] == "csv" ):?>disabled="disabled"<?endif;?>>
            <?}
            else{?>
                <input type="text" name="PROFILE[SETUP][EXPORT_STEP]" id="export_step_value" value="50000" disabled="disabled">
            <?}?>
        <?}?>
            
    </td>
</tr>
<tr id="file_setting" style="display: table-row">
    <td colspan="2" align="center">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tbody>
                <tr>
                    <td colspan="2" align="center">
                        <?=BeginNote();?>
                        <?=GetMessage( "ACRIT_EXPORTPRO_RUN_EXPORT_FILE_DESCRIPTION" );?>
                        <?=EndNote();?>
                    </td>
                </tr>
                <tr id="check_compress_block">
                    <?if( ( $arProfile["TYPE"] != "advantshop" ) ){?>
                        <td width="40%" class="adm-detail-content-cell-l">
                            <span id="hint_PROFILE[USE_COMPRESS]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[USE_COMPRESS]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_USE_COMPRESS_HELP" );?>' );</script>
                            <label for="PROFILE[USE_COMPRESS]"><?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_USE_COMPRESS" );?></label>
                        </td>
                        <td width="60%" class="adm-detail-content-cell-r"><input type="checkbox" name="PROFILE[USE_COMPRESS]" value="Y" <?=$bUseCompress?>></td>
                    <?}
                    else{?>
                        <td colspan="2"></td>
                    <?}?>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l">
                        <span id="hint_PROFILE[SETUP][URL_DATA_FILE]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[SETUP][URL_DATA_FILE]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_FILENAME_HELP" )?>' );</script>
                        <?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_FILENAME" )?>
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r" id="export_file_path">
                        <?if( strlen( $types[$arProfile["TYPE"]]["PORTAL_VALIDATOR"] ) > 0 ){?>
                            <div style="float: left;">
                                <input type="text" name="PROFILE[SETUP][URL_DATA_FILE]" size="30" id="URL_DATA_FILE" value="<?=( strlen( $arProfile["SETUP"]["URL_DATA_FILE"] ) > 0 ) ? $arProfile["SETUP"]["URL_DATA_FILE"] : $exportFilePath;?>">
                                <input type="button" value="..." onclick="BtnClick()">
                            </div>
                            <div style="padding: 5px 0px 0px 300px;">
                                <a href="<?=$types[$arProfile["TYPE"]]["PORTAL_VALIDATOR"];?>" target="_blank"><?=$types[$arProfile["TYPE"]]["PORTAL_VALIDATOR"];?></a>
                            </div>
                            <div style="clear: both;"></div>
                        <?}
                        else{?>
                            <input type="text" name="PROFILE[SETUP][URL_DATA_FILE]" size="30" id="URL_DATA_FILE" value="<?=( strlen( $arProfile["SETUP"]["URL_DATA_FILE"] ) > 0 ) ? $arProfile["SETUP"]["URL_DATA_FILE"] : $exportFilePath;?>">
                            <input type="button" value="..." onclick="BtnClick()">
                        <?}?>
                    </td>
                </tr>
                <tr id="tr_type_file">
                    <?if( ( $arProfile["TYPE"] != "advantshop" ) ){?>
                        <td width="40%" class="adm-detail-content-cell-l">
                            <span id="hint_PROFILE[SETUP][FILE_TYPE]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[SETUP][FILE_TYPE]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_FILE_TYPE_HELP" )?>' );</script>
                            <label for="PROFILE[SETUP][FILE_TYPE]"><?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_FILE_TYPE" )?></label>
                        </td>
                        <td width="60%" class="adm-detail-content-cell-r">
                            <?if( empty( $arProfile["SETUP"]["FILE_TYPE"] ) )
                                $arProfile["SETUP"]["FILE_TYPE"] = "xml";
                            
                            foreach( $profileUtils->GetFileExportType() as $type ){
                                $checked = ( $type == $arProfile["SETUP"]["FILE_TYPE"] ) ? 'checked="checked"' : ""?>
                                <input type="radio" name="PROFILE[SETUP][FILE_TYPE]" value="<?=$type?>" <?=$checked?> onchange="ChangeFileType(this.value)"><?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_FILE_".strtoupper( $type ) )?>
                            <?}?>
                        </td>
                    <?}
                    else{?>
                        <td colspan="2">
                            <input type="hidden" name="PROFILE[SETUP][FILE_TYPE]" value="csv" />
                        </td>
                    <?}?>
                </tr>
                <tr id="tr_type_run">
                    <td width="40%" class="adm-detail-content-cell-l">
                        <span id="hint_PROFILE[SETUP][TYPE_RUN]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[SETUP][TYPE_RUN]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_TYPE_HELP" )?>' );</script>
                        <label for="PROFILE[SETUP][TYPE_RUN]"><?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_TYPE" )?></label>
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?if( empty( $arProfile["SETUP"]["TYPE_RUN"] ) )
                            $arProfile["SETUP"]["TYPE_RUN"] = "comp";
                            
                        foreach( $profileUtils->GetRunType() as $type ){
                            $checked = ($type == $arProfile["SETUP"]["TYPE_RUN"]) ? 'checked="checked"' : "" ?>
                            <input type="radio" name="PROFILE[SETUP][TYPE_RUN]" value="<?=$type?>" <?=$checked?> onchange="ChangeRunType(this.value)"><?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_".strtoupper( $type ) )?>
                        <?}?>
                    </td>
                </tr>
                
                <?
                $hideRunNewWindow = "hide";
                if( $arProfile["SETUP"]["TYPE_RUN"] != "cron" ){
                    $hideCronInfo = "hide";
                    $hideDateStart = "hide";
                    $hideDatePeriod = "hide";
                    $hideCronThread = "hide";
                    $hideCronIsPeriod = "hide";
                    $hideRunNewWindow = "";
                }
                if( file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/acrit.exportpro/export_{$arProfile["ID"]}_run.lock" ) )
                    $hideRunNewWindow = "hide";
                ?>
                
                <tr id="tr_run_new_window" class="<?=$hideRunNewWindow?>">
                    <td width="40%" class="adm-detail-content-cell-l"></td>
                    <td width="60%" class="adm-detail-content-cell-r" align="left">
                        <a class="adm-btn <?if( $exportTimeStamp > $profileTimeStamp ):?>adm-btn-save<?else:?>adm-btn-red<?endif;?>" href="/bitrix/tools/acrit.exportpro/acrit_exportpro.php?ID=<?=$ID?>" target="_blank"><?if( !empty( $arProfile["SETUP"]["URL_DATA_FILE"] ) ):?><?=GetMessage( "ACRIT_EXPORTPRO_RERUN_FILE_EXPORT" )?><?else:?><?=GetMessage( "ACRIT_EXPORTPRO_RUN_FILE_EXPORT" )?><?endif;?></a>
                        <?if( !$exportTimeStamp || ( $exportTimeStamp < $profileTimeStamp ) ){?>
                            <br/><br/>
                            <span class="important-info"><?if( !$exportTimeStamp ):?><?=GetMessage( "ACRIT_EXPORTPRO_FILE_EXPORT_NOT_EXIST" )?><?elseif( $exportTimeStamp < $profileTimeStamp ):?><?=GetMessage( "ACRIT_EXPORTPRO_FILE_EXPORT_NEED_RERUN" )?><?endif;?></span>
                        <?}?>
                    </td>
                </tr>
                <tr id="tr_run_new_window_cron" class="<?=$hideCronInfo?>">
                    <td width="40%" class="adm-detail-content-cell-l"></td>
                    <td width="60%" class="adm-detail-content-cell-r" align="left">
                        <a class="adm-btn <?if( $exportTimeStamp > $profileTimeStamp ):?>adm-btn-save<?else:?>adm-btn-red<?endif;?>" onclick="$( 'input[name=apply]' ).click();"><?if( !empty( $arProfile["SETUP"]["URL_DATA_FILE"] ) ):?><?=GetMessage( "ACRIT_EXPORTPRO_RERUN_FILE_EXPORT" )?><?else:?><?=GetMessage( "ACRIT_EXPORTPRO_RUN_FILE_EXPORT" )?><?endif;?></a>
                        <?if( !$exportTimeStamp || ( $exportTimeStamp < $profileTimeStamp ) ):?>
                            <br/><br/>
                            <span class="important-info"><?if( !$exportTimeStamp ):?><?=GetMessage( "ACRIT_EXPORTPRO_FILE_EXPORT_NOT_EXIST" )?><?elseif( $exportTimeStamp < $profileTimeStamp ):?><?=GetMessage( "ACRIT_EXPORTPRO_FILE_EXPORT_NEED_RERUN" )?><?endif;?></span>
                        <?endif;?>
                    </td>
                </tr>
                <?if( !empty( $arProfile["SETUP"]["URL_DATA_FILE"] ) ):?>
                    <?$urlDataFile = ( $packarc ) ? $fileZipPath : $arProfile["SETUP"]["URL_DATA_FILE"];?>
                    <tr id="tr_open_new_window">
                        <td width="40%" class="adm-detail-content-cell-l"></td>
                        <td width="60%" class="adm-detail-content-cell-r" align="left">
                            <span id="hint_OPEN_INNEW_WINDOW"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_OPEN_INNEW_WINDOW' ), '<?=GetMessage( "ACRIT_EXPORTPRO_OPEN_INNEW_WINDOW_HELP" )?>' );</script>
                            
                            <a href="<?=$urlDataFile?>" target="_blank"><?=GetMessage( "ACRIT_EXPORTPRO_OPEN_INNEW_WINDOW" )?></a>
                        </td>
                    </tr>
                <?endif?>
                
                
                <tr id="tr_cron_info" class="<?=$hideCronInfo?>">
                    <td colspan="2">
                        <?=BeginNote();?>
                        <?=GetMessage( "ACRIT_EXPORTPRO_CES_NOTES0" );?><br><br>
                        <?=GetMessage( "ACRIT_EXPORTPRO_CES_NOTES1" );?><br><br>
                        <?=GetMessage( "ACRIT_EXPORTPRO_CES_NOTES3" );?>
                        <b><?=$_SERVER["DOCUMENT_ROOT"];?>/bitrix/crontab/crontab.cfg</b>
                        <?=GetMessage( "ACRIT_EXPORTPRO_CES_NOTES4" );?><br><br>
                        <?=GetMessage( "ACRIT_EXPORTPRO_CES_NOTES5" );?><br>
                        <b>crontab <?=$_SERVER["DOCUMENT_ROOT"];?>/bitrix/crontab/crontab.cfg</b><br>
                        <?=GetMessage( "ACRIT_EXPORTPRO_CES_NOTES6" );?><br>
                        <b>crontab -l</b><br>
                        <?=GetMessage( "ACRIT_EXPORTPRO_CES_NOTES7" );?><br>
                        <b>crontab -r</b><br><br>
                        
                        <?$arRetval = array();
                        @exec( "crontab -l", $arRetval );
                        if( is_array( $arRetval ) && !empty( $arRetval ) ){?>
                            <?=GetMessage( "ACRIT_EXPORTPRO_CES_NOTES8" );?><br>
                            <textarea name="crontasks" cols="70" rows="5" wrap="off" readonly>
                                <?foreach( $arRetval as $stRetval ){
                                    if( strlen( $stRetval ) > strlen( PHP_EOL ) ){?>
                        	            <?=htmlspecialcharsbx($stRetval)."\n";?>
	                                <?}
                                }?>
                            </textarea><br>
                        <?}?>
                        <?=GetMessage( "ACRIT_EXPORTPRO_CES_NOTES10" );?>
                    </td>
                </tr>
                <tr id="tr_date_start" class="<?=$hideDateStart?>">
                    <td width="40%" class="adm-detail-content-cell-l" style="vertical-align:middle">
                        <span id="hint_PROFILE[SETUP][DAT_START]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[SETUP][DAT_START]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_DATESTART_HELP" )?>' );</script>
                        <label for="PROFILE[SETUP][DAT_START]"><?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_DATESTART" )?></label><br>
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <div class="adm-input-wrap adm-input-wrap-calendar">
                            <input class="adm-input adm-input-calendar" type="text" name="PROFILE[SETUP][DAT_START]" size="18" value="<?if( !empty( $arProfile["SETUP"]["DAT_START"] ) ):?><?=$arProfile["SETUP"]["DAT_START"]?><?else:?><?=date( "d.m.Y H:i:s", time() + 120 );?><?endif;?>">
                            <span class="adm-calendar-icon" title="<?=GetMessage( "ACRIT_EXPORTPRO_NAJMITE_DLA_VYBORA_D" )?>" onclick="BX.calendar({node:this, field:'PROFILE[SETUP][DAT_START]', form: '', bTime: true, bHideTime: false});"></span>
                        </div>
                    </td>
                </tr>
                <tr id="tr_date_period" class="<?=$hideDatePeriod?>">
                    <td width="40%" class="adm-detail-content-cell-l">
                        <span id="hint_PROFILE[SETUP][PERIOD]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[SETUP][PERIOD]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_PERIOD_HELP" )?>' );</script>
                        <label for="PROFILE[SETUP][PERIOD]"><?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_PERIOD" )?></label>
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <input type="text" name="PROFILE[SETUP][PERIOD]" value="<?if( !empty( $arProfile["SETUP"]["PERIOD"] ) ):?><?=$arProfile["SETUP"]["PERIOD"]?><?else:?>1440<?endif;?>" size="20">
                    </td>
                </tr>
                <tr id="tr_cron_threads" class="<?=$hideCronThread?>">
                    <td width="40%" class="adm-detail-content-cell-l">
                        <span id="hint_PROFILE[SETUP][THREADS]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[SETUP][THREADS]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_THREADS_HELP" )?>' );</script>
                        <label for="PROFILE[SETUP][THREADS]"><?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_THREADS" )?></label>
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <input type="text" name="PROFILE[SETUP][THREADS]" value="<?=intval( $arProfile["SETUP"]["THREADS"] ) > 0 ? $arProfile["SETUP"]["THREADS"] : 1?>" size="20">
                    </td>
                </tr>
                
                
                <tr id="tr_cron_is_period" class="<?=$hideCronIsPeriod?>">
                    <td width="40%" class="adm-detail-content-cell-l">
                        <span id="hint_PROFILE[SETUP][IS_PERIOD]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[SETUP][IS_PERIOD]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_IS_PERIOD_HELP" )?>' );</script>
                        <label for="PROFILE[SETUP][IS_PERIOD]"><?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_IS_PERIOD" )?></label>
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                    	<input type="checkbox" name="PROFILE[SETUP][IS_PERIOD]" value="Y" <?=$arProfile["SETUP"]["IS_PERIOD"] == "Y" ? 'checked="checked"' : ""?> >
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <span id="hint_PROFILE[SETUP][LAST_START_EXPORT]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[SETUP][LAST_START_EXPORT]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_LSATSTART_HELP" )?>' );</script>
        <label for="PROFILE[SETUP][LAST_START_EXPORT]"><?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_LSATSTART" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" name="PROFILE[SETUP][LAST_START_EXPORT]" readonly="readonly" placeholder=".. ::" value="<?=$arProfile["SETUP"]["LAST_START_EXPORT"]?>">
    </td>
</tr>
<?if( file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/acrit.exportpro/export_{$arProfile["ID"]}_run.lock" ) ):?>
    <tr id="unlock-container">
        <td>
            <?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_EXPORT_RUN" );?>
        </td>
        <td>
            <a class="adm-btn adm-btn-save" onclick="UnlockExport( <?=$arProfile["ID"]?> )"><?=getMessage( "ACRIT_EXPORTPRO_RUNTYPE_UNLOCK" )?></a>
        </td>
    </tr>
<?endif?>
<tr>
    <td align="center" colspan="2">
        <div class="adm-info-message"><?=GetMessage( "ACRIT_EXPORTPRO_RUNTYPE_INFO" )?></div>
    </td>
</tr>
<?CAdminFileDialog::ShowScript(
    array(
        "event" => "BtnClick",
        "arResultDest" => array( "FORM_NAME" => "exportpro_form", "FORM_ELEMENT_NAME" => "URL_DATA_FILE" ),
        "arPath" => array( "SITE" => SITE_ID, "PATH" => "/upload" ),
        "select" => "F", // F - file only, D - folder only
        "operation" => "S", // O - open, S - save
        "showUploadTab" => true,
        "showAddToMenuTab" => false,
        "fileFilter" => "xml,csv",
        "allowAllFiles" => true,
        "SaveConfig" => true,
    )
);?>