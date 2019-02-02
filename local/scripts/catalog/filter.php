<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if ( !$_SESSION['filter']['section'] || $_SESSION['filter']['section'] != $arSection['ID'] ) {
  unset($_SESSION['filter']);
  $_SESSION['filter']['section'] = $arSection['ID'];
}

if ( isset($_GET['show']) && $_GET['show'] ) {
  $_SESSION['filter']['show'] = htmlspecialchars($_GET['show']);
}
if ( isset($_GET['order']) && $_GET['order'] ) {
  $_SESSION['filter']['order'] = htmlspecialchars($_GET['order']);
}
if ( isset($_GET['sort']) && $_GET['sort'] ) {
  $_SESSION['filter']['sort'] = htmlspecialchars($_GET['sort']);
}
if ( isset($_GET['count_page']) && $_GET['count_page'] ) {
  $_SESSION['filter']['count_page'] = (int)$_GET['count_page'];
}
?>
<div class="sorts obyavlenieTable catalog">
  <div class="rTypeWrapper">
    <a class="rType blocks <?if ($_SESSION['filter']['show']!='list'):?>active<?endif;?>" href="?show=blocks"></a>
    <a class="rType list <?if ($_SESSION['filter']['show']=='list'):?>active<?endif;?>" href="?show=list"></a>
  </div>
  Сортировать: &nbsp;&nbsp;
  <?
  $order = ($_SESSION['filter']['order']=='desc' && $_SESSION['filter']['sort']=='PRICES') ? 'desc' : 'asc';
  ?>
  <a href="?sort=PRICES&order=<?if ($order=='asc'&&$_SESSION['filter']['sort']=='PRICES'):?>desc<?else:?>asc<?endif;?>" class="sort sales <?=$order?> <?if ($_SESSION['filter']['sort']=='PRICES'):?>active<?endif;?>">Цена</a>
  <?
  $order = ($_SESSION['filter']['order']=='desc' && $_SESSION['filter']['sort']=='RAITING') ? 'desc' : 'asc';
  ?>
  <a href="?sort=RAITING&order=<?if ($order=='asc'&&$_SESSION['filter']['sort']=='RAITING'):?>desc<?else:?>asc<?endif;?>" class="sort raiting <?=$order?> <?if ($_SESSION['filter']['sort']=='RAITING'):?>active<?endif;?>">Рейтинг</a>
  <?
  $order = ($_SESSION['filter']['order']=='desc' && $_SESSION['filter']['sort']=='TOP') ? 'desc' : 'asc';
  ?>
  <a href="?sort=TOP&order=<?if ($order=='asc'&&$_SESSION['filter']['sort']=='TOP'):?>desc<?else:?>asc<?endif;?>" class="sort sales <?=$order?> <?if ($_SESSION['filter']['sort']=='TOP'):?>active<?endif;?>">Популярные</a>
  <?
  $order = ($_SESSION['filter']['order']=='desc' && $_SESSION['filter']['sort']=='NOVELTY') ? 'desc' : 'asc';
  ?>
  <a href="?sort=NOVELTY&order=<?if ($order=='asc'&&$_SESSION['filter']['sort']=='NOVELTY'):?>desc<?else:?>asc<?endif;?>" class="sort thanks <?=$order?> <?if ($_SESSION['filter']['sort']=='NOVELTY'):?>active<?endif;?>">Новинки</a>
  &nbsp; &nbsp;
  На странице: &nbsp;&nbsp;
  <?
  $options = array(12, 24, 36, 48);
  if ( !$_SESSION['filter']['count_page'] ) {
    $_SESSION['filter']['count_page'] = $options[0];
  }

  ?>
  <script>
    var filterCountItems = function(){
      var options = document.getElementById('filterSelectCountItems').children,
          count = options.length;
      for ( var i=0; i<count; i++ ) {
        if ( options[i].selected ) {
          location.href = '?count_page=' + options[i].value;
        }
      }
    }
  </script>
  <span class="select-wraper orange">
    <span class="select-text"><?=(int)$_SESSION['filter']['count_page']?></span>
    <select id="filterSelectCountItems" class="dropdown-filter" onchange="filterCountItems()">
      <?
      foreach ($options as $item) {
        echo'<option value="'.$item.'" ';
        if ( (int)$_SESSION['filter']['count_page'] == $item ) { echo'selected'; }
        echo'>'.$item.'</option>';
      }
      ?>
    </select>
  </span>
  &nbsp;&nbsp;&nbsp;
</div>