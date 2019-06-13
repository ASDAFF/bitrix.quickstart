<?php
$MESS['AMAS_PAGE_TITLE'] = 'Настройки';
$MESS['AMAS_TAB_TITLE']  = 'Настройки сайта -  ';

$MESS['AMAS_TAB_LIST']       = array(
	array(
		 'DIV'   => 'tab_mail',
		 'TAB'   => 'MESSAGE',
		 'TITLE' => 'Оформление писем',
	),
	array(
		 'DIV'   => 'tab_dkim',
		 'TAB'   => 'DKIM',
		 'TITLE' => 'Настройки DKIM',
	),
);


//DKIM
$MESS['AMAS_DKIM_ON']       = 'Включить DKIM в письмах';
$MESS['AMAS_DKIM_d']        = 'd='; //домен
$MESS['AMAS_DKIM_s']        = 's='; //селектор
$MESS['AMAS_DKIM_i']        = 'i='; //обратный адрес для домена // Идентификатор пользователя (AUID), за которой следует «@», за которой следует домен из тега "d="
$MESS['AMAS_DKIM_h']        = 'h='; //подписываемые заголовки
$MESS['AMAS_DKIM_h_values'] = array('from', 'to', 'subject');

//RSA
$MESS['AMAS_RSA_PUBLIC_KEY']  = 'Открытый ключ';
$MESS['AMAS_RSA_PRIVATE_KEY'] = 'Закрытый ключ';


$MESS['AMAS_RSA_PUBLIC_KEY_EXAMPLE']  = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCGePf6WOlKUSrRdcFyZpU5rAvU
S4g8kCfZa7HRoSOiu2+0fzhX3uSjscma+YWhL0Zl/0/D/fOkJEb2PaTyNbg/4mEq
XXf1o3m4p0HotSOO09otXBgFoGfydzjd95dBZWpLa02MIdzg+VhfIcy6lUgcEoXp
zukV52oQAsRZuzp4dQIDAQAB
-----END PUBLIC KEY-----';

$MESS['AMAS_RSA_PRIVATE_KEY_EXAMPLE']  = '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQCGePf6WOlKUSrRdcFyZpU5rAvUS4g8kCfZa7HRoSOiu2+0fzhC
3uSjscma+YWhL0Zl/0/D/fOkJEb2PaTyNbg/4mEqXXf1o3m4p0HotSOO09otXGgF
oGfydzjd95dBZWpLa02MIdzg+VhfIcy6lUgcEoXpzukV52oQAsRZuzp4dQIDAQAB
AoGBAIUFEuBtpVBjnESZBKQi/8iN/SGjGgA25YR4uOzSRssKOQTGuCPGE0wuaWzJ
GbwJZM0u0nvhMWsUi7G35vvF0hsGiyYy5LgS5VMkSNpWSDXT9CSo/GzI8AQGQ/Bs
8bZUy3IJGc8Vpv2w10EAV4UogxXuVvh3jHQgxWRsC5bM5dYhAkEAzxU07HvFz9kb
7jww1RErNxtY5zAtI4g1QTppgAngpRoBGzuB62E9WfahOwHau/c6RGsBnRX4lqtH
DnTG5z5D/QJBAKY81/SCgM2lsxsxuMduy7QNOVi3CwADGuuCyj+SpAKOdSvjRNv8
o4pSo1jcYLAZ0EdGpS47RUnqZzj3KtRIY9kCQDb/zOSaIvmHEjH97oJIYw/pxXzx
gKuVO3+tgeOtu+pds7mF3oWjd+Xy6POBFJUjnmgZe347HaD2sJKYVVOJaAkCQQCJ
7gfdB38pomdW7J19VA/Ol/5R/qYw32KxsDZxYxGUUXEk9hBHWyqydXi1HT1YQELR
MOuTHXiTKNt2p5YwxtOZAkBghM5DFlpOJd6r8uSM0sIoh7qrF1flJnpUC33rWsum
+Df4qnc9XwrpNsxa7G7axsY9VV13o9Vc5cHywfndry4c
-----END RSA PRIVATE KEY-----';


//MAIL
$MESS['AMAS_MAIL_ON']       = 'Включить оформление в письмах';
$MESS['AMAS_MAIL_MACROS_HINT']  = '#WORK_AREA# - макрос, в который подставится текст писем сайта, используйте в нужном месте шаблона';

$MESS['AMAS_MAIL_DEFAULT_THEME'] = '<style type="text/css">
  a{
    word-wrap:break-word;
  }
  table{
    border-collapse:collapse;
    table-layout: fixed;
  }
  h1,h2,h3,h4,h5,h6{
    display:block;
    margin:0;
    padding:0;
  }
  img,a img{
    border:0;
    margin:0;
    outline:none;
    text-decoration:none;
  }
  body{
    height:100% !important;
    margin:0;
    padding:0;
    font-family: "Open Sans", Arial, Helvetica, sans-serif;
    width:100% !important;
    background: #f6f6f6;
  }
  img{
    -ms-interpolation-mode:bicubic;
  }
  table{
    mso-table-lspace:0pt;
    mso-table-rspace:0pt;
  }
  p,a,li,td{
    mso-line-height-rule:exactly;
  }

  p,a,li,td,body,table,blockquote{
    -ms-text-size-adjust:100%;
    -webkit-text-size-adjust:100%;
  }

  h1{
    color:#606060 !important;
    font-family: Arial, Helvetica, sans-serif;
    font-size:40px;
    font-style:normal;
    font-weight:bold;
    line-height:125%;
    letter-spacing:-1px;
    text-align:left;
  }
  h2{
    color:#404040 !important;
    font-family: Arial, Helvetica, sans-serif;
    font-size:26px;
    font-style:normal;
    font-weight:normal;
    line-height:125%;
    letter-spacing:-.75px;
    text-align:left;
  }
  h3{
    color:#606060 !important;
    font-family: Arial, Helvetica, sans-serif;
    font-size:18px;
    font-style:normal;
    font-weight:bold;
    line-height:125%;
    letter-spacing:-.5px;
    text-align:left;
  }
  h4{
    color:#808080 !important;
    font-family: Arial, Helvetica, sans-serif;
    font-size:16px;
    font-style:normal;
    font-weight:bold;
    line-height:125%;
    letter-spacing:normal;
    text-align:left;
  }
</style>
<div style="background-color:#f6f6f6;padding:50px 0;font-size: 14px;color: #000;">
  <div style="max-width:600px;overflow-y:auto;margin: 0 auto;padding: 15px 30px 30px 30px;background-color:#ffffff;">
    <table style="border-collapse: collapse;border-bottom: 2px solid #5188d4; margin-bottom:15px; width:100%" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
      <td border="0" valign="middle" align="left">
            %ЛОГОТИП%
      </td>
      <td border="0" valign="middle" align="right">
        <p style="font-size:18px;line-height:18px;margin:0 0 0 0;padding:0 0 10px 0;">
           %ТЕЛЕФОН%
        </p>
        <p style="font-size:18px;line-height:18px;margin:0 0 0 0;padding:0 0 15px 0;">
            %E-MAIL%
        </p>
      </td>
    </tr>
    </tbody>
    </table>
     #WORK_AREA#
  </div>
  <div style="color:#2f2f2f;text-align:center;font-size:12px;margin-top:30px">
     &copy; 2018 <a rel="noopener noreferrer" href="mailto:%E-MAIL%" target="_blank">%E-MAIL%</a>
  </div>
</div>';
