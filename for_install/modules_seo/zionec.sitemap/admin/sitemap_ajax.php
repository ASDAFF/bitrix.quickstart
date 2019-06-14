<?php

if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
)
{
  include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
  include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/zionec.sitemap/lang/ru/admin/sitemap_ajax.php');
  include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/zionec.sitemap/classes/general/getContent.php");
  include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/zionec.sitemap/classes/general/mapping.php");
  global $DB;

  if ($_POST['act'] == 'generic_static')
  {
    $res = array();
    $res['path'] = GetMessage("A_SELECT_DATA");
    getContent::$staticFolders = array();
    $excludeDir = array(
        'bitrix',
        'upload'
    );
    $folders = array();
    $aSites = SiteMapGen::getSiteInfo();
    $res['text'] = '<form name="form_1" id="form_1"><table><tbody>';

    foreach ($aSites['sites'] as $site => $aSite)
    {
      $ROOT = $aSite['DIR'];

      getContent::showTree($ROOT, $site);

      if (!empty(getContent::$staticFolders[$site]))
      {
        $res['text'] .= '<tr><td colspan="2"><h3 class="admin-h3">' . GetMessage('A_FOR_SITE') . $aSite['NAME'] . ' [' . $site . ']</div></td></tr>';
        foreach (getContent::$staticFolders[$site] as $key => $value)
        {
          $res['text'] .= '<tr>
                    <td style="width:550px;">
                        <div onmouseover="popup.showTooltipDisplay(this)" onmouseout="popup.hideTooltipDisplay(this)" class="row-property">' . getContent::$staticFolders[$site][$key]['value'] . '</div>
                        <div style="display:none;text-align:center" class="display-tooltip">';
          if (empty(getContent::$staticFolders[$site][$key]['name']))
          {
            $text = GetMessage('A_E_FOLDER_NOT_NAME');
          }
          else
          {
            $text = getContent::$staticFolders[$site][$key]['name'];
          }

          $res['text'] .= $text . '<div class="one_tr_d"></div></div>
                        <input type="hidden" name="folder[]" value="' . getContent::$staticFolders[$site][$key]['path'] . '">
                        <input type="hidden" name="path[]" value="' . getContent::$staticFolders[$site][$key]['value'] . '">
                        <input type="hidden" name="name[]" value="' . getContent::$staticFolders[$site][$key]['name'] . '">
                        <input type="hidden" name="site[]" value="' . $site . '">
                    </td>
                    <td style="width:50px;"><div onclick="popup.delete(this)" onmouseover="popup.showTooltip(this, 0)" onmouseout="popup.hideTooltip(\'js-tooltip\')" class="row-property" style="padding: 0;text-align: center;background-color: #597DA3;color: #fff;z-index:1"><span style="position:relative;font-size: 30.6px;z-index:2;display:block">' . GetMessage('A_CLOSE') . '</span></div></td>
                    </tr>';
        }
      }
      else
      {
        $res['text'] .= '<tr><td colspan="2"><h3 class="admin-h3">' . GetMessage('A_FOR_SITE') . $aSite['NAME'] . ' [' . $site . ']</div></td></tr><tr><td colspan="2">' . GetMessage('A_E_FOLDER_NOT_FOUND') . '</td></tr>';
      }
    }
    $res['text'] .= '
        <tr>
            <td colspan="2">
                <div onclick="popup.send(\'form_1\', \'bitrix/admin/sitemap_ajax\', \'save_static_file\', popup.updateStaticLocalFile)" onmouseover="popup.showTooltip(this, 1)" onmouseout="popup.hideTooltip(\'js-tooltip\')" class="row-property-button left-margin">' . GetMessage('A_SAVE') . '</div>
            </td>
        </tr>
        </tbody>
        </table>
        </form>';

    echo getContent::jsonencode($res);
  }

  if ($_POST['act'] == 'save_static_file')
  {
    $succes = 0;
    $error = 0;
    $res['title'] = GetMessage('A_SAVE_STATIC');

    $result = getContent::saveStatic();

    if (!empty($result['error']['text']))
    {
      $res['error'] = GetMessage('A_E_STATIC_ERROR_1');
      echo \Bitrix\Main\Web\Json::encode($res);
      exit;
    }
    if (!empty($result['error']['count']))
    {
      $error = $result['error']['count'];
    }
    if (!empty($result['success']['count']))
    {
      $succes = $result['success']['count'];
    }

    $res['text'] = '<div class="adm-notification">' . GetMessage('A_SUCCESS_ROW') . $succes . '<br>' . GetMessage('A_ERROR_ROW') . $error . '</div>';
    $res['type'] = 0;
    echo getContent::jsonencode($res);
    exit;
  }

  if ($_POST['act'] == 'save_params_static_file')
  {
    $succes = 0;
    $error = 0;
    $res['title'] = GetMessage('A_SAVE_STATIC_PARAM');
    $result = getContent::saveParamsStaticFile();
    if (!empty($result['error']['count']))
    {
      $error = $result['error']['count'];
    }
    if (!empty($result['success']['count']))
    {
      $success = $result['success']['count'];
    }

    $res['text'] = '<div class="adm-notification">' . GetMessage('A_SUCCESS_ROW') . $succes . '<br>' . GetMessage('A_ERROR_ROW') . $error . '</div>';
    echo getContent::jsonencode($res);
    exit;
  }

  if ($_POST['act'] == 'update_static_local_file')
  {
    $objResult__ = getContent::getStaticOne();
    if (!empty($objResult__['ID']))
    {
      $text = '<form name="form_2" id="form_2" style="position: relative">
            <table>
                <thead>
                    <th>' . GetMessage('A_PARAM_0') . '</th>
                    <th style="cursor: pointer" onmouseover="popup.showTooltip(this, 4)" onmouseout="popup.hideTooltip(\'js-tooltip\')">' . GetMessage('A_PARAM_1') . '</th>
                    <th style="cursor: pointer" onmouseover="popup.showTooltip(this, 2)" onmouseout="popup.hideTooltip(\'js-tooltip\')">' . GetMessage('A_PARAM_2') . '</th>
                    <th style="cursor: pointer" onmouseover="popup.showTooltip(this, 3)" onmouseout="popup.hideTooltip(\'js-tooltip\')">' . GetMessage('A_PARAM_3') . '</th>
                    <th>' . GetMessage('A_PARAM_4') . '</th>
                </thead>
                <tbody>';
    }

    include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/zionec.sitemap/classes/general/mapping.php");
    $aSite = SiteMapGen::getSiteInfo();
    $arSiteTitle = array();
    $objResult = getContent::getStaticAll();

    while ($arResult = $objResult->Fetch())
    {
      if (empty($arSiteTitle[$arResult['SIT']]))
      {
        $arSiteTitle[$arResult['SIT']] = $arResult['SIT'];
        $text .= '<tr><td colspan="5"><h3 class="admin-h3">' . $aSite['sites'][$arResult['SIT']]['NAME'] . ' [' . $arResult['SIT'] . ']</h3></td></tr>';
      }
      $text .= '<tr>
                <td style="width:300px;">
                    <div onmouseover="popup.showTooltipDisplay(this)" onmouseout="popup.hideTooltipDisplay(this)" class="row-property">' . $arResult['PATH'] . '</div>
                    <div style="display:none;text-align:center" class="display-tooltip">' . $arResult['NAME'] . '<div class="one_tr_d"></div></div>
                    <input type="hidden" name="folder[]" value="' . $arResult['CODE'] . '">
                    <input type="hidden" name="id[]" value="' . $arResult['ID'] . '">
                    <input type="hidden" name="path[]" value="' . $arResult['PATH'] . '">
                    <input type="hidden" name="name[]" value="' . $arResult['NAME'] . '">
                    <input type="hidden" name="site[]" value="' . $arResult['SIT'] . '">
                </td>
                <td>
                    <select class="custom-input" style="height: 37px;margin-top: 10px;width:130px" name="freq[]">
                        <option value="none" ' . (($arResult['FREQ'] == 'none') ? 'selected="selected"' : '') . '>' . GetMessage('A_FREQ_1') . '</option>
                        <option value="always" ' . (($arResult['FREQ'] == 'always') ? 'selected="selected"' : '') . '>' . GetMessage('A_FREQ_2') . '</option>
                        <option value="hourly" ' . (($arResult['FREQ'] == 'hourly') ? 'selected="selected"' : '') . '>' . GetMessage('A_FREQ_3') . '</option>
                        <option value="daily" ' . (($arResult['FREQ'] == 'daily') ? 'selected="selected"' : '') . '>' . GetMessage('A_FREQ_4') . '</option>
                        <option value="weekly" ' . (($arResult['FREQ'] == 'weekly') ? 'selected="selected"' : '') . '>' . GetMessage('A_FREQ_5') . '</option>
                        <option value="monthly" ' . (($arResult['FREQ'] == 'monthly') ? 'selected="selected"' : '') . '>' . GetMessage('A_FREQ_6') . '</option>
                        <option value="yearly" ' . (($arResult['FREQ'] == 'yearly') ? 'selected="selected"' : '') . '>' . GetMessage('A_FREQ_7') . '</option>
                        <option value="never" ' . (($arResult['FREQ'] == 'never') ? 'selected="selected"' : '') . '>' . GetMessage('A_FREQ_7') . '</option>
                    </select>
                </td>
                <td>
                    <select class="custom-input" style="height: 37px;margin-top: 10px;width:130px" name="mod[]">
                        <option value="0">' . GetMessage('A_NO') . '</option>
                        <option ';
      $text .= ($arResult['MOD'] == 1) ? 'selected="selected" ' : ' ';
      $text .= 'value="1">' . GetMessage('A_YES') . '</option>
                    </select>
                </td>
                <td>
                    <input class="custom-input" type="text" style="margin-top: 10px;width:70px" name="priority[]" value="' . $arResult['PRIORITY'] . '">
                </td>
                <td style="width:50px;"><div onclick="popup.delete(this, ge(\'form_2\'))" onmouseover="popup.showTooltip(this, 0)" onmouseout="popup.hideTooltip(\'js-tooltip\')" class="row-property" style="padding: 0;text-align: center;background-color: #597DA3;color: #fff;z-index:1"><span style="position:relative;font-size: 30.6px;z-index:2;display:block">' . GetMessage('A_CLOSE') . '</span></div></td>
            </tr>';
    }
    if (!empty($objResult__['ID']))
    {
      $text .= '<tr>
                    <td colspan="2">
                        <div onclick="popup.send(\'form_2\', \'bitrix/admin/sitemap_ajax\', \'save_params_static_file\')" onmouseover="popup.showTooltip(this, 1)" onmouseout="popup.hideTooltip(\'js-tooltip\')" class="row-property-button left-margin">' . GetMessage('A_SAVE') . '</div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>';
    }
    echo $text;
    exit;
  }

  if ($_POST['act'] == 'generic_iblock')
  {
    CModule::IncludeModule('iblock');
    $c = 0;
    $res['title'] = GetMessage('A_LIST_IBLOCK');
    $res['text'] = '
            <form name="form_1" id="form_1">
            <table>
            <tbody>';

    $title = array();
    $aSites = SiteMapGen::getSiteInfo();
    $arIblocks = getContent::getIblockBySite();

    foreach ($aSites['sites'] as $key => $value)
    {
      if (empty($title[$key]))
      {
        $title[$key] = $key;
        $res['text'] .= '<tr><td colspan="2"><h3 class="admin-h3">' . $value['NAME'] . ' [' . $key . ']</h3></td></tr>';
      }

      foreach ($arIblocks[$key] as $index => $iblockId)
      {
        $resSelect = getContent::getIblockByOne($iblockId['ID'], $iblockId['CODE']);
        if (empty($resSelect['ID']))
        {
          $res['text'] .= '
                    <tr>
                        <td style="width:550px;">
                            <div class="row-property">' . $iblockId['NAME'] . '</div>
                            <input type="hidden" name="id[]" value="' . $iblockId['ID'] . '">
                            <input type="hidden" name="path[]" value="' . $iblockId['CODE'] . '">
                            <input type="hidden" name="name[]" value="' . $iblockId['NAME'] . '">
                            <input type="hidden" name="site[]" value="' . $key . '">
                        </td>
                        <td style="width:50px;"><div onclick="popup.delete(this)" onmouseover="popup.showTooltip(this, 0)" onmouseout="popup.hideTooltip(\'js-tooltip\')" class="row-property" style="padding: 0;text-align: center;background-color: #597DA3;color: #fff;z-index:1"><span style="position:relative;font-size: 30.6px;z-index:2;display:block">' . GetMessage('A_CLOSE') . '</span></div></td>
                    </tr>';
          $c++;
        }
      }
    }

    if ($c > 0)
    {
      $res['text'] .= '
            <tr>
                <td colspan="2">
                    <div onclick="popup.send(\'form_1\', \'bitrix/admin/sitemap_ajax\', \'save_iblock\', popup.updateIblockLocal)" onmouseover="popup.showTooltip(this, 1)" onmouseout="popup.hideTooltip(\'js-tooltip\')" class="row-property-button left-margin">' . GetMessage('A_SAVE') . '</div>
                </td>
            </tr>
            </tbody>
            </table>
            </form>';
    }
    else
    {
      $res['text'] = GetMessage('A_E_NO_IBLOCK');
    }
    echo getContent::jsonencode($res);
    exit;
  }

  if ($_POST['act'] == 'save_iblock')
  {
    $succes = 0;
    $error = 0;
    $res['title'] = GetMessage('A_SAVE_IBLOCK');;

    $result = getContent::saveIblock();
    if (!empty($result['success']['count']))
    {
      $success = $result['success']['count'];
    }
    if (!empty($result['error']['count']))
    {
      $error = $result['error']['count'];
    }

    $res['text'] = '<div class="adm-notification">' . GetMessage('A_SUCCESS_ROW') . $succes . '<br>' . GetMessage('A_ERROR_ROW') . $error . '</div>';
    $res['type'] = 1;
    echo getContent::jsonencode($res);
    exit;
  }

  if ($_POST['act'] == 'update_iblock_local')
  {
    echo getContent::showIblockView($aSite);
    exit;
  }

  if ($_POST['act'] == 'save_params_iblock')
  {
    $succes = 0;
    $error = 0;
    $res['title'] = GetMessage('A_SAVE_IBLOCK_PARAM');

    $result = getContent::saveParamsIblock();
    if (!empty($result['success']['count']))
    {
      $success = $result['success']['count'];
    }
    if (!empty($result['error']['count']))
    {
      $error = $result['error']['count'];
    }

    $res['text'] = '<div class="adm-notification">' . GetMessage('A_SUCCESS_ROW') . $succes . '<br>' . GetMessage('A_ERROR_ROW') . $error . '</div>';
    echo getContent::jsonencode($res);
    exit;
  }

  if ($_POST['act'] == 'generic')
  {
    $res['title'] = GetMessage('A_GENERATE');
    $start = microtime(true);
    $state = SiteMapGen::Generate();
    $last = microtime(true) - $start;
    if (SiteMapGen::$gzip)
    {
      $line = '';
    }
    else
    {
      $line = GetMessage('A_SUCCESS_GENERATE');
    }

    $allGenerationTime = round($last, 4);
    if ($state === -1)
    {
      $res['text'] = '<div class="adm-notification">' . GetMessage('A_TEST') . '</div>';
    }
    else if ($state === -2)
    {
      $res['text'] = GetMessage('A_E_DOMEN');
    }
    else
    {
      $res['text'] = '<div class="adm-notification">' . GetMessage('A_GENERATE_TIME') . '<strong>' . $allGenerationTime . '</strong><br>' . GetMessage('A_FILE_WRITE') . '<strong>' . SiteMapGen::$count . '</strong><br>' . $line;
    }

    echo getContent::jsonencode($res);
    exit;
  }

  if ($_POST['act'] == 'save_generic_property')
  {
    getContent::saveGenericProperty();
    echo getContent::jsonencode($res);
    exit;
  }

  if ($_POST['act'] == 'help')
  {
    $res['title'] = GetMessage('A_REPORT');
    $res['text'] = GetMessage('A_TEXT_REPORT');
    echo getContent::jsonencode($res);
    exit;
  }
}
