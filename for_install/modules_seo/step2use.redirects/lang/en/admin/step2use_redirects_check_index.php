<?php
$MESS['S2U_CHECK_INDEX'] = 'Check availability of pages from the index of search engines';
$MESS['S2U_REINDEX_RESULT_TITLE'] = 'Run the index ...';
$MESS['S2U_REINDEX_RESULT_TITLE_END'] = 'Run the index is complete! <a href="step2use_redirects_404 list.php"> View transitions of broken url </a>';
$MESS['S2U_REINDEX_RESULT'] = 'Last checked page of issue: # PAGE # <br/> Checked links from the index: # CNT # <br/> Last proven link: # LAST # ';
$MESS['S2U_SE'] = 'Search engine';
$MESS['S2U_YANDEX'] = 'Yandex';
$MESS['S2U_PAGE_N'] = 'Start from the page в„–';
$MESS['S2U_SLEEP'] = 'Timeout between pages (seconds).';
$MESS['S2U_SLEEP_MIN'] = 'min';
$MESS['S2U_SLEEP_MAX'] = 'max';
$MESS['S2U_SITE'] = 'Site';
$MESS['S2U_REINDEX_NO_SITE'] = 'Could not determine the server URL of the selected site!';
$MESS['S2U_REINDEX_NO_SITE_DESC'] = 'For proper operation, you must specify the server URL <ahref="http://bitrix-test.loc/bitrix/admin/site_edit.php?lang=ru&LID=#SITE_ID#&tabControl_active_tab=edit1">on site settings page </a>';
$MESS['S2U_DESCR'] = 'The mechanism is designed to detect the broken links that are in the search engine index';
$MESS['S2U_DESCR'] .= '<br/>Verification is carried out automatically log on pages that are in the index. Thus generating an artificial traffic.';
$MESS['S2U_DESCR'] .= '<br/><br/><b>Restrictions on Yandex</b>';
$MESS['S2U_DESCR'] .= '<br/>URL indexed pages taken from the SERPs Yandex. In this connection, a limit to the number of results. Lots can be obtained 100 pages (0 to 99 pages, inclusive), ie 1000 results.';
$MESS['S2U_DESCR'] .= '<br/>In addition, when referring to the results of the issuance of Yandex can block the display of the results and the process is interrupted. The script remembers the last page tested. If there is a suspicion that Yandex blocks issue, we recommend that after some time to start the process again from the last verified page.';
$MESS['S2U_REINDEX'] = 'Start checking';
$MESS['S2U_REINDEX_STOP'] = 'Stop';
$MESS['S2U_REINDEX_CONTINUE'] = 'Continue';
?>
