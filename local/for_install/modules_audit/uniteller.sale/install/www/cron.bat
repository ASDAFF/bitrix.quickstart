set cron_path="C:\Program Files (x86)\Bitrix Environment\www\"
set phpexe_path="C:\Program Files (x86)\Bitrix Environment\apache2\zendserver\bin\php.exe"
set phpini_path="C:\Program Files (x86)\Bitrix Environment\apache2\zendserver\etc\php.ini"
set ut_path="C:\Program Files (x86)\Bitrix Environment\www\personal\ordercheck\cron.php"
cd %cron_path%
cd ..
%phpexe_path% -c %phpini_path% %ut_path%