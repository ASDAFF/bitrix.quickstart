<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($this->StartResultCache())
{
    $this->InitComponentTemplate();

    $template = & $this->GetTemplate();
    $templateFolder = $template->GetFolder() ;
    $codePath = $_SERVER["DOCUMENT_ROOT"] . $template->GetFolder() . "/code.php" ;

    /* � ����� ������� ������ ������ ���� code.php,
        � ������� ����������� �������� � $arResult */
    if(file_exists( $codePath )){
        include $codePath;
    }

    $this->ShowComponentTemplate();
}
