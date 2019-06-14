<?php
ini_set("display_errors","Off");

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    Bitrix\Iblock,
    Bitrix\Iblock\IblockTable,
    Bitrix\Iblock\IBlockSection,
    Bitrix\Main\Config\Option;


Loc::loadMessages(__FILE__);

GLOBAL $sale_module;
$sale_module=true;

if(!Loader::includeModule('sale')) {
    $sale_module=false;
}

if(!Loader::includeModule("iblock")) {
    return false;
}




class CStepUseIC
{
    const tprefix="webes_ic";


    public static function recalc_all($price_param_id=0)
    {
        //ini_set("display_errors","On");
        COption::SetOptionString("webes.itemconsist", "id_price_input", $price_param_id);
        $rez=self::mysql_query_my("(SELECT DISTINCT(item_id) as id FROM `webes_ic_set` WHERE parent_id=0 ORDER BY last_change_price) UNION (SELECT DISTINCT(parent_id) as id FROM `webes_ic_set` WHERE parent_id!=0 ORDER BY last_change_price)");
        if(self::mysql_num_rows_my($rez) > 0)
        {
            for($i=0;$i<self::mysql_num_rows_my($rez);$i++)
            {
                self::calc_item(self::mysqli_result($rez,$i,'id'),$price_param_id);
                print self::mysqli_result($rez,$i,'id').'......ok<br>';
            }
        }
        print '<p>OK</p>';
    }

    public static function calc_item($element_id,$price_param_id=0)
    {
        GLOBAL $arCosts;
        if(!is_array($arCosts))$arCosts=self::ingridients_simple_costs();
        $elementData=self::get_element_data($element_id);
        $q="select * from webes_ic_ib_params where ib_id=".intval($elementData['iblock_id'])." AND ib_section IN (".intval($elementData['section_id']).")";
        $rez=self::mysql_query_my($q);
        if(self::mysql_num_rows_my($rez)==0)
        {
            $q="select * from webes_ic_ib_params where ib_id=".intval($elementData['iblock_id']);
            $rez=self::mysql_query_my($q);
        }
        if(self::mysql_num_rows_my($rez)==0)return false; // не задан конфиг цены для ИБ

        $P=new IC_IB_PARAMS();
        $P->loadN($rez,0);
        $price_config=unserialize($P->params);
        $arPrices=array();  // ИД товара или торгового предложения => цена
        $arPrices[$element_id]=self::calc_price($elementData['consist'],$price_config);
        if(!$price_param_id && $elementData['offers_exists'])
        {
            foreach($elementData['offers'] as $offer_id => $oar)
                $arPrices[$offer_id]=self::calc_price($oar['consist'],$price_config);
            asort($arPrices);

            // затухание
            if($price_config['zatuhan'] > 0)
            {
                $min=min($arPrices);
                $max=max($arPrices);
                foreach ($arPrices as $offer_id => $price)
                {
                    $sale_prc=(($price-$min)/($max-$min))*$price_config['zatuhan']; // процент скидки на текущую позицию
                    $arPrices[$offer_id]=($price-($price*$sale_prc/100));
                }
            }
        }

        // округление
        foreach ($arPrices as $offer_id => $price)
            $arPrices[$offer_id] = self::okrugl($price,$price_config['okrugl']);

        // запись значений
        foreach ($arPrices as $offer_id => $price)
        {
            self::write_price($offer_id,$price,$price_param_id);
        }
    }


    public static function write_price($offer_id,$price,$price_param_id=0)
    {
        GLOBAL $sale_module;

        if(!$price_param_id && $sale_module)
        {
            $PRICE_TYPE_ID = 1; // Базовая
            $arFields = Array(
                "PRODUCT_ID" => $offer_id,
                "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
                "PRICE" => $price,
            );
            $res = CPrice::GetList(
                array(),
                array(
                    "PRODUCT_ID" => $offer_id,
                    "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
                )
            );
            if ($arr = $res->Fetch())
            {
                $arFields['CURRENCY']=$arr['CURRENCY'];
                Bitrix\Catalog\Model\Price::update($arr["ID"], $arFields);
            }
            else
            {
                Bitrix\Catalog\Model\Price::add($arFields);
            }
        }

        else if($price_param_id) // если установлено в какое свойство писать
        {
            CIBlockElement::SetPropertyValuesEx($offer_id, false, array($price_param_id => $price));
        }

        self::mysql_query_my("UPDATE webes_ic_set SET last_price = $price, last_change_price = NOW() WHERE item_id = $offer_id");

    }


    public static function okrugl($price,$type)
    {
        switch($type)
        {
            case 0: return $price;
            case 1: return ceil($price);    // до целого
            case 2: return ceil($price/10)*10;    // до 10
            case 3: return ceil($price/50)*50;    // до 50
            case 4: return ceil($price/100)*100;    // до 100
        }
    }

    public static function calc_price($consist,$price_config)
    {
        GLOBAL $arCosts;
        $baseprice=0;
        foreach ($consist as $eid => $ecnt)
            $baseprice=$baseprice+$arCosts[$eid]*$ecnt;
        return $baseprice*$price_config['koef'];
    }

    public static function ingridients_simple()
    {
        $ret=array();
        $ai=self::get_all_ingridients();

        foreach($ai['groups'] as $g_id => $arItems)
        {
            foreach ($arItems as $items)
            foreach($items as $iid => $aritem)
            {
                $ret[$iid]=$aritem['name'];
            }
        }
        return $ret;
    }
    public static function ingridients_simple_costs()
    {
        $ret=array();
        $ai=self::get_all_ingridients();

        foreach($ai['groups'] as $g_id => $arItems)
        {
            foreach ($arItems as $items)
            foreach($items as $iid => $aritem)
            {
                $ret[$iid]=$aritem['price'];
            }
        }
        return $ret;
    }

    public static function get_public_consist($consist,$ingridients_simple)
    {
        $a=array();
        foreach($consist as $iid => $cnt)
        {
            $a[]=$ingridients_simple[$iid].' - '.$cnt.Loc::GetMessage("webes_ic_classies_EC");
        }
        return implode(", ",$a);
    }

    public static function save_consist($id,$configcnt,$parent_id=0)
    {
        foreach($configcnt as $k => $v)
            if($v==0)unset($configcnt[$k]);

        $rez=self::mysql_query_my("select * from webes_ic_set where item_id=$id");
        $S=new IC_SET();
        if(self::mysql_num_rows_my($rez)>0)$S->loadN($rez,0);
        else {$S->create();$S->item_id=$id;}
        $S->contents_setted=serialize($configcnt);
        $S->parent_id=$parent_id;
        $S->update();
        return array("result"=>"success");
    }

    public static function delete_ib_config($cid)
    {
        $C=new IC_IB_PARAMS();
        $C->load($cid);
        if(!$C->id)return array("result"=>"error","description"=>"Error id IB_PARAMS");
        $C->del();
        return array("result"=>"success");
    }

    public static function save_group_params($group_id)
    {
        $G=new IC_GROUPS();
        $G->load($group_id);
        if(!$G->id)return array("result"=>"error","description"=>"Error id group");
        if($_POST['new_name']=='')return array("result"=>"error","description"=>"Error name");
        $G->name=$_POST['new_name'];
        $G->update();
        return array("result"=>"success");
    }

    public static function delete_ingridient($i_id)
    {
        $I=new IC_ITEMS();
        $I->load($i_id);
        if(!$I->id)return array("result"=>"error","description"=>"Error id element");
        $I->del();
        return array("result"=>"success");
    }

    public static function save_ingridient($i_id)
    {
        $I=new IC_ITEMS();
        $I->load($i_id);
        if(!$I->id)return array("result"=>"error","description"=>"Error id element");
        $I->name=$_POST['name'];
        $I->price=$_POST['price'];
        $I->update();
        return array("result"=>"success");
    }

    public static function add_ingridient($group_id)
    {
        if(!$group_id)return array("result"=>"error","description"=>"Error id group");
        $I=new IC_ITEMS();
        $I->create();
        $I->group_id=$group_id;
        $I->name=$_POST['name'];
        $I->price=$_POST['price'];
        $I->update();
        return array("result"=>"success");
    }

    public static function add_group($name)
    {
        if($name=='')return array("result"=>"error","description"=>Loc::GetMessage("webes_ic_classies_GROUP_NO_NAME"));
        $rez=self::mysql_query_my("select * from webes_ic_groups where name='$name'");
        if(self::mysql_num_rows_my($rez)>0)return array("result"=>"error","description"=>Loc::GetMessage("webes_ic_classies_GROUP_EXISTS"));
        $G=new IC_GROUPS();
        $G->create();
        $G->name=$name;
        $G->update();
        return array("result"=>"success");
    }

    public static function get_all_ingridients()
    {
        $groups=array();
        $rez=self::mysql_query_my("select * from webes_ic_groups order by name");
        if(self::mysql_num_rows_my($rez)>0)
            for($i=0;$i<self::mysql_num_rows_my($rez);$i++)
            {
                $G=new IC_GROUPS();
                $G->loadN($rez,$i);
                $groups[$G->id]['name']=$G->name;
                $rez2=self::mysql_query_my("select * from webes_ic_items where group_id=$G->id ORDER BY name");
                if(self::mysql_num_rows_my($rez2)>0)
                    for($j=0;$j<self::mysql_num_rows_my($rez2);$j++)
                    {
                        $I=new IC_ITEMS();
                        $I->loadN($rez2,$j);
                        $groups[$G->id]['items'][$I->id]=array('name'=>$I->name,'price'=>$I->price);
                    }
            }
         return array("result"=>"success","groups"=>$groups);
    }

    public static function save_ib_params($cid,$arConfig)
    {
        $C=new IC_IB_PARAMS();
        $C->load($cid);
        if(!$C->id)return array("result"=>"error","description"=>"Error config ID");
        $arStrtr=array(','=>'.',' '=>'');
        $arConfig['koef']=strtr((string)$arConfig['koef'],$arStrtr);
        $C->params=serialize($arConfig);
        $C->update();
        return array("result"=>"success");
    }

    public static function get_all_ib_settings()
    {
        $settings=array();
        $rez=self::mysql_query_my("select * from webes_ic_ib_params order by ib_id");
        $a=self::get_iblocks();
        $ibs=$a['ibs'];
        $secs=self::get_sections_ibock();

        if(self::mysql_num_rows_my($rez)>0)
            for($i=0;$i<self::mysql_num_rows_my($rez);$i++)
            {
                $IP=new IC_IB_PARAMS();
                $IP->loadN($rez,$i);
                $settings[$IP->id]=array(
                    'ib_id'     =>  $IP->ib_id,
                    'ib_name'   =>  $ibs[$IP->ib_id],
                    's_id'      =>  $IP->ib_section,
                    's_name'    =>  (isset($secs[$IP->ib_section])?$secs[$IP->ib_section]:'-'),
                    'params'    =>  unserialize($IP->params)
                );
            }
        return array("result"=>"success","settings"=>$settings);
    }

    public static function get_iblocks()
    {

        $arIblocks = array();
        $rsIblocks = Bitrix\Iblock\IblockTable::getList(array(
            "order"  => array("SORT" => "ASC", "NAME" => "ASC"),
            "filter" => array("ACTIVE" => "Y"),
            "select" => array("ID", "NAME"),
        ));
        while($row = $rsIblocks->fetch())
            $arIblocks[ $row["ID"] ] = $row["NAME"];
        return array("result"=>"success","ibs"=>$arIblocks);
    }


    public static function add_iblock_config($ib_id,$ib_section)
    {
        if($ib_id==0)return array("result"=>"error","description"=>Loc::GetMessage("webes_ic_classies_INCORRECT_IDIB"));
        $rez=self::mysql_query_my("select * from webes_ic_ib_params where ib_id=$ib_id AND ib_section=$ib_section");
        if(self::mysql_num_rows_my($rez)>0)return array("result"=>"error","description"=>Loc::GetMessage("webes_ic_classies_CONFIG_IS_EXISTS"));
        $C=new IC_IB_PARAMS();
        $C->create();
        $C->ib_id=$ib_id;
        $C->ib_section=$ib_section;
        $C->update();
        return array("result"=>"success");

    }

    public function get_sections_ibock($ib_id=0)
    {
            $ret=array();
            if($ib_id)$arFilter = array('IBLOCK_ID' => $ib_id, "ACTIVE" => "Y");
            else $arFilter = array("ACTIVE" => "Y");
            $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter);
            while ($arSect = $rsSect->GetNext())
            {
                $ret[$arSect['ID']]=$arSect['NAME'];
            }
            return $ret;
    }

    public static function get_element_data($element_id)
    {

        GLOBAL $sale_module;
        $arRet=array();

        if(!function_exists("GetIBlockElementListEx"))return array("result"=>"error",'description'=>"Error GetIBlockElementListEx not exist");

        $items= GetIBlockElementListEx("",array(),array(),array(),0,array("ID"=>$element_id));
        $arConsist=self::get_consist($element_id);

        while($arItem =  $items->GetNext())
            $arRet=array(
                "name"  =>  $arItem['NAME'],
                "url"   =>  $arItem['DETAIL_PAGE_URL'],
                "iblock_id" =>  $arItem['IBLOCK_ID'],
                "section_id" => $arItem['IBLOCK_SECTION_ID'],
                "consist"   =>  $arConsist['consist'],
                "last_price"   =>  $arConsist['last_price'],
                );

        if($sale_module)
        {
            $arRet['offers_exists']=CCatalogSKU::getExistOffers(array($element_id))[$element_id];
            if($arRet['offers_exists'])
            {
                $offers=CCatalogSKU::getOffersList($element_id,0,array('=ACTIVE' => 'Y'),array('*'),array("*"));

                foreach($offers as $arOffers)
                {
                    foreach($arOffers as $offer_id => $arOffer)
                    {
                        $arConsist=self::get_consist($offer_id);
                        $arRet['offers'][$offer_id]=array(
                                'id'    =>  $arOffer['ID'],
                                'name'  =>  $arOffer['NAME'],
                                "consist"   =>  $arConsist['consist'],
                                "last_price"   =>  $arConsist['last_price'],
                            );
                    }
                }
            }
        }

        $arRet['ingridients']=self::get_all_ingridients();

        return $arRet;
    }

    public static function get_consist($arOfferID)
    {
        $rez=self::mysql_query_my("select * from webes_ic_set where item_id=$arOfferID");
        if(self::mysql_num_rows_my($rez)==0)return array('consist'=>null,'last_price'=>null);
        return array('consist'=>unserialize(self::mysqli_result($rez,0,'contents_setted')), 'last_price'=>self::mysqli_result($rez,0,'last_price'));
    }

    public static function get_all_elements_statuses($arr)
    {
        $ret=array();
        $ret['statuses']=array();
        //$status=0;
        foreach($arr as $id)
        {
            //$ret['statuses'][]=array($id => $status);
            $el_data=self::get_element_data($id);
            if(!$el_data['offers_exists'] && empty($el_data['consist']))
                $ret['statuses'][$id]=0;
            else {
                $status=1;
                foreach($el_data['offers'] as $oid => $aroffer)
                    if(empty($aroffer['consist']))$status=0;
                $ret['statuses'][$id]=$status;
            }

        }
        return $ret;
    }

    public static function gdtime($dtime)
    {
        $a = explode(' ', $dtime);
        return CStepUseIC::gdate(trim($a[0])) . ' ' . $a[1];
    }

    public static function gdate($odate)
    {
        if (!strpos(' ' . $odate, '-')) return $odate;
        $a = explode('-', $odate);
        return $a[2] . '.' . $a[1] . '.' . $a[0];
    }

    public static function file_get_contents_my($link)
    {$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows; U; Windows NT 5.0; En; rv:1.8.0.2) Gecko/20070306 Firefox/1.0.0.4");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch,CURLOPT_TIMEOUT,10);
        $result=curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return $result;
    }

    public static function getlastid($tabl)
    {
        $rez = CStepUseIC::mysql_query_my("select * from $tabl order by id DESC LIMIT 0,1");
        $id = CStepUseIC::mysqli_result($rez, 0, "id");
        return $id;
    }
    public static function upload($fileformname, $filename, $DIR, $allow_exts = null)
    {
        if (!is_dir('upload')) mkdir('upload', 0777);
        $uploaddir = "$DIR/";
        $ff = basename($_FILES[$fileformname]['name']);
        $a = explode(".", $ff);
        $r = $a[count($a) - 1];
        $uploadfile = $uploaddir . $filename . '.' . $r;
        if (move_uploaded_file($_FILES[$fileformname]['tmp_name'], $uploadfile)) {
            $a = explode(".", $ff);
            $r = $a[count($a) - 1];
            if ($allow_exts !== null && !in_array(mb_strtolower($r), $allow_exts)) {
                unlink($uploadfile);
                return 0;
            } else return $r;
        } else return 0;

    }



    public static function mes($t)
    {   GLOBAL $connection;
        return $connection->escape_string($t);
    }


    public static function mysql_query_my($query)
    {
        GLOBAL $connection;
        return $connection->query($query);
    }


    public static function mysql_num_rows_my($rez)
    {
        return $rez->num_rows;
    }


    public static function mysqli_result($res, $row, $field = 0)
    {
        if (!$res) return null;
        $res->data_seek($row);
        $datarow = $res->fetch_array();
        return $datarow[$field];
    }

}


//////// class IC_IB_PARAMS   ---  AVTOMATICAL ///////////////
class IC_IB_PARAMS
{
    var $id;
    var $ib_id;      // ИД инфоблока
    var $ib_section;      // ИД секции
    var $params;     // serialized параметры: коеф. корректировки цены, округление, уменьшение цены на сколько %


    Function loadN($rez,$N)
    {
        $this->id=CStepUseIC::mysqli_result($rez,$N,"id");
        $this->ib_id=CStepUseIC::mysqli_result($rez,$N,"ib_id");
        $this->ib_section=CStepUseIC::mysqli_result($rez,$N,"ib_section");
        $this->params=CStepUseIC::mysqli_result($rez,$N,"params");
    }


    Function load($idL)
    {
        $idL=intval($idL);
        $rez=CStepUseIC::mysql_query_my("select * from ".CStepUseIC::tprefix."_ib_params where id='$idL'");
        if(!CStepUseIC::mysql_num_rows_my($rez)){return 0;}
        $this->id=$idL;
        $this->loadN($rez,0);
    }

    Function update()
    {
        $query="update ".CStepUseIC::tprefix."_ib_params set ib_id='".CStepUseIC::mes($this->ib_id)."',ib_section='".CStepUseIC::mes($this->ib_section)."', params='".CStepUseIC::mes($this->params)."', id=$this->id where id=$this->id";
        CStepUseIC::mysql_query_my($query);
    }

    Function loadPOST()
    {
        if(isset($_POST["ib_id"]))$this->ib_id=$_POST["ib_id"];
        if(isset($_POST["ib_section"]))$this->ib_section=$_POST["ib_section"];
        if(isset($_POST["params"]))$this->params=$_POST["params"];
    }

    Function create()
    {
        $this->id=CStepUseIC::getlastid(CStepUseIC::tprefix."_ib_params")+1;
        $q="insert into ".CStepUseIC::tprefix."_ib_params (id) values ($this->id)";
        CStepUseIC::mysql_query_my($q);
    }
    Function del()
    {
        CStepUseIC::mysql_query_my("delete from ".CStepUseIC::tprefix."_ib_params where id='$this->id'");
    }
    Function getdir()
    {$ID=$this->id;
        if(!is_dir("ic_ib_paramsfiles"))return "";
        $if=strval(floor($ID/10000));
        $iif=strval(floor($ID/100));
        if(!is_dir("ic_ib_paramsfiles/i$if")){mkdir("ic_ib_paramsfiles/i$if",0777);chmod("ic_ib_paramsfiles/i$if",0777);}
        if(!is_dir("ic_ib_paramsfiles/i$if/i$iif")){mkdir("ic_ib_paramsfiles/i$if/i$iif",0777);chmod("ic_ib_paramsfiles/i$if/i$iif",0777);}
        if(!is_dir("ic_ib_paramsfiles/i$if/i$iif/i$ID")){mkdir("ic_ib_paramsfiles/i$if/i$iif/i$ID",0777);chmod("ic_ib_paramsfiles/i$if/i$iif/i$ID",0777);}
        return "ic_ib_paramsfiles/i$if/i$iif/i$ID/";
    }
    Function gethttpdir()
    {$ID=$this->id;
        $if=strval(floor($ID/10000));
        $iif=strval(floor($ID/100));
        return "http://".$_SERVER['HTTP_HOST']."/ic_ib_paramsfiles/i$if/i$iif/i$ID/";
    }
}
// QUERY
//////// end AVTOMATICAL ///////////////



//////// class IC_GROUPS   ---  AVTOMATICAL ///////////////
class IC_GROUPS
{
    var $id;
    var $name;


    Function loadN($rez,$N)
    {
        $this->id=CStepUseIC::mysqli_result($rez,$N,"id");
        $this->name=CStepUseIC::mysqli_result($rez,$N,"name");
    }


    Function load($idL)
    {
        $idL=intval($idL);
        $rez=CStepUseIC::mysql_query_my("select * from ".CStepUseIC::tprefix."_groups where id='$idL'");
        if(!CStepUseIC::mysql_num_rows_my($rez)){return 0;}
        $this->id=$idL;
        $this->loadN($rez,0);
    }

    Function update()
    {
        $query="update ".CStepUseIC::tprefix."_groups set name='".CStepUseIC::mes($this->name)."', id=$this->id where id=$this->id";
        CStepUseIC::mysql_query_my($query);
    }

    Function loadPOST()
    {
        if(isset($_POST["name"]))$this->name=$_POST["name"];
    }

    Function create()
    {
        $this->id=CStepUseIC::getlastid(CStepUseIC::tprefix."_groups")+1;
        $q="insert into ".CStepUseIC::tprefix."_groups (id) values ($this->id)";
        CStepUseIC::mysql_query_my($q);
        //print $q;
    }
    Function del()
    {
        CStepUseIC::mysql_query_my("delete from ".CStepUseIC::tprefix."_groups where id='$this->id'");
    }
    Function getdir()
    {$ID=$this->id;
        if(!is_dir("ic_groupsfiles"))return "";
        $if=strval(floor($ID/10000));
        $iif=strval(floor($ID/100));
        if(!is_dir("ic_groupsfiles/i$if")){mkdir("ic_groupsfiles/i$if",0777);chmod("ic_groupsfiles/i$if",0777);}
        if(!is_dir("ic_groupsfiles/i$if/i$iif")){mkdir("ic_groupsfiles/i$if/i$iif",0777);chmod("ic_groupsfiles/i$if/i$iif",0777);}
        if(!is_dir("ic_groupsfiles/i$if/i$iif/i$ID")){mkdir("ic_groupsfiles/i$if/i$iif/i$ID",0777);chmod("ic_groupsfiles/i$if/i$iif/i$ID",0777);}
        return "ic_groupsfiles/i$if/i$iif/i$ID/";
    }
    Function gethttpdir()
    {$ID=$this->id;
        $if=strval(floor($ID/10000));
        $iif=strval(floor($ID/100));
        return "http://".$_SERVER['HTTP_HOST']."/ic_groupsfiles/i$if/i$iif/i$ID/";
    }
}
// QUERY
//////// end AVTOMATICAL ///////////////



//////// class IC_ITEMS   ---  AVTOMATICAL ///////////////
class IC_ITEMS
{
    var $id;
    var $name;
    var $price;
    var $group_id;


    Function loadN($rez,$N)
    {
        $this->id=CStepUseIC::mysqli_result($rez,$N,"id");
        $this->name=CStepUseIC::mysqli_result($rez,$N,"name");
        $this->price=CStepUseIC::mysqli_result($rez,$N,"price");
        $this->group_id=CStepUseIC::mysqli_result($rez,$N,"group_id");
    }


    Function load($idL)
    {
        $idL=intval($idL);
        $rez=CStepUseIC::mysql_query_my("select * from ".CStepUseIC::tprefix."_items where id='$idL'");
        if(!CStepUseIC::mysql_num_rows_my($rez)){return 0;}
        $this->id=$idL;
        $this->loadN($rez,0);
    }

    Function update()
    {
        $query="update ".CStepUseIC::tprefix."_items set name='".CStepUseIC::mes($this->name)."', price='".CStepUseIC::mes($this->price)."', group_id='".CStepUseIC::mes($this->group_id)."', id=$this->id where id=$this->id";
        CStepUseIC::mysql_query_my($query);
    }

    Function loadPOST()
    {
        if(isset($_POST["name"]))$this->name=$_POST["name"];
        if(isset($_POST["price"]))$this->price=$_POST["price"];
        if(isset($_POST["group_id"]))$this->group_id=$_POST["group_id"];
    }

    Function create()
    {
        $this->id=CStepUseIC::getlastid(CStepUseIC::tprefix."_items")+1;
        $q="insert into ".CStepUseIC::tprefix."_items (id) values ($this->id)";
        CStepUseIC::mysql_query_my($q);
        //print $q;
    }
    Function del()
    {
        CStepUseIC::mysql_query_my("delete from ".CStepUseIC::tprefix."_items where id='$this->id'");
    }
    Function getdir()
    {$ID=$this->id;
        if(!is_dir("ic_itemsfiles"))return "";
        $if=strval(floor($ID/10000));
        $iif=strval(floor($ID/100));
        if(!is_dir("ic_itemsfiles/i$if")){mkdir("ic_itemsfiles/i$if",0777);chmod("ic_itemsfiles/i$if",0777);}
        if(!is_dir("ic_itemsfiles/i$if/i$iif")){mkdir("ic_itemsfiles/i$if/i$iif",0777);chmod("ic_itemsfiles/i$if/i$iif",0777);}
        if(!is_dir("ic_itemsfiles/i$if/i$iif/i$ID")){mkdir("ic_itemsfiles/i$if/i$iif/i$ID",0777);chmod("ic_itemsfiles/i$if/i$iif/i$ID",0777);}
        return "ic_itemsfiles/i$if/i$iif/i$ID/";
    }
    Function gethttpdir()
    {$ID=$this->id;
        $if=strval(floor($ID/10000));
        $iif=strval(floor($ID/100));
        return "http://".$_SERVER['HTTP_HOST']."/ic_itemsfiles/i$if/i$iif/i$ID/";
    }
}
// QUERY
//////// end AVTOMATICAL ///////////////


/* устанавливаемые составы */
//////// class IC_SET   ---  AVTOMATICAL ///////////////
class IC_SET
{
    var $id;
    var $item_id;
    var $contents_setted;    // serialized
    var $parent_id;    // родительский элемент (если это предложение)
    var $last_price;            // последняя рассчитанная цена
    var $last_change_price;     // дата время последнего пересчета цены



    Function loadN($rez,$N)
    {
        $this->id=CStepUseIC::mysqli_result($rez,$N,"id");
        $this->item_id=CStepUseIC::mysqli_result($rez,$N,"item_id");
        $this->contents_setted=CStepUseIC::mysqli_result($rez,$N,"contents_setted");
        $this->parent_id=CStepUseIC::mysqli_result($rez,$N,"parent_id");
        $this->last_price=CStepUseIC::mysqli_result($rez,$N,"last_price");
        $this->last_change_price=CStepUseIC::mysqli_result($rez,$N,"last_change_price");
    }


    Function load($idL)
    {
        $idL=intval($idL);
        $rez=CStepUseIC::mysql_query_my("select * from ".CStepUseIC::tprefix."_set where id='$idL'");
        if(!CStepUseIC::mysql_num_rows_my($rez)){return 0;}
        $this->id=$idL;
        $this->loadN($rez,0);
    }

    Function update()
    {
        $query="update ".CStepUseIC::tprefix."_set set item_id='".CStepUseIC::mes($this->item_id)."', contents_setted='".CStepUseIC::mes($this->contents_setted)."', parent_id='".CStepUseIC::mes($this->parent_id)."', last_price='".CStepUseIC::mes($this->last_price)."', last_change_price='".CStepUseIC::mes($this->last_change_price)."' where id=$this->id";
        CStepUseIC::mysql_query_my($query);
    }


    Function create()
    {
        $this->id=CStepUseIC::getlastid(CStepUseIC::tprefix."_set")+1;
        $q="insert into ".CStepUseIC::tprefix."_set (id) values ($this->id)";
        CStepUseIC::mysql_query_my($q);
        //print $q;
    }
    Function del()
    {
        CStepUseIC::mysql_query_my("delete from ".CStepUseIC::tprefix."_set where id='$this->id'");
    }
    Function getdir()
    {$ID=$this->id;
        if(!is_dir("ic_setfiles"))return "";
        $if=strval(floor($ID/10000));
        $iif=strval(floor($ID/100));
        if(!is_dir("ic_setfiles/i$if")){mkdir("ic_setfiles/i$if",0777);chmod("ic_setfiles/i$if",0777);}
        if(!is_dir("ic_setfiles/i$if/i$iif")){mkdir("ic_setfiles/i$if/i$iif",0777);chmod("ic_setfiles/i$if/i$iif",0777);}
        if(!is_dir("ic_setfiles/i$if/i$iif/i$ID")){mkdir("ic_setfiles/i$if/i$iif/i$ID",0777);chmod("ic_setfiles/i$if/i$iif/i$ID",0777);}
        return "ic_setfiles/i$if/i$iif/i$ID/";
    }
    Function gethttpdir()
    {$ID=$this->id;
        $if=strval(floor($ID/10000));
        $iif=strval(floor($ID/100));
        return "http://".$_SERVER['HTTP_HOST']."/ic_setfiles/i$if/i$iif/i$ID/";
    }
}
// QUERY
//////// end AVTOMATICAL ///////////////











?>