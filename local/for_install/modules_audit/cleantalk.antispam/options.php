<?php
/**
 * Settings of CleanTalk module
 *
 * @author  Cleantalk
 * @since   29/08/2013
 *
 * @link    http://cleantalk.org
 */

$sModuleId  = 'cleantalk.antispam';
CModule::IncludeModule( $sModuleId );
global $MESS;
IncludeModuleLangFile( __FILE__ );

if( $REQUEST_METHOD == 'POST' && $_POST['Update'] == 'Y' ) {
    /**
     * Set settings when submit
     */
    COption::SetOptionString( $sModuleId, 'status', $_POST['status'] == '1' ? '1' : '0' );
    COption::SetOptionString( $sModuleId, 'form_new_user', $_POST['form_new_user'] == '1' ? '1' : '0' );
    COption::SetOptionString( $sModuleId, 'form_comment_blog', $_POST['form_comment_blog'] == '1' ? '1' : '0' );
    COption::SetOptionString( $sModuleId, 'form_comment_forum', $_POST['form_comment_forum'] == '1' ? '1' : '0' );
    COption::SetOptionString( $sModuleId, 'form_comment_treelike', $_POST['form_comment_treelike'] == '1' ? '1' : '0' );
    COption::SetOptionString( $sModuleId, 'key', $_POST['key'] );
}
 
/**
 * Describe tabs
 */
$aTabs = array(
    array(
        'DIV'   => 'edit1',
        'TAB'   => GetMessage('MAIN_TAB_SET'),
        'ICON'  => 'fileman_settings',
        'TITLE' => GetMessage('MAIN_TAB_TITLE_SET' )
    ),
);
 
/**
 * Init tabs
 */
$oTabControl = new CAdmintabControl( 'tabControl', $aTabs );
$oTabControl->Begin();
 
/**
 * Settings form
 */
?><form method="POST" enctype="multipart/form-data" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars( $sModuleId )?>&lang=<?echo LANG?>">
    <?=bitrix_sessid_post()?>
    <?$oTabControl->BeginNextTab();?>
    <tr class="heading">
        <td colspan="2"><?=GetMessage( 'CLEANTALK_TITLE' )?></td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="status"><?echo GetMessage( 'CLEANTALK_LABEL_STATUS' );?>:</td>
        <td  valign="top">
            <input type="checkbox" name="status" id="status"<? if ( COption::GetOptionString( $sModuleId, 'status', '0' ) == '1'):?> checked="checked"<? endif; ?> value="1" />
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="form_new_user"><?echo GetMessage( 'CLEANTALK_LABEL_NEW_USER' );?>:</td>
        <td  valign="top">
            <input type="checkbox" name="form_new_user" id="form_new_user"<? if ( COption::GetOptionString( $sModuleId, 'form_new_user', '0' ) == '1'):?> checked="checked"<? endif; ?> value="1" />
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="form_comment_blog"><?echo GetMessage( 'CLEANTALK_LABEL_COMMENT_BLOG' );?>:</td>
        <td  valign="top">
            <input type="checkbox" name="form_comment_blog" id="form_comment_blog"<? if ( COption::GetOptionString( $sModuleId, 'form_comment_blog', '0' ) == '1'):?> checked="checked"<? endif; ?> value="1" />
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="form_comment_forum"><?echo GetMessage( 'CLEANTALK_LABEL_COMMENT_FORUM' );?>:</td>
        <td  valign="top">
            <input type="checkbox" name="form_comment_forum" id="form_comment_forum"<? if ( COption::GetOptionString( $sModuleId, 'form_comment_forum', '0' ) == '1'):?> checked="checked"<? endif; ?> value="1" />
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="form_comment_treelike"><?echo GetMessage( 'CLEANTALK_LABEL_COMMENT_TREELIKE' );?>:</td>
        <td  valign="top">
            <input type="checkbox" name="form_comment_treelike" id="form_comment_treelike"<? if ( COption::GetOptionString( $sModuleId, 'form_comment_treelike', '0' ) == '1'):?> checked="checked"<? endif; ?> value="1" />
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="key"><?echo GetMessage( 'CLEANTALK_LABEL_KEY' );?>:</td>
        <td  valign="top">
            <input type="text" name="key" id="key" value="<?php echo COption::GetOptionString( $sModuleId, 'key', '' ) ?>" />
        </td>
    </tr>
    <?$oTabControl->Buttons();?>
    <input type="submit" name="Update" value="<?php echo GetMessage( 'CLEANTALK_BUTTON_SAVE' ) ?>" />
    <input type="reset" name="reset" value="<?php echo GetMessage( 'CLEANTALK_BUTTON_RESET' ) ?>" />
    <input type="hidden" name="Update" value="Y" />
    <?$oTabControl->End();?>
</form>
