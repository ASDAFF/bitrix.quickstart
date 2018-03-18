<?php IncludeModuleLangFile( __FILE__ );?>
<?php
    //Get iblock types list
        $iTypes = array();
        $res = CIBlockType::GetList(array('NAME' => 'ASC'), array('LANGUAGE_ID' => LANGUAGE_ID));
        while($iType = $res->Fetch()){
            $iTypes[$iType['ID']] = $iType['NAME'];
        }
    //---End---Get iblock types list

    //Get product iblocks list
        $iblocks = array();
        $rsIBlock = CIBlock::GetList(Array(), array('ACTIVE'=>'Y'));
        while($iblock = $rsIBlock->Fetch()){
            $iblocks[$iblock['ID']] = $iTypes[$iblock['IBLOCK_TYPE_ID']].' | '.$iblock['NAME'];
        }
        asort($iblocks);
    //---End---Get product iblocks list
?>
<tr>
	<td><?=GetMessage($webformatLangPrefix.'CATALOG');?>:</td>
	<td>
        <select name="webformat_iblock1c[catalog]" size="1">
            <option value="0">--<?=GetMessage($webformatLangPrefix.'IBLOCK_NONE');?>--</option>
            <?php
                foreach($iblocks as $iblockID => $label){
                    echo ('<option value="'.$iblockID.'">'.$label.'</option>');
                }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td><?=GetMessage($webformatLangPrefix.'OFFERS');?>:</td>
    <td>
        <select name="webformat_iblock1c[offers]" size="1">
            <option value="0">--<?=GetMessage($webformatLangPrefix.'IBLOCK_NONE');?>--</option>
            <?php
                foreach($iblocks as $iblockID => $label){
                    echo ('<option value="'.$iblockID.'">'.$label.'</option>');
                }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td></td>
    <td class="wfMessage"><?=GetMessage($webformatLangPrefix.'BEHAVIOUR_DESC');?></td>
</tr>
<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td></td>
    <td>
        <input type="checkbox" name="webformat_iblock1c[extra][CODE][IS_REQUIRED]" value="N" id="webformat_iblock1c_code"/><label for="webformat_iblock1c_code">&nbsp;<?=GetMessage($webformatLangPrefix.'RESET_CODE');?></label>
    </td>
</tr>
<tr>
    <td></td>
    <td>
        <input type="checkbox" name="webformat_iblock1c[extra][SECTION_CODE][IS_REQUIRED]" value="N" id="webformat_iblock1c_scode"/><label for="webformat_iblock1c_scode">&nbsp;<?=GetMessage($webformatLangPrefix.'RESET_SCODE');?></label>
    </td>
</tr>
<tr>
    <td></td>
    <td class="wfMessage padding">
        <?=GetMessage($webformatLangPrefix.'CODE_CHK_DESC');?>
    </td>
</tr>