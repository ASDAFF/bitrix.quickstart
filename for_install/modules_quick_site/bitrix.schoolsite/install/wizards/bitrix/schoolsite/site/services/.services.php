<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
    "main" => Array(
        "NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
        "STAGES" => Array(
            "files.php",
            "template.php", //Install template
            "theme.php", //Install theme
            "groups.php", //Create user groups
            "menu.php", // Install menu
            "options.php", //Install module options
        ),
    ),
   
    "iblock" => Array(
        "NAME" => GetMessage("SERVICE_STRUCTURE"),
        "STAGES" => Array(
            "types.php",
            "news.php",
            "events.php",

            "photogallery.php",
            "video.php",
   
            "students_files.php",
            "teachers_files.php",
            "parents_files.php",
   
            "teachers.php",
            "audience.php",
            "subjects.php",
            
            "parents_questions.php",
            "students_questions.php",
            "applications_for_admission.php",
            "addevent.php",
        ),
    ),

    "fileman" => Array(
        "NAME" => GetMessage("SERVICE_FILEMAN"),
    ),
 
    "medialibrary" => Array(
        "NAME" => GetMessage("SERVICE_MEDIALIBRARY"),
        "MODULE_ID" => Array("fileman"),
        "STAGES" => Array("index.php"),
        "DESCRIPTION" => GetMessage("SERVICE_MEDIALIBRARY_DESC")
    ),
 
);
?>
