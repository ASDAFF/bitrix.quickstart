<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!CModule::IncludeModule("blog"))
	return false;

$arComponentParameters = Array(
	"GROUPS" => array(
		"VARIABLE_ALIASES" => array(
			"NAME" => GetMessage("B_VARIABLE_ALIASES"),
		),
	),
	"PARAMETERS" => Array(
		"ID" => Array(
				"NAME" => GetMessage("BPC_ID"),
				"TYPE" => "STRING",
				"DEFAULT" => "={\$id}",
				"PARENT" => "DATA_SOURCE",
			),
		"BLOG_URL" => Array(
				"NAME" => GetMessage("BPC_BLOG_URL"),
				"TYPE" => "STRING",
				"DEFAULT" => "={\$blog}",
				"PARENT" => "DATA_SOURCE",
			),
		"PATH_TO_BLOG" => Array(
			"NAME" => GetMessage("BPC_PATH_TO_BLOG"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_USER" => Array(
			"NAME" => GetMessage("BPC_PATH_TO_USER"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_SMILE" => Array(
			"NAME" => GetMessage("BPC_PATH_TO_SMILE"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"BLOG_VAR" => Array(
			"NAME" => GetMessage("BPC_BLOG_VAR"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "VARIABLE_ALIASES",
		),
		"POST_VAR" => Array(
			"NAME" => GetMessage("BPC_POST_VAR"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "VARIABLE_ALIASES",
		),
		"USER_VAR" => Array(
			"NAME" => GetMessage("BPC_USER_VAR"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "VARIABLE_ALIASES",
		),
		"PAGE_VAR" => Array(
			"NAME" => GetMessage("BPC_PAGE_VAR"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "VARIABLE_ALIASES",
		),
		"COMMENT_ID_VAR" => Array(
			"NAME" => GetMessage("BPC_COMMENT_ID_VAR"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "VARIABLE_ALIASES",
		),
		"COMMENTS_COUNT" => Array(
				"NAME" => GetMessage("BPC_COMMENTS_COUNT"),
				"TYPE" => "STRING",
				"DEFAULT" => 25,
				"PARENT" => "VISUAL",
			),
		"DATE_TIME_FORMAT" => CComponentUtil::GetDateTimeFormatField(GetMessage("BC_DATE_TIME_FORMAT"), "VISUAL"),		
		"CACHE_TIME" => array("DEFAULT"=>"86400"),
		"AJAX_POST" => Array(
		  	"NAME" => GetMessage("BPC_AJAX_POST"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"VALUE" => "Y",
			"DEFAULT" =>"",
			"PARENT" => "ADDITIONAL_SETTINGS",
			),		
		"SIMPLE_COMMENT" => Array(
		  	"NAME" => GetMessage("BPC_SIMPLE_COMMENT"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"VALUE" => "Y",
			"DEFAULT" =>"",
			"PARENT" => "ADDITIONAL_SETTINGS",
		
			),		
		"USE_ASC_PAGING" => Array(
		  	"NAME" => GetMessage("BPC_USE_ASC_PAGING"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"VALUE" => "Y",
			"DEFAULT" =>"",
			"PARENT" => "ADDITIONAL_SETTINGS",
			),
		"SHOW_RATING" => array(
			"NAME" => GetMessage("SHOW_RATING"),
			"TYPE" => "LIST",
			"VALUES" => Array(
				"" => GetMessage("SHOW_RATING_CONFIG"),
				"Y" => GetMessage("MAIN_YES"),
				"N" => GetMessage("MAIN_NO"),
			),
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"RATING_TYPE" => Array(
			"NAME" => GetMessage("RATING_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => Array(
				"" => GetMessage("RATING_TYPE_CONFIG"),
				"like" => GetMessage("RATING_TYPE_LIKE_TEXT"),
				"like_graphic" => GetMessage("RATING_TYPE_LIKE_GRAPHIC"),
				"standart_text" => GetMessage("RATING_TYPE_STANDART_TEXT"),
				"standart" => GetMessage("RATING_TYPE_STANDART_GRAPHIC"),
			),
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),	
		"SMILES_COUNT" => Array(
				"NAME" => GetMessage("BPC_SMILES_COUNT"),
				"TYPE" => "STRING",
				"DEFAULT" => 4,
				"PARENT" => "VISUAL",
			),
		"IMAGE_MAX_WIDTH" => Array(
				"NAME" => GetMessage("BPC_IMAGE_MAX_WIDTH"),
				"TYPE" => "STRING",
				"DEFAULT" => 600,
				"PARENT" => "VISUAL",
			),		
		"IMAGE_MAX_HEIGHT" => Array(
				"NAME" => GetMessage("BPC_IMAGE_MAX_HEIGHT"),
				"TYPE" => "STRING",
				"DEFAULT" => 600,
				"PARENT" => "VISUAL",
			),
		"EDITOR_RESIZABLE" => Array(
				"NAME" => GetMessage("BPC_EDITOR_RESIZABLE"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
				"PARENT" => "VISUAL",
			),		
		"EDITOR_DEFAULT_HEIGHT" => Array(
				"NAME" => GetMessage("BPC_EDITOR_DEFAULT_HEIGHT"),
				"TYPE" => "STRING",
				"DEFAULT" => 200,
				"PARENT" => "VISUAL",
			),
		"EDITOR_CODE_DEFAULT" => Array(
				"NAME" => GetMessage("BPC_EDITOR_CODE_DEFAULT"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
				"PARENT" => "VISUAL",
			),
		"ALLOW_VIDEO" => Array(
				"NAME" => GetMessage("BPC_ALLOW_VIDEO"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
				"PARENT" => "ADDITIONAL_SETTINGS",
			),
		"ALLOW_IMAGE_UPLOAD" => Array(
				"NAME" => GetMessage("BPC_ALLOW_IMAGE_UPLOAD"),
				"TYPE" => "LIST",
				"VALUES" => Array(
						"A" => GetMessage("BPC_ALLOW_IMAGE_UPLOAD_A"),
						"R" => GetMessage("BPC_ALLOW_IMAGE_UPLOAD_R"),
						"N" => GetMessage("BPC_ALLOW_IMAGE_UPLOAD_N"),
					),
				"MULTIPLE" => "N",
				"DEFAULT" => "N",
				"PARENT" => "ADDITIONAL_SETTINGS",
			),
		"SHOW_SPAM" => Array(
				"NAME" => GetMessage("BPC_SHOW_SPAM"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
				"PARENT" => "ADDITIONAL_SETTINGS",
			),
		"NO_URL_IN_COMMENTS" => Array(
				"NAME" => GetMessage("BPC_NO_URL_IN_COMMENTS"),
				"TYPE" => "LIST",
				"VALUES" => Array(
						"" => GetMessage("BPC_NO_URL_IN_COMMENTS_N"),
						"A" => GetMessage("BPC_NO_URL_IN_COMMENTS_A"),
						"L" => GetMessage("BPC_NO_URL_IN_COMMENTS_L"),
					),
				"MULTIPLE" => "N",
				"DEFAULT" => "",
				"PARENT" => "ADDITIONAL_SETTINGS",
			),
		"NO_URL_IN_COMMENTS_AUTHORITY" => Array(
				"NAME" => GetMessage("BPC_NO_URL_IN_COMMENTS_AUTHORITY"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"PARENT" => "ADDITIONAL_SETTINGS",
			),

	)
);
?>