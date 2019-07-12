<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Contact information");
?>
<?$APPLICATION->IncludeComponent("sergeland:google.maps", "", 
	array(
		"CONTAINER_ID" => "map-canvas",
		"CONTAINER_CLASS" => "map",
		"LATITUDE" => "#LATITUDE#",
		"LONGITUDE" => "#LONGITUDE#",
		"LATITUDE_CENTER_MAP" => "#LATITUDE_CENTER#",
		"LONGITUDE_CENTER_MAP" => "#LONGITUDE_CENTER#",
		"ZOOM" => "11",
		"KEY" => "AIzaSyBbJ16uUP1tqA_-qsojvMCBV12V71rukHA",
		"SCROLLWHEEL" => "N",
		"TOUCH" => "Y",
		"MARKER_IMAGE_FILE" => "",
		"TITLE" => "#ADDRESS#",
		"CONTENT" => "#ADDRESS#",
		"CONTENT_SHOW_ONLOAD" => "N",
		"CONTENT_OFFSET_TOP" => "16",
		"CONTENT_OFFSET_RIGHT" => "0",
		"STYLES" => "[{\"featureType\": \"landscape\",\"stylers\": [{\"saturation\": -100}]}, {\"featureType\": \"poi\",\"stylers\": [{\"saturation\": -100}]}, {\"featureType\": \"road\",\"stylers\": [{\"saturation\": -100}]}, {\"featureType\": \"transit\",\"stylers\": [{\"saturation\": -100}]}, {\"featureType\": \"water\",\"stylers\": [{\"saturation\": -100}]}, {\"featureType\": \"administrative\",\"stylers\": [{\"saturation\": -100}]}]"
	),
	false
);?>
<div class="row mt-35">
	<div class="main col-md-7">
		<h1 class="page-title">Leave a message</h1>
		<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</p>
		<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "#SITE_DIR#include/form-contacts.php",
			)
		);?>
	</div>
	<div class="col-md-5">
		<div class="sidebar">
			<div class="side vertical-divider-left">
				<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => "#SITE_DIR#include/contacts-address.php",
					)
				);?>
				<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => "#SITE_DIR#include/contacts-social.php",
					)
				);?>
			</div>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>