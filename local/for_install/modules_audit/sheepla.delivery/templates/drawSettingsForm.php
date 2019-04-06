<?
global $APPLICATION;
$aTabs[] = array("DIV" => "editsettings","TAB" => GetMessage("SHEEPLA_EDITSETTINGS_TITLE"),"TITLE" => GetMessage("SHEEPLA_EDITSETTINGS_TITLE"));
$aTabs[] = array( "DIV" => "sheeplacarriers", "TAB" => 'Sheepla Carriers',"TITLE" => 'Sheepla Carriers');
if($isSheeplaAdmin){
    $aTabs[] = array( "DIV" => "sheeplaadvanced", "TAB" => 'Sheepla Advanced',"TITLE" => 'Sheepla Advanced');
}

$aTabs[] = array( "DIV" => "sheeplalog", "TAB" => 'Sheepla Log',"TITLE" => 'Sheepla Warning log');

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();

?>

<form method="POST" action="<? echo $APPLICATION->GetCurPage().'?mid=sheepla.delivery&amp;lang='.LANG.'&amp;mid_menu=1'; ?>" name="form1" onSubmit="return prepareData()">

<?$tabControl->BeginNextTab();?>
    <tr><td colspan="2">
    <input type="hidden" name="lang" value="<?=LANG?>" />
    <input type="hidden" name="SID" value="<?=htmlspecialchars($SID)?>" />
<?
    echo bitrix_sessid_post();
    
?>
    <?if($sheeplaSettings['configOk']=='1'):?>
        <div><h3><?=GetMessage('SHEEPLA_SETTINGS_CONF_OK');?></h3></div>
    <?else:?>
        <div><h3><?=GetMessage('SHEEPLA_SETTINGS_CONF_ERR');?></h3></div>
    <?endif;?>
    </td>
    </tr>
    <tr>
        <td class="field-name"><?=GetMessage('SHEEPLA_SETTINGS_ADMINAPIKEY_NAME');?></td>
        <td><input type="text" size="50" name="adminApiKey" value="<?=$sheeplaSettings['adminApiKey'];?>" /></td>
    </tr>
    <tr>
        <td class="field-name"><?=GetMessage('SHEEPLA_SETTINGS_PUBLICAPIKEY_NAME');?></td>
        <td><input type="text" size="50" name="publicApiKey" value="<?=$sheeplaSettings['publicApiKey'];?>" /></td>
    </tr>
    <tr>
        <td class="field-name"><?=GetMessage('SHEEPLA_SETTINGS_APIURL_NAME');?></td>
        <td><input type="text" size="50" name="apiUrl" value="<?=$sheeplaSettings['apiUrl'];?>" /></td>
    </tr>
    <tr>
        <td class="field-name"><?=GetMessage('SHEEPLA_SETTINGS_JSURL_NAME');?></td>
        <td><input type="text" size="50" name="jsUrl" value="<?=$sheeplaSettings['jsUrl'];?>" /></td>
    </tr>
    <tr>
        <td class="field-name"><?=GetMessage('SHEEPLA_SETTINGS_CSSURL_NAME');?></td>
        <td><input type="text" size="50" name="cssUrl" value="<?=$sheeplaSettings['cssUrl'];?>" /></td>
    </tr>
    <? if($isSheeplaAdmin):?>
    <tr>
    <?else:?>
    <tr style="display: none;">
    <?endif;?>
    
        <td class="field-name"><?=GetMessage('SHEEPLA_SETTINGS_SYNC_TYPE');?></td>
        <td>
            <select name="syncAll">
                <option <?if ($sheeplaSettings['syncAll']=='1') echo " selected='selected' " ?> value="1"><?=GetMessage('SHEEPLA_YES');?></option>
                <option <?if ($sheeplaSettings['syncAll']=='0') echo " selected='selected' " ?> value="0"><?=GetMessage('SHEEPLA_NO');?></option>
            </select>
        </td>
    </tr>
    <tr>        <td class="field-name"><?=GetMessage('SHEEPLA_SETTINGS_CHEKOUT_URL_TITLE');?></td>
        <td><input type="text" size="50" name="checkout" value="<?=$sheeplaSettings['checkout'];?>" /></td>
    </tr>
    
<? $tabControl->BeginNextTab(); ?>
    <?if(sizeof($sheeplaTemplates)>0){
        /** BEGIN Showing already added carriers*/
        $i=0;
        if(sizeof($sheeplaCarriers)>0){
            foreach($sheeplaCarriers as $carrier_key => $carrier_value){
                   echo '<tr>
                          <td>
                             <input type="hidden"  id="carrier_sheepla_db_id_'.$i.'" name="carrier_sheepla_db_id_'.$i.'" value="'.$carrier_value['SHEEPLA_DB_ID'].'">
                             <label for="carrier_sheepla_title_'.$i.'">'.GetMessage('SHEEPLA_SETTINGS_PROFILE_TITLE').'</label>
                             <input type="text" size="20" required id="carrier_sheepla_title_'.$i.'" name="carrier_sheepla_title_'.$i.'" value="'.$carrier_value['TITLE'].'">
                          </td>';
                   echo ' <td>
                            <label for="carrier_sheepla_description_'.$i.'">'.GetMessage('SHEEPLA_SETTINGS_PROFILE_DESCRIPTION').'</label>
                            <input type="text" size="20" id="carrier_sheepla_description_'.$i.'" name="carrier_sheepla_description_'.$i.'" value="'.$carrier_value['DESCRIPTION'].'">
                          </td>';
                   echo ' <td>
                            <label for="carrier_sheepla_template_'.$i.'">'.GetMessage('SHEEPLA_SETTINGS_PROFILE_TEMPLATE').'</label>
                            <select id="carrier_sheepla_template_'.$i.'" name="carrier_sheepla_template_'.$i.'"  style="width:100px;">';
                            foreach($sheeplaTemplates as $tpl_key => $tpl_val){
                                if($carrier_value['SHEEPLA_TEMPLATE'] == $tpl_val['id']){$selected = ' selected="selected" '; }else{ $selected = ''; }
                                echo '<option '.$selected.' value="'.$tpl_val['id'].'">'.$tpl_val['name'].'('.$tpl_val['carrierName'].')</option>';
                            }                    
                   echo'   </select>
                          </td>';
                   echo ' <td>
                            <label for="carrier_sheepla_sort_'.$i.'">'.GetMessage('SHEEPLA_SETTINGS_PROFILE_SORT').'</label>
                            <input type="text" size="3" id="carrier_sheepla_sort_'.$i.'" name="carrier_sheepla_sort_'.$i.'" value="'.$carrier_value['SHEEPLA_SORT'].'">
                          </td>';   
                   echo'  <td>
                            <label for="carrier_sheepla_template_'.$i.'">'.GetMessage('SHEEPLA_SETTINGS_PROFILE_MARK').'</label>
                            <select id="carrier_sheepla_template_'.$i.'" name="carrier_sheepla_delete_'.$i.'">
                                <option selected="selected" value="">'.GetMessage('SHEEPLA_NO').'</option>
                                <option value="1">'.GetMessage('SHEEPLA_YES').'</option>
                            </select>
                            <!--<input id="carrier_sheepla_template_'.$i.'" name="carrier_sheepla_delete_'.$i.'" type="checkbox" value="1">-->                    
                          </td>
                        </tr>
                      ';
                
                $i++;
            }
        }
        /** END Showing already added carriers*/
        
        
        /** BEGIN form to add 3 new carriers */        
        for($j=0;$j<3;$j++){
            /** //TODO Put GetMessage */
            echo '<tr>
                  <td>
                     <label for="carrier_sheepla_title_'.$i.'">'.GetMessage('SHEEPLA_SETTINGS_PROFILE_TITLE').'</label>
                     <input type="text" size="20" id="carrier_sheepla_title_'.$i.'" name="carrier_sheepla_title_'.$i.'" value="">
                  </td>';
            echo '<td>
                    <label for="carrier_sheepla_description_'.$i.'">'.GetMessage('SHEEPLA_SETTINGS_PROFILE_DESCRIPTION').'</label>
                    <input type="text" size="20" id="carrier_sheepla_description_'.$i.'" name="carrier_sheepla_description_'.$i.'" value="">
                  </td>';
            echo '<td>
                    <label for="carrier_sheepla_template_'.$i.'">'.GetMessage('SHEEPLA_SETTINGS_PROFILE_TEMPLATE').'</label>
                    <select id="carrier_sheepla_template_'.$i.'" name="carrier_sheepla_template_'.$i.'" style="width:100px;">';
                        foreach($sheeplaTemplates as $tpl_key => $tpl_val){                            
                            echo '<option value="'.$tpl_val['id'].'">'.$tpl_val['name'].'('.$tpl_val['carrierName'].')</option>';
                        }                    
            echo '</select>
                  </td>';
            echo '<td>
                    <label for="carrier_sheepla_sort_'.$i.'">'.GetMessage('SHEEPLA_SETTINGS_PROFILE_SORT').'</label>
                    <input type="text" size="3" id="carrier_sheepla_sort_'.$i.'" name="carrier_sheepla_sort_'.$i.'" value="100">
                  </td>';
            echo '<td>
                      '.GetMessage('SHEEPLA_SETTINGS_PROFILE_MSG1').'  
                      <input id="carrier_sheepla_template_'.$i.'" name="carrier_sheepla_delete_'.$i.'" type="hidden" value="0">                
                  </td>
                  </tr>
                  ';
            
            $i++;
        }
        /** END form to add 3 new carriers */
            
        
    }else{
        /** //TODO Put GetMessage */
        echo '<div class="errortext">'.GetMessage('SHEEPLA_SETTINGS_PROFILE_MSG2').'</div>';    
    }?>
<?if($isSheeplaAdmin):?> 
<? $tabControl->BeginNextTab(); ?>
   <th>
        <td colspan="2"><h4>Checkout page</h4></td>
   </th> 
    <tr>
        <td>
            <label for="jquery_city_selector">jQuery City Input Selector</label>
        </td><td>
            <input id="jquery_city_selector" size="100" required="required" type="text" name="jQDeliveryCitySelector" value="<?=$sheeplaSettings['jQDeliveryCitySelector'];?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="jquery_delivery_selector">jQuery Delivery Input Selector</label>
        </td><td>
            <input id="jquery_delivery_selector" size="100" required="required" type="text" name="jQDeliverySelector" value="<?=$sheeplaSettings['jQDeliverySelector'];?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="jquery_delivery_short_selector">jQuery Delivery Input Short Selector</label>
        </td><td>
            <input id="jquery_delivery_short_selector" size="100" required="required" type="text" name="jQDeliverySelectorShort" value="<?=$sheeplaSettings['jQDeliverySelectorShort'];?>" />
        </td>
    </tr>  
    <tr>
        <td>
            <label for="jquery_delivery_location_selector">jQuery Delivery Input Location Selector</label>
        </td><td>
            <input id="jquery_delivery_location_selector" size="100" required="required" type="text" name="jQLocationSelector" value="<?=$sheeplaSettings['jQLocationSelector'];?>" />
        </td>
    </tr>  
    <tr>
        <td>
            <label for="jquery_delivery_location_label_selector">jQuery Delivery Input Label Selector</label>
        </td><td>
            <input id="jquery_delivery_location_label_selector" size="100" required="required" type="text" name="jQLabelSelector" value="<?=$sheeplaSettings['jQLabelSelector'];?>" />
        </td>
    </tr> 
    <th>
        <td colspan="2"><h4>Admin order page</h4></td>        
    </th>
    <tr>
        <td>
            <label for="admin_order_add_url">Admin Order Add Url</label>
        </td><td>
            <input id="admin_order_add_url" size="100" required="required" type="text" name="adminOrderAddUrl" value="<?=$sheeplaSettings['adminOrderAddUrl'];?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="admin_order_edit_url">Admin Order Edit Url</label>
        </td><td>
            <input id="admin_order_edit_url" size="100" required="required" type="text" name="adminOrderEditUrl" value="<?=$sheeplaSettings['adminOrderEditUrl'];?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="admin_order_view_url">Admin Order View Url</label>
        </td><td>
            <input id="admin_order_view_url" size="100" required="required" type="text" name="adminOrderViewUrl" value="<?=$sheeplaSettings['adminOrderViewUrl'];?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="admin_order_view_selector">Admin Order View Selector</label>
        </td><td>
            <input id="admin_order_view_selector" size="100" required="required" type="text" name="orderViewSheeplaSelector" value="<?=$sheeplaSettings['orderViewSheeplaSelector'];?>" />
        </td>
    </tr>
    
     <th>
        <td colspan="2"><h4>Admin order page jquery selectors</h4></td>        
    </th>
    <tr>
        <td>
            <label for="admin_order_jquery_selector">Admin Order jQuery Selector</label>
        </td><td>
            <input id="admin_order_jquery_selector" size="100" required="required" type="text" name="adminOrderjQSelector" value="<?=$sheeplaSettings['adminOrderjQSelector'];?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="admin_order_jquery_short_selector">Admin Order jQuery Short Selector</label>
        </td><td>
            <input id="admin_order_jquery_short_selector" size="100" required="required" type="text" name="adminOrderjQSelectorShort" value="<?=$sheeplaSettings['adminOrderjQSelectorShort'];?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="admin_order_jquery_location_selector">Admin Order jQuery Location Selector</label>
        </td><td>
            <input id="admin_order_jquery_location_selector" size="100" required="required" type="text" name="adminOrderjQLocationSelector" value="<?=$sheeplaSettings['adminOrderjQLocationSelector'];?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="admin_order_jquery_label_selector">Admin Order jQuery Label Selector</label>
        </td><td>
            <input id="admin_order_jquery_label_selector" size="100" required="required" type="text" name="adminOrderjQLocationSelector" value="<?=$sheeplaSettings['adminOrderjQLabelSelector'];?>" />
        </td>
    </tr>
<?endif;?>   
<? $tabControl->BeginNextTab(); ?> 
    <tr>
        <td><?=$sheeplaLog?></td>
    </tr>
<?$tabControl->Buttons();?>
<? $SHEEPLA_RIGHT = $APPLICATION->GetGroupRight('sheepla.delivery'); ?>
<input type="submit" <?if ($SHEEPLA_RIGHT<"W") echo "disabled" ?> name="Update" value="<?echo GetMessage("SHEEPLA_SETTINGS_SAVE_TITLE")?>"/>
<input type="hidden" name="save" value="Y"/>
<?$tabControl->End();?>
</form>

