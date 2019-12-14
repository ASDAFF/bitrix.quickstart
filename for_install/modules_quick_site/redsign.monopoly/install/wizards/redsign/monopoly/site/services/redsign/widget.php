<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)
	die();

if(IsModuleInstalled('redsign.monopoly')){

	COption::SetOptionString('redsign.monopoly', 'headType', 'type1' );
	COption::SetOptionString('redsign.monopoly', 'headStyle', 'style1' );
	COption::SetOptionString('redsign.monopoly', 'filterType', 'ftype1' );
	COption::SetOptionString('redsign.monopoly', 'sidebarPos', 'pos1' );
	
	COption::SetOptionString('redsign.monopoly', 'MSFichi', 'Y' );
	COption::SetOptionString('redsign.monopoly', 'MSCatalog', 'Y' );
	COption::SetOptionString('redsign.monopoly', 'MSService', 'Y' );
	COption::SetOptionString('redsign.monopoly', 'MSAboutAndReviews', 'Y' );
	COption::SetOptionString('redsign.monopoly', 'MSNews', 'Y' );
	COption::SetOptionString('redsign.monopoly', 'MSPartners', 'Y' );
	COption::SetOptionString('redsign.monopoly', 'MSGallery', 'Y' );

}