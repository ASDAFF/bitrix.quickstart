 <?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
	
	// ключ кеширования
	$this->SetResultCacheKeys(array(
		"IST",
		"ID_IST",
		"ID_GAL",
		"KOL_PHOTO",
		"READMORE",
	));

	if ($this->StartResultCache())
	{
		switch($arParams['IST']) {
			case 'U':
			$usertype  = 'oid';
			break;
			case 'G':
			$usertype  = 'gid';
			break;
			default:
			$usertype  = 'oid';
			break;
		}
		$photo_sizes = intval($arParams['PHOTO_SIZES']);
		$arrfotos = $this->mlife_open_http('https://api.vk.com/method/photos.get?'.$usertype.'='.$arParams['ID_IST'].'&aid='.$arParams['ID_GAL'].'&extended=0&feed_type=photo&photo_sizes='.$photo_sizes);
		$getarr = false;
		if($arrfotos){
		$getarr = json_decode($arrfotos);
		$error_msg = $getarr->error->error_msg;
		}else{
		$error_msg = 'Нет соединения с сервисом';
		}
		if($arParams['KOL_PHOTO']==0) $arParams['KOL_PHOTO']=10000;
		
		if($getarr && count($getarr->response)>0) {
			$i=0;$count = $arParams['KOL_PHOTO'];
			foreach($getarr->response as $key=>$listfoto) {
				if($i<$count) {
				$fotosrc = '';
				if(is_array($listfoto->sizes)){
					$sizebig = 0;
					$fotosrc_prew = false;
					foreach($listfoto->sizes as $size) {
						if($sizebig<intval($size->width)) {
							$sizebig = $size->width;
							$fotosrc = $size->src;
						}
						if($size->width=='130') $fotosrc_prew = $size->src;
					}
				}
				elseif($listfoto->src_xxbig){
				$fotosrc=$listfoto->src_xxbig;
				}
				else if($listfoto->src_xbig and !$fotosrc){
				$fotosrc=$listfoto->src_xbig;
				}
				else if($listfoto->src_big and !$fotosrc){
				$fotosrc=$listfoto->src_big;
				}
				
					if($fotosrc){
						if ($fotosrc_prew) {
							$arResult['photo'][$key]['src'] = $fotosrc_prew;
						}else{
							$arResult['photo'][$key]['src'] = $listfoto->src;
						}
						$arResult['photo'][$key]['created'] = $listfoto->created;
						$arResult['photo'][$key]['text'] = $listfoto->text;
						$arResult['photo'][$key]['src_big'] = $fotosrc;
					}
				
				}else{
				break;
				}
				$i++;
			}
			unset($getarr);
			$arResult['error_msg']=$error_msg;
		}
		
		if($arParams['READMORE']==1) {
		$creat = '';
		if($arParams['IST']=='G') $creat = '-';
		$arResult['show_readmore_href'] = 'http://vk.com/album'.$creat.$arParams['ID_IST'].'_'.$arParams['ID_GAL'];
		}
		
		$this->IncludeComponentTemplate();
	}
?>