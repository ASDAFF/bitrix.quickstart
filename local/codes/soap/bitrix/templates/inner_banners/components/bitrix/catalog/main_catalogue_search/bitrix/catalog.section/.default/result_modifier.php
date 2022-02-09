<?php

 if(isset($_REQUEST['luck']))
     if ($url = $arResult['ITEMS'][0]["~DETAIL_PAGE_URL"])
         LocalRedirect(str_replace('%2F', '/', $url));
