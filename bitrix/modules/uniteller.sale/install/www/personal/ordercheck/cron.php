<?php
/**
 * ���������� �� cron.bat. ������ ��������� � ��������� ������, ��� ����, ����� ��������� ����� UnitellerAgent();.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */
ignore_user_abort(true);
set_time_limit(0);

// �������� ������, ��� ��� �������� ����.
define('UNITELLER_AGENT', true);

// ��� ����������� ������� � 1C-Bitrix ������������ ������������ $_SERVER['DOCUMENT_ROOT'].
chdir(dirname(__FILE__) . '../../../');
$_SERVER['DOCUMENT_ROOT'] = getcwd();

// ����� ���������� � �������.
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');