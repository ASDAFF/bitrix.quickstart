<?
if (is_array($arResult["CURRENT_ELEMENT"]["STD_SIZE"])) {

    ?>
    <div class="bs-docs-tooltip-examples ">

        <ul id="myTab" class="nav nav-tabs size-tab">
            <?php
            $i = 0;
            foreach ($arResult["CURRENT_ELEMENT"]["STD_SIZE"] as $key => $size) {
                /* data-toggle="tab" */
                ?>
                <li id="li_<?= $arResult['mixData'][$size["SIZE"]]['ID'] ?>" <?= ($i == 0 ? 'class="active"' : '') ?>>			
				<a
					data-size="<?= $arResult['mixData'][$size["SIZE"]]['ID'] ?>"
					href="#tab<?= $arResult['mixData'][$size["SIZE"]]['ID'] ?>"><?= $arResult['mixData'][$size["SIZE"]]['NAME'] ?></a>
                </li><?php
                $i++;
            }
            ?>
        </ul>

    </div>

	<div class="tab-content size-d " id="myTabContent">
<?
	$measure_block = COption::GetOptionString("main", "measure_block");
	if( (int)$measure_block == 1 )
	{
        $i = 0;
        foreach ($arResult["CURRENT_ELEMENT"]["STD_SIZE"] as $key => $size) {
            // the size of the currently selected
            if ($i == 0) {
                $currentSize = $arResult['mixData'][$size["SIZE"]]['ID'];
            }
            ?>
            <div id="tab<?= $arResult['mixData'][$size["SIZE"]]['ID'] ?>"
                 class="tab-pane fade<?= ($i == 0 ? ' active in' : '') ?>">
                <?php
                $realSizesHtml = '<div class="active-p">
    					<div class="post-p">';
                $j = 0;
                if (is_array($size["REAL_SIZES"]))
                    foreach ($size["REAL_SIZES"] as $k => $v) {
                        $realSizesHtml .= '<span>' . $Params["SIZES_CODES"][$k] . '</span>';
                        ++$j;
                    }
                $realSizesHtml .= '</div><div class="post">';
                if (is_array($size["REAL_SIZES"]))
                    foreach ($size["REAL_SIZES"] as $k => $v) {
                        $realSizesHtml .= '<span class="size-ar">' . $v . ' '.GetMessage("SM").'</span> ';
                    }
                $realSizesHtml .= '</div></div>';

                if ($j > 0) echo $realSizesHtml;
                ?>

            </div>
            <?php
            $i++;
        }
	}
?>
    </div>
<?php
}
?>
<script type="text/javascript">
	function checkNotifyFormState()
	{
		$("#box").hide();
		
		$.ajax({
			type		: "POST",
			url			: "/local/components/novagr.shop/catalog.list/templates/.default/ajax.php?<?=bitrix_sessid_get();?>",
			data		: {
				'action'	:	"productSubsribe",
				'CHECK'		: "Y",
				'ajax'		: "Y",
				'elemId'	: $("#notify_elem_id").attr('value'),
				'user_mail'	: "<?=CUser::GetEmail();?>"
			},
			dataType	: "JSON",
			success: function(data){
				if(data.CHECK == "Y")
				{
					$('#signed').show();
					$('#notifyme-form').hide();
					$('#unsubsc').show();
				}else{
					$('#signed').hide();
					$('#notifyme-form').show();
					$('#unsubsc').hide();
				}
				if( $('#notifyme-form').is(":visible") ) $("#box").show();
			}
		});
	}
	
	$(document).ready(function(e) {
		
		$('.authNotifySize2').live('click', function(){
			$("#notify_elem_id").attr('value', $(this).attr('data-elem-id') );
			checkNotifyFormState();
		});
		
		$("button.close,.not-extremum").on("click", function(){
			$("div.extremum-slide").hide();
		});
		
		
		setTimeout(checkNotifyFormState, 1000);
		
		$('#unsubsc').on('click', function(){
			$('#box').hide();
			$.ajax({
				type		: "POST",
				url			: "/local/components/novagr.shop/catalog.list/templates/.default/ajax.php?<?=bitrix_sessid_get();?>",
				data		: {
					'action'		: "productSubsribe",
					'UNSUBSCRIBE'	: "Y",
					'ajax'			: "Y",
					'elemId'		: $("#notify_elem_id").attr('value'),
					'user_mail'		: "<?=CUser::GetEmail();?>"
				},
				dataType	: "JSON",
				success: function(data){
					$('#signed').hide();
					$('#notifyme-form').show();
				}
			});
		});
		
	});
</script>