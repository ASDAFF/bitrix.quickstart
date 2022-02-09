<? 
require($_SERVER["DOCUMENT_ROOT"]."/yandexparser/libs/phpQuery/phpQuery.php");
  
class pageHelper{

    private $use_cache = false;
    private $use_proxy = false;
    private $cookiefile = 'cookies.txt';
 
    public function enableProxy() {
        $this->use_proxy = true; 
    }

    public function disableProxy() {
        $this->use_proxy = false;
    }

    public function enableCache() {
        $this->use_cache = true;
    }

    public function disableCache() {
        $this->use_cache = false;
    }
 
    private function getProxy(){
        return '125.216.144.199:8080';
    }
     
    private function cache_get_html($url){
        
        $dir = $_SERVER["DOCUMENT_ROOT"] . '/yandexparser/parser_tmp/';
        $filename = $dir . md5($url) . '.html';
        
        if($this->use_cache)
            if(file_exists($filename))
                return file_get_html($filename);
 
        if($this->use_proxy){ 
            $context = array('http' => array ( 
                             'proxy' => $this->getProxy(),
                             'request_fulluri' => true ));  
            $context = stream_context_create($context); 
            @$html = file_get_html($url, false, $context); 
        } else {
            @$html = file_get_html($url);
        }
        
        if(is_object($html))  
            $html->save($filename);  
       
        return $html;
    }
  
    function curl_request($url){  
        $ch = curl_init();         
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_USERAGENT, 
                "Opera/9.10 (Windows NT 5.1; U; ru)");   
        curl_setopt ($ch, CURLOPT_HEADER, false);  
        curl_setopt ($ch, CURLOPT_COOKIEJAR, $this->cookiefile);  
        curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->cookiefile);  
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
 
        if($this->use_proxy)
            curl_setopt ($ch, CURLOPT_PROXY, $this->getProxy()); 
  
        $result = curl_exec($ch); 
        
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
 
        echo $result; echo ' -----  '; 
        
        die($httpcode);
        
        $document = phpQuery::newDocument($result);
        
        return $document;    
    }
    
    function get($url){
        return $this->curl_request($url); 
    }

} 
 


class yandexParser{
    
    private $offersCount; // сколько позиций тянем 
    private $minRating;   // минимальный рейтинг товара чтобы потянуть
    private $selectors = array('offer'       => '.b-offers__offers',              // предложение в списке
                               'pager_page'  => '.b-pager__pages .b-pager__page'  // активная страница в пагинации 
                               );
 
    function __construct($arParams) {
        $this->offersCount = $arParams['offersCount'] ? $arParams['offersCount'] : 20;
        $this->minRating = $arParams['minRating'] ? $arParams['minRating'] : 4;
         
    }
    
    private function getModelUrl($modelID, $arParams){ // по ид выплюнет урл страницы
        $page = $arParams['page'] ? $arParams['page'] : 1; 
        return "http://market.yandex.ru/offers.xml?&modelid={$modelID}&grhow=shop&how=aprice&np=1&page={$page}";
 
    }
  
    private function getOffers($page){
        return $page->find($this->selectors['offer']);
    }
    
    private function getOfferInfo($offer){
        $price = pq($offer)->find('.b-prices__num')->html();
        $price = str_replace(' ', '', $price);
        
        $shop = pq($offer)->find('.b-offers__price .shop-link')->html();
 
        $delivery = pq($offer)->find('.b-offers__desc .b-offers__delivery b')->html();
        
        $rating = 0;
        
        foreach(pq($offer)->find('.b-rating .b-rating__star-other') as $star)
            $rating++;


        $text = pq($offer)->find('.b-offers__price')->html();
        if(strpos($text, "наличии")!==false){  
            $v_nalichii = 'Y'; 
        } else {   
            $v_nalichii = 'N';   
        } 
 
        return array('PRICE'   => $price,
                     'SHOP'    => $shop,
                     'DELIVERY'=> $delivery, 
                     'RATING'  => $rating, 
                     'V_NALICHII'=> $v_nalichii);
    }
    
    private function getPagesCnt($page){
        
        foreach($page->find($this->selectors['pager_page']) as $p)
            $arr[] = pq($p)->html();
 
        $cnt = max($arr);
        
        if(!$cnt)
            $cnt = 1;
        
        return $cnt;
    } 
 
    function parse($modelID){ 
        $pageHelper = new pageHelper(); 
        $pageHelper->enableProxy();  
        $currentPage = 0; 
        while(true){
            $page = $pageHelper->get($this->getModelUrl($modelID ,
                                                        array('page' => ++$currentPage)));
 
            $pagesCnt = $this->getPagesCnt($page);
 
            $offers = $this->getOffers($page); 
 
            foreach($offers as $offer){
         
                $offerInfo = $this->getOfferInfo($offer); 
            
                if($offerInfo['RATING'] >= $this->minRating 
                   &&
                   $offerInfo['V_NALICHII'] != 'N') 
                      $result[] = $offerInfo; 
    
            }
            
            if(count($result) >= $this->offersCount)
                break;
            if($pagesCnt == $currentPage)
                break;
        }
        $result = array_slice($result, 0, $this->offersCount); 
        return $result;
    }
    
 
    
}
 