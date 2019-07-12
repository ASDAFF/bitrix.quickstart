<?
function getImgById($id){
	return CFile::GetFileArray( $id );
}

function getResizedImgById($id, $w, $h){
	$img = getImgById($id);

	$arFilter = '';
	$arFileTmp = CFile::ResizeImageGet(
	$img,
	array("width" => $w, "height" => $h),
	BX_RESIZE_IMAGE_PROPORTIONAL,
	true, $arFilter
	);
    
    return $arFileTmp;
}
?>