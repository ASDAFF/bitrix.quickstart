<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("blog");
CModule::IncludeModule("socialnetwork");
//SELECT wp_bp_activity.component, wp_bp_activity.content, wp_bp_activity.date_recorded, wp_users.user_login FROM wp_bp_activity, wp_users WHERE wp_bp_activity.type='activity_update' AND wp_bp_activity.user_id=wp_users.ID
$user = new CUser;

$users = array();






/* количество записей */
$query = " SELECT COUNT(*) as CNT FROM {$arResult['prefix']}bp_activity, {$arResult['prefix']}users WHERE {$arResult['prefix']}bp_activity.type='activity_update' AND {$arResult['prefix']}bp_activity.user_id={$arResult['prefix']}users.ID";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);


$wizard =& $this->GetWizard();
$site_id = $wizard->GetVar("siteID");

$arF = array(
    "SITE_ID" => $site_id,
    "NAME" => "WP_BLOGS"
);

$bg = CBlogGroup::GetList(array(), array('NAME' => 'WP_BLOGS'))->GetNext();
if(!$bg['ID'])
	$bg['ID'] = CBlogGroup::Add($arF);
	
//print_r($bg['ID']); die();

/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

if($left > $count["CNT"])
{	
	
	$left = 0;
	$right = 10;

	/* Две эти строчки непосредственно завершают шаг и скрипт переходит к следеющему файлу(если он существует) */
	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
}
else
{
	global $USER;

	$query = " SELECT  {$arResult['prefix']}bp_activity.type, {$arResult['prefix']}bp_activity.secondary_item_id, {$arResult['prefix']}bp_activity.item_id, {$arResult['prefix']}bp_activity.id, {$arResult['prefix']}bp_activity.component, {$arResult['prefix']}bp_activity.content, {$arResult['prefix']}bp_activity.date_recorded, {$arResult['prefix']}users.user_login FROM {$arResult['prefix']}bp_activity, {$arResult['prefix']}users WHERE ({$arResult['prefix']}bp_activity.type='activity_update' OR {$arResult['prefix']}bp_activity.type='activity_comment') AND {$arResult['prefix']}bp_activity.user_id={$arResult['prefix']}users.ID ORDER BY {$arResult['prefix']}bp_activity.id ASC LIMIT ".$left.", 10";
	//$query = "SELECT * FROM ".$arResult["prefix"]."users LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
	
	
	
				global $USER;
				$usr = $USER->GetByLogin($arItem['user_login'])->GetNext();	
	
				if($arItem['type'] == 'activity_comment')
				{
					if(CBlogComment::GetList(array(), array('AUTHOR_IP' => $arItem['id']))->GetNext()) continue;
					
				//die();
					$comm = CBlogComment::GetList(array(), array('AUTHOR_IP' => $arItem['secondary_item_id']))->GetNext();
					if(!$comm['ID'])
					{
						$post = CBlogPost::GetList(array(), array("CODE" => $arItem['secondary_item_id']))->GetNext();
					}
					else
					{
						$post['ID'] = $comm['POST_ID'];
						$post['BLOG_ID'] = $comm['BLOG_ID'];
					}
					
					
					
					//$post = CBlogPost::GetList(array(), array("CODE" => $arItem['secondary_item_id']))->GetNext();
					$arFields = array(
						//"TITLE" => 'Мое первое сообщение в блоге',
						"POST_TEXT" => $arItem['content'],
						"BLOG_ID" => $post["BLOG_ID"],
						"POST_ID" => $post["ID"],						
						"AUTHOR_ID" => $usr["ID"], //добавляем неанонимный комментарий, 
						"PARENT_ID" => $comm['ID'], //комментарий привязан к сообщению
						"AUTHOR_IP" => $arItem['id'],
						//в противном случае необходимо задать AUTHOR_NAME, AUTHOR_EMAIL
						"DATE_CREATE" => date('d.m.Y h:i', strtotime($arItem['date_recorded'])), 
						//"AUTHOR_IP" => $UserIP[0],
						//"AUTHOR_IP1" => $UserIP[1]
					);
					
					global $APPLICATION;
					
					$newID = CBlogComment::Add($arFields);
					
					if(IntVal($newID)>0)
					{
						echo "Новый комментарий [".$newID."] добавлен.";
					}
					else
					{
						if ($ex = $APPLICATION->GetException())
							echo $ex->GetString();
							//die();
					}


						//print_r($post); echo '<br/><br/>';	
					
					//die();
				}
	
	
				
					$blog = CBlog::GetByOwnerID($usr['ID']);
					if(!$blog)
					{
						
						$arFields = array(
							"NAME" => $arItem['user_login'],							
							"GROUP_ID" => $bg['ID'],
							"ENABLE_IMG_VERIF" => 'Y',
							"EMAIL_NOTIFY" => 'Y',
							"ENABLE_RSS" => "Y",
							"URL" => "{$arItem['user_login']}-blog",
							"USE_SOCNET" => 'Y',
							"ACTIVE" => "Y",
							"OWNER_ID" => $usr['ID']
						);

						$blog['ID'] = CBlog::Add($arFields);						
					}
					
					$post = CBlogPost::GetList(array(), array("CODE" => $arItem['id']))->GetNext();
					
					//print_r($post);
					//die();
					
					if($post) continue;
	
		switch($arItem['component'])
		{
			case 'activity':												
					global $DB;												
					$arFields = array(
							"TITLE" => $arItem['content'],
							"DETAIL_TEXT" => $arItem['content'],
							"BLOG_ID" => $blog['ID'],
							"AUTHOR_ID" => $usr['ID'],
							"=DATE_CREATE" => $DB->GetNowFunction(),
							"DATE_PUBLISH" =>date('d.m.Y h:i', strtotime($arItem['date_recorded'])),			
							"PUBLISH_STATUS" => 'P',
							"ENABLE_TRACKBACK" => 'N',
							"ENABLE_COMMENTS" => 'Y'	,
							"MICRO" => 'N',
							"CODE" => $arItem['id'],
							"PERMS_P" => Array("1" => BLOG_PERMS_READ, "2" => BLOG_PERMS_READ),
							"PERMS_C" => Array("1" => BLOG_PERMS_READ, "2" => BLOG_PERMS_WRITE)
					);
					$newID = CBlogPost::Add($arFields);

					
				break;
			case 'groups':
				

						$query = " SELECT  * FROM {$arResult['prefix']}bp_groups, {$arResult['prefix']}users WHERE {$arResult['prefix']}bp_groups.id='{$arItem['item_id']}' AND {$arResult['prefix']}bp_groups.creator_id = {$arResult['prefix']}users.ID";
						$res = mysql_query($query, $link);
						$arI= mysql_fetch_assoc($res);			
						$group = CSocNetGroup::GetList(array(), array("NAME" => $arI['name']))->GetNext();			
						if(!$group['ID'])
						{				
							$subj = CSocNetGroupSubject::GetList(array(), array('NAME' => 'WP'))->GetNext();
							if(!$subj['ID'])
								$subj['ID'] = CSocNetGroupSubject::Add(array('SITE_ID' => $site_id, 'NAME' => 'WP'));				
							$arFields = array(
								"SITE_ID" => $site_id,
								"NAME" => $arI['name'],
								"DESCRIPTION" => $arI['description'],
								"VISIBLE" => "Y",
								"OPENED" => "Y",
								"CLOSED" => "N",
								"SUBJECT_ID" => $subj['ID'],									
								"INITIATE_PERMS" => SONET_ROLES_OWNER,
								"SPAM_PERMS" => SONET_ROLES_USER,
							);				
							$usr1 = $USER->GetByLogin($arI['user_login'])->GetNext();		
							$group['ID'] = CSocNetGroup::CreateGroup($usr1['ID'], $arFields);				
							if($usr['ID'] != $usr1['ID'])
								CSocNetUserToGroup::Add(array('USER_ID' => $usr['ID'], "GROUP" => $group['ID'], "ROLE" => SONET_ROLES_USER));				
						}
						
						$blog = CBlog::GetList(array(), array("SOCNET_GROUP_ID" => $group['ID']))->GetNext();
						if(!$blog)
						{
									
									$arFields = array(
										"NAME" => $group['ID']."-blog",							
										"GROUP_ID" => $bg['ID'],
										"SOCNET_GROUP_ID" => $group['ID'],
										"ENABLE_IMG_VERIF" => 'Y',
										"EMAIL_NOTIFY" => 'Y',
										"ENABLE_RSS" => "Y",
										"URL" => $group['ID']."-blog",
										"USE_SOCNET" => 'Y',
										"ACTIVE" => "Y",
										"OWNER_ID" => $usr['ID']
									);

									$blog['ID'] = CBlog::Add($arFields);						
						}

						$post = CBlogPost::GetList(array(), array("CODE" => $arItem['id']))->GetNext();
						if($post) continue;
						
						global $DB;												
						$arFields = array(
										"TITLE" => $arItem['content'],
										"DETAIL_TEXT" => $arItem['content'],
										"BLOG_ID" => $blog['ID'],
										"AUTHOR_ID" => $usr['ID'],
										"=DATE_CREATE" => $DB->GetNowFunction(),
										"DATE_PUBLISH" =>date('d.m.Y h:i', strtotime($arItem['date_recorded'])),			
										"PUBLISH_STATUS" => 'P',
										"ENABLE_TRACKBACK" => 'N',
										"ENABLE_COMMENTS" => 'Y'	,
										"MICRO" => 'N',
										"CODE" => $arItem['id'],
										"PERMS_P" => Array("1" => BLOG_PERMS_READ, "2" => BLOG_PERMS_READ),
										"PERMS_C" => Array("1" => BLOG_PERMS_READ, "2" => BLOG_PERMS_WRITE)
						);
						$newID = CBlogPost::Add($arFields);

				
			

				break;
			default:
				continue;
				break;
		}

	}

	//die();
	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
