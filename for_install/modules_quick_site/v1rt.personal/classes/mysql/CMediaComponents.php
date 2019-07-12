<?

class CMediaComponents
{
    public static function getListName(array $arFolders = array())
    {
        global $DB;
        $sql = "SELECT ID, NAME FROM b_medialib_collection WHERE ID > 0 AND ACTIVE = 'Y' AND ML_TYPE = '1'";
        $res = $DB->Query($sql);
        if(mysql_num_rows($res->result) > 0)
        {
            while($media = mysql_fetch_array($res->result, MYSQL_ASSOC))
                $arFolders[$media["ID"]] = "[".$media["ID"]."] ".substr($media["NAME"], 0, 50);
        }

        return $arFolders;
    }


    public static function getChildren($id)
    {
        global $DB;
        $sql = "SELECT ID FROM b_medialib_collection WHERE PARENT_ID = ".$id." AND ACTIVE = 'Y' AND ML_TYPE = '1'";
        $res = $DB->Query($sql);
        while($g = $res->fetch())
            $folder[] = $g["ID"];

        return $folder;
    }


    public static function getList($folders, $match = "", $detail = "")
    {
        global $DB;
        $count = count($folders);

        if($folders[0] != "")
        {
            foreach($folders as $i=>$id)
            {
                $sqlOR .= "ID = ".$id;
                if(($i+1) < $count)
                    $sqlOR .= " OR ";
            }
            $sql = "SELECT ID, NAME, DESCRIPTION FROM b_medialib_collection WHERE ($sqlOR) AND ACTIVE = 'Y' AND ML_TYPE = '1'";
            $res = $DB->Query($sql);
        }
        else
        {
            $sql = "SELECT ID, NAME, DESCRIPTION FROM b_medialib_collection WHERE ID > 0 AND ACTIVE = 'Y' AND ML_TYPE = '1'";
            $res = $DB->Query($sql);
        }

        if(mysql_num_rows($res->result) > 0)
        {
            $i = 0;
            while($media = mysql_fetch_array($res->result, MYSQL_ASSOC))
            {
                $gallery[$i] = $media;
                $gallery[$i]["DETAIL_PAGE_URL"] = str_replace("#$match[1]#", $gallery[$i]["ID"], $detail);
                $i++;
            }
        }

        return $gallery;
    }


    public static function getTitle($id)
    {
        global $DB;
        $sql_title = "SELECT NAME FROM b_medialib_collection WHERE ID = '".$id."'";
        $result = $DB->Query($sql_title);
        $title = @mysql_fetch_array($result->result, MYSQL_ASSOC);

        return $title;
    }


    public static function getImages($id, $random = "N", $count = 0)
    {
        global $DB;

        //1.0.3 bug fix
        if(!is_array($id))
            $id = array($id);

        $sql_1 = "SELECT ITEM_ID FROM b_medialib_collection_item WHERE COLLECTION_ID = '".$id[0]."'";	// добавил индекс [0]
    	for($i = 1; $i < count($id); $i++)	// добавил этот цикл
    		$sql_1 .= "OR COLLECTION_ID='".$id[$i]."'";
        //Вытаскиваем ID файлов из таблицы b_file
        if($random == "N")
            $sql_2 = "SELECT SOURCE_ID,NAME,DESCRIPTION FROM b_medialib_item WHERE ID IN (".$sql_1.") ORDER BY NAME ASC";
        else
        {
            if($count != 0 && is_numeric($count))
                $sql_2 = "SELECT SOURCE_ID,NAME,DESCRIPTION FROM b_medialib_item WHERE ID IN (".$sql_1.") ORDER BY RAND(), NAME ASC";
            else
                $sql_2 = "SELECT SOURCE_ID,NAME,DESCRIPTION FROM b_medialib_item WHERE ID IN (".$sql_1.") ORDER BY RAND(), NAME ASC LIMIT ".$count;
        }

        return $DB->Query($sql_2);
    }
}

?>