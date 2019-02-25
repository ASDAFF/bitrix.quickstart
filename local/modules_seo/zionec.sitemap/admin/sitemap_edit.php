<?php

include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/zionec.sitemap/lang/ru/admin/sitemap_edit.php');
global $DB, $APPLICATION;

if (!CModule::IncludeModule('zionec.sitemap'))
{
  $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$aTabs = array(
    array(
        "DIV" => "edit1",
        "TAB" => GetMessage('TAB_1'),
        "ICON" => "sale",
        "TITLE" => GetMessage('TITLE_TAB_1')
    ),
    array(
        "DIV" => "edit2",
        "TAB" => GetMessage('TAB_2'),
        "ICON" => "sale",
        "TITLE" => GetMessage('TITLE_TAB_2')
    ),
    array(
        "DIV" => "edit3",
        "TAB" => GetMessage('TAB_3'),
        "ICON" => "sale",
        "TITLE" => GetMessage('TITLE_TAB_3')
    ),
    array(
        "DIV" => "edit4",
        "TAB" => GetMessage('TAB_4'),
        "ICON" => "sale",
        "TITLE" => GetMessage('TITLE_TAB_4')
    ),
    array(
        "DIV" => "edit5",
        "TAB" => GetMessage('TAB_5'),
        "ICON" => "sale",
        "TITLE" => GetMessage('TITLE_TAB_5')
    )
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

// POST
if ($_POST['test_mode'] == 'Y')
{
  // switch on test mode
  if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/zionec_sitemap/'))
  {
    mkdir($_SERVER['DOCUMENT_ROOT'] . '/zionec_sitemap/', 0775, true);
  }

  file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/zionec_sitemap/.htaccess', 'deny from all');

  if ($_POST['test_mode_value'] == 'Y')
  {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/zionec_sitemap/test.log', '');
  }
  else
  {
    unlink($_SERVER['DOCUMENT_ROOT'] . '/zionec_sitemap/test.log');
  }

  LocalRedirect($_SERVER['REQUEST_URI']);
}

function getNounCase($val = false)
{
  if (!$val)
  {
    return false;
  }

  $case = '';
  $nval = $val % 10;
  switch ($nval)
  {
    case 2:
    case 3:
    case 4:
      $case = GetMessage('A');
      break;
    case 5:
    case 6:
    case 7:
    case 8:
    case 9:
    case 0:
      $case = GetMessage('OV');
      break;
  }
  return $case;
}

// include additional classes
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/zionec.sitemap/classes/general/mapping.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/zionec.sitemap/classes/general/getContent.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/zionec.sitemap/classes/general/database_provider.php");

$aSite = SiteMapGen::getSiteInfo();

$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/custom-admin.css");
$APPLICATION->AddHeadScript("/bitrix/js/zionec.sitemap/jquery.min.js");
$APPLICATION->AddHeadScript("/bitrix/js/zionec.sitemap/custom-admin-lang.js");
$APPLICATION->AddHeadScript("/bitrix/js/zionec.sitemap/custom-admin.js");
$APPLICATION->SetTitle(GetMessage('TITLE'));
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php"); ?>

<div class="adm-info-message-wrap">
    <div class="adm-notification">
      <?= GetMessage('NOTIFICATION') ?>
    </div>
</div>

<? if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/zionec_sitemap/test.log'))
{ ?>
    <div style="margin-bottom:10px;" class="adm-notification">
      <?= GetMessage('TEST') ?>
    </div>
<? } ?>

<?
$wrongDomain = false;
foreach ($aSite['sites'] as $key => $value)
{
  if (empty($value['DOMAIN']))
  {
    $wrongDomain = true;
  }
}

if ($wrongDomain)
{ ?>
    <div style="margin-bottom:10px;" class="adm-notification">
      <?= GetMessage('DOMEN') ?>
    </div>
<? } ?>

<? $tabControl->Begin() ?>
<? $tabControl->BeginNextTab() ?>
<tr>
    <td>
        <table>
            <tbody>
            <tr>
                <td>
                    <div class="wrapeer-button">
                      <?= GetMessage('GET_STATIC') ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="adm-cell">
          <?

          // get static content
          $rsStatic = getContent::getStaticAll();
          $staticList = array();
          while ($arStatic = $rsStatic->Fetch())
            $staticList[$arStatic['SIT']][] = $arStatic;

          if (!empty($staticList))
          { ?>
              <form name="form_2" id="form_2" style="position: relative">
                  <table>
                      <thead>
                      <th><?= GetMessage('PARAM_0') ?></th>
                      <th style="cursor: pointer" onmouseover="popup.showTooltip(this, 4)"
                          onmouseout="popup.hideTooltip('js-tooltip')"><?= GetMessage('PARAM_1') ?></th>
                      <th style="cursor: pointer" onmouseover="popup.showTooltip(this, 2)"
                          onmouseout="popup.hideTooltip('js-tooltip')"><?= GetMessage('PARAM_2') ?></th>
                      <th style="cursor: pointer" onmouseover="popup.showTooltip(this, 3)"
                          onmouseout="popup.hideTooltip('js-tooltip')"><?= GetMessage('PARAM_3') ?></th>
                      <th><?= GetMessage('PARAM_4') ?></th>
                      </thead>
                      <tbody>

                      <?php foreach ($staticList as $siteId => $staticInfo)
                      { ?>
                        <?php if (!empty($aSite['sites'][$siteId]))
                      { ?>

                          <tr>
                              <td colspan="5">
                                  <h3 class="admin-h3"><?= $aSite['sites'][$siteId]['NAME'] ?> [<?= $siteId ?>]</h3>
                              </td>
                          </tr>
                      <?php } ?>
                        <?php for ($i = 0; $i < $ic = count($staticInfo); $i++)
                      { ?>

                          <tr>
                              <td style="width:300px;">
                                  <div onmouseover="popup.showTooltipDisplay(this)"
                                       onmouseout="popup.hideTooltipDisplay(this)"
                                       class="row-property"><?= $staticInfo[$i]['PATH'] ?></div>
                                  <div style="display:none;text-align:center"
                                       class="display-tooltip"><?= (!empty($staticInfo[$i]['NAME'])) ? $staticInfo[$i]['NAME'] : GetMessage('FOLDER_NO_NAME') ?>
                                      <div class="one_tr_d"></div>
                                  </div>
                                  <input type="hidden" name="folder[]" value="<?= $staticInfo[$i]['CODE'] ?>">
                                  <input type="hidden" name="path[]" value="<?= $staticInfo[$i]['PATH'] ?>">
                                  <input type="hidden" name="id[]" value="<?= $staticInfo[$i]['ID'] ?>">
                                  <input type="hidden" name="name[]" value="<?= $staticInfo[$i]['NAME'] ?>">
                                  <input type="hidden" name="site[]" value="<?= $staticInfo[$i]['SIT'] ?>">
                              </td>
                              <td>
                                  <select title="frequency" class="custom-input"
                                          style="height: 37px;margin-top: 10px;width:130px" name="freq[]">
                                      <option value="none" <?php echo(($staticInfo[$i]['FREQ'] == 'none') ? 'selected="selected"' : '') ?>><?= GetMessage('FREQ_1') ?></option>
                                      <option value="always" <?php echo(($staticInfo[$i]['FREQ'] == 'always') ? 'selected="selected"' : '') ?>><?= GetMessage('FREQ_2') ?></option>
                                      <option value="hourly" <?php echo(($staticInfo[$i]['FREQ'] == 'hourly') ? 'selected="selected"' : '') ?>><?= GetMessage('FREQ_3') ?></option>
                                      <option value="daily" <?php echo(($staticInfo[$i]['FREQ'] == 'daily') ? 'selected="selected"' : '') ?>><?= GetMessage('FREQ_4') ?></option>
                                      <option value="weekly" <?php echo(($staticInfo[$i]['FREQ'] == 'weekly') ? 'selected="selected"' : '') ?>><?= GetMessage('FREQ_5') ?></option>
                                      <option value="monthly" <?php echo(($staticInfo[$i]['FREQ'] == 'monthly') ? 'selected="selected"' : '') ?>><?= GetMessage('FREQ_6') ?></option>
                                      <option value="yearly" <?php echo(($staticInfo[$i]['FREQ'] == 'yearly') ? 'selected="selected"' : '') ?>><?= GetMessage('FREQ_7') ?></option>
                                      <option value="never" <?php echo(($staticInfo[$i]['FREQ'] == 'never') ? 'selected="selected"' : '') ?>><?= GetMessage('FREQ_8') ?></option>
                                  </select>
                              </td>
                              <td>
                                <?
                                $modVal = ($staticInfo[$i]['MOD'] == 1) ? 'selected="selected"' : '';
                                ?>
                                  <select title="modification" class="custom-input"
                                          style="height: 37px;margin-top: 10px;width:130px" name="mod[]">
                                      <option value="0"><?= GetMessage('NO') ?></option>
                                      <option <?= $modVal ?>value="1"><?= GetMessage('YES') ?></option>
                                  </select>
                              </td>
                              <td>
                                  <input title="priority" class="custom-input" type="text"
                                         style="margin-top: 10px;width:70px" name="priority[]"
                                         value="<?= (!empty($staticInfo[$i]['PRIORITY'])) ? $staticInfo[$i]['PRIORITY'] : 0 ?>">
                              </td>
                              <td style="width:50px;">
                                  <div onclick="popup.delete(this, ge('form_2'))"
                                       onmouseover="popup.showTooltip(this, 0)"
                                       onmouseout="popup.hideTooltip('js-tooltip')" class="row-property"
                                       style="padding: 0;text-align: center;background-color: #597DA3;color: #fff;z-index:1">
                                      <span style="position:relative;font-size: 30.6px;z-index:2;display:block"><?= GetMessage('CLOSE') ?></span>
                                  </div>
                              </td>
                          </tr>
                      <?php } ?>
                      <?php } ?>

                      <tr>
                          <td colspan="2">
                              <div onclick="popup.send('form_2', 'bitrix/admin/sitemap_ajax', 'save_params_static_file')"
                                   onmouseover="popup.showTooltip(this, 1)" onmouseout="popup.hideTooltip('js-tooltip')"
                                   class="row-property-button left-margin"><?= GetMessage('SAVE') ?></div>
                          </td>
                      </tr>
                      </tbody>
                  </table>
              </form>
          <?php } ?>
        </div>
    </td>
</tr>

<? $tabControl->BeginNextTab(); ?>

<tr>
    <td>
        <table>
            <tbody>
            <tr>
                <td>
                    <div class="wrapeer-button">
                      <?= GetMessage('GET_IBLOCK') ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="adm-cell-iblock">
          <?= getContent::showIblockView($aSite); ?>
        </div>
    </td>
</tr>

<? $tabControl->EndTab() ?>
<? $tabControl->BeginNextTab(); ?>

<tr>
    <td>
      <?
      $objResult = $DB->Query("SELECT `NAME`, `VALUE` FROM `b_sitemap_generation`", false);
      $newArResult = array();
      $c = 0;
      $priority = 0;
      $mod = ' ';
      $time = 0;
      $protocol = 0;
      $frequency = '';
      while ($arResult = $objResult->Fetch())
      {
        if ($arResult['NAME'] == 'TIME')
        {
          $time = $arResult['VALUE'];
        }

        if ($arResult['NAME'] == 'MOD')
        {
          $mod = ($arResult['VALUE'] == 1) ? ' selected="selected"' : ' ';
        }

        if ($arResult['NAME'] == 'PRIORITY')
        {
          $priority = $arResult['VALUE'];
        }

        if ($arResult['NAME'] == 'FREQ')
        {
          $frequency = $arResult['VALUE'];
        }

        if ($arResult['NAME'] == 'GZIP')
        {
          $gzip = ($arResult['VALUE'] == 1) ? ' selected="selected"' : ' ';
        }

        if ($arResult['NAME'] == 'PROTOCOL')
        {
          $protocol = (!empty($arResult['VALUE'])) ? json_decode($arResult['VALUE'], true) : 0;
        }
      }

      if (!$wrongDomain)
      { ?>
          <div onclick="popup.generation()" class="row-property-button"><?= GetMessage('GENERIC') ?></div>
      <?php } ?>
    </td>
</tr>

<? $tabControl->EndTab() ?>
<? $tabControl->BeginNextTab(); ?>

<tr>
    <td>
        <form method="POST">
            <input type="hidden" name="test_mode" value="Y">
            <div class="adm-notification">
              <?= GetMessage('TEST_TEXT') ?>
            </div>
          <?
          if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/zionec_sitemap/test.log'))
          {
            $test_file = true;
          }
          else
          {
            $test_file = false;
          }

          ?>
            <table cellpadding="10">
                <tbody>
                <tr>
                    <td>
                        <label for="test_mode_value"><?= GetMessage('TEST_TITLE') ?></label>
                    </td>
                    <td>
                        <input <?= (($test_file) ? 'checked="checked"' : '') ?> id="test_mode_value" type="checkbox"
                                                                                name="test_mode_value" value="Y">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="submit" value="<?= GetMessage('CHANGE_STATE') ?>">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </td>
</tr>

<? $tabControl->EndTab() ?>
<? $tabControl->BeginNextTab() ?>

<tr>
    <td>
        <form name="form_4" id="form_4" method="POST">
            <div class="admin-content-white">
                <h3 class="admin-h3"><?= GetMessage('MULTISITE') ?></h3>
                <!-- admin-content-block -->
                <div class="admin-content-block">
                    <div class="adm-notification">
                      <?= GetMessage('TEXT_1') ?><?php echo ($aSite['count'] == 1) ? '' : 'o' ?> <span class="required"><b><?php echo $aSite['count'] ?></b></span><?= GetMessage('TEXT_2') ?><?php echo getNounCase($aSite['count']) ?>
                        .
                    </div>
                    <div class="admin-block-content">
                        <table class="table-width-100" cellpadding="10" cellspacing="0">
                            <thead>
                            <tr class="thead">
                                <th><?= GetMessage('TEXT_3') ?></th>
                                <th>LID</th>
                                <th><?= GetMessage('TEXT_4') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $i = 0;
                            foreach ($aSite['sites'] as $key => $value)
                            {
                              if ($i % 2 == 0)
                              {
                                $class = ' class="tr-even" ';
                              }
                              else
                              {
                                $class = '';
                              }
                              echo '<tr' . $class . '><td class="td-center">' . $value['NAME'] . '</td><td class="td-center">' . $key . '</td><td class="td-center">' . $value['DOMAIN'] . '</td></tr>';
                              $i++;
                            }
                            unset($i, $class, $value, $arSiteTitle);

                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /admin-content-block -->
                <h3 class="admin-h3"><?= GetMessage('TEXT_7') ?></h3>
                <!-- admin-content-block -->
                <div class="admin-content-block">
                    <div class="adm-notification">
                      <?= GetMessage('TEXT_8') ?>
                    </div>
                    <div class="admin-block-content">
                        <div class="select-block-div">
                            <table>
                                <tbody>


                                <?php

                                // для каждого сайта в отдельности нужно выбирать протокол
                                if (!empty($aSite['sites']))
                                {
                                  foreach ($aSite['sites'] as $key => $value)
                                  {
                                    ?>
                                      <tr>
                                          <td><?= $value['NAME'] ?> <b>(<?= $key; ?>)</b></td>
                                          <td>
                                              <select title="protocol" class="custom-input"
                                                      style="height: 37px;margin-top: 10px;width:180px"
                                                      name="protocol_<?= $key; ?>">
                                                  <option <?= (!empty($protocol[$key]) && $protocol[$key] == '0' ? 'selected="selected"' : ''); ?>
                                                          value="0"><?= GetMessage('TEXT_9') ?></option>
                                                  <option <?= (!empty($protocol[$key]) && $protocol[$key] == '1' ? 'selected="selected"' : ''); ?>
                                                          value="1">http://
                                                  </option>
                                                  <option <?= (!empty($protocol[$key]) && $protocol[$key] == '2' ? 'selected="selected"' : ''); ?>
                                                          value="2">https://
                                                  </option>
                                              </select>
                                          </td>
                                      </tr>
                                    <?
                                  }
                                }

                                ?>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <!-- /admin-content-block -->
                <h3 class="admin-h3"><?= GetMessage('TEXT_10') ?></h3>
                <!-- admin-content-block -->
                <div class="admin-content-block">
                    <div class="adm-notification">
                      <?= GetMessage('TEXT_11') ?>
                    </div>
                    <div class="admin-block-content">
                        <table>
                            <tbody>
                            <tr>
                                <td><?= GetMessage('MAIN_PAGE_TIME') ?></td>
                                <td>
                                    <select title="modification" class="custom-input"
                                            style="height: 37px;margin-top: 10px;width:180px" name="mod">
                                        <option value="0"><?= GetMessage('NO') ?></option>
                                        <option<?= $mod ?> value="1"><?= GetMessage('YES') ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?= GetMessage('MAIN_PAGE_PRIORITY') ?></td>
                                <td>
                                    <input title="priority" type="text" class="custom-input" name="priority"
                                           value="<?= $priority ?>">
                                </td>
                            </tr>
                            <tr>
                                <td><?= GetMessage('PARAM_1') ?></td>
                                <td>
                                    <select class="custom-input" style="height: 37px;margin-top: 10px;width:130px" name="frequency">
                                        <option value="none" <?= (($frequency == 'none') ? 'selected="selected"' : ''); ?>><?= GetMessage('FREQ_1'); ?></option>
                                        <option value="always" <?= (($frequency == 'always') ? 'selected="selected"' : ''); ?>><?= GetMessage('FREQ_2'); ?></option>
                                        <option value="hourly" <?= (($frequency == 'hourly') ? 'selected="selected"' : ''); ?>><?= GetMessage('FREQ_3'); ?></option>
                                        <option value="daily" <?= (($frequency == 'daily') ? 'selected="selected"' : ''); ?>><?= GetMessage('FREQ_4'); ?></option>
                                        <option value="weekly" <?= (($frequency == 'weekly') ? 'selected="selected"' : ''); ?>><?= GetMessage('FREQ_5'); ?></option>
                                        <option value="monthly" <?= (($frequency == 'monthly') ? 'selected="selected"' : ''); ?>><?= GetMessage('FREQ_6'); ?></option>
                                        <option value="yearly" <?= (($frequency == 'yearly') ? 'selected="selected"' : ''); ?>><?= GetMessage('FREQ_7'); ?></option>
                                        <option value="never" <?= (($frequency == 'never') ? 'selected="selected"' : ''); ?>><?= GetMessage('FREQ_8'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /admin-content-block -->
                <h3 class="admin-h3"><?= GetMessage('TEXT_12') ?></h3>
                <!-- admin-content-block -->
                <div class="admin-content-block">
                    <div class="adm-notification">
                      <?= GetMessage('TEXT_13') ?>
                    </div>
                    <div class="admin-block-content">
                        <table>
                            <tbody>
                            <tr>
                                <td><?= GetMessage('SITEMAP_PAGE') ?></td>
                                <td><input title="days" onmouseover="popup.showTooltipDisplay(this, 300, 55)"
                                           onmouseout="popup.hideTooltipDisplay(this)" class="custom-input" type="text"
                                           name="time" value="<?= $time ?>"><?= GetMessage('DAY') ?>
                                    <div style="display:none;text-align:center"
                                         class="display-tooltip"><?= GetMessage('DAY_BEFORE') ?>
                                        <div class="one_tr_d"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?= GetMessage('GZIP') ?></td>
                                <td>
                                    <select title="gzip on or off" class="custom-input"
                                            style="height: 37px;margin-top: 10px;width:180px" name="gzip">
                                        <option value="0"><?= GetMessage('NO') ?></option>
                                        <option<?= $gzip ?> value="1"><?= GetMessage('YES') ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="row-property-button"
                                         onclick="popup.send('form_4','bitrix/admin/sitemap_ajax','save_generic_property','',true)"><?= GetMessage('SAVE_PARAMS') ?></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /admin-content-block -->
            </div>
        </form>
    </td>
</tr>

<? $tabControl->End() ?>

<div id="layer_fixed">
    <div id="layer_fixed_window">
        <div id="pop-up-window">
            <div id="ppw-title"><span onclick="popup.close();" id="ppw-close"><?= GetMessage('CLOSE') ?></span><span
                        id="title-ppw"><?= GetMessage('TITLE') ?></span></div>
            <div class="ppw-outer">
                <div id="ppw-notification"></div>
                <div id="ppw-text"></div>
            </div>
        </div>
    </div>
</div>

<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
