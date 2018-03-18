<?if(!check_bitrix_sessid()) return;?>
<?
echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
?>
<p><a href="/"><?=GetMessage('BEONO_MODULE_BASKET_INSTALL_FINISHED');?></a></p>

<?=BeginNote();?><?=GetMessage('BEONO_MODULE_SEND_EVENT')?><?=EndNote();?>
<button id="beono_send_install_event" onclick="javascript: this.innerHTML = '<?=GetMessage('BEONO_MODULE_SEND_EVENT_BUTTON_THANKS')?>'; this.disabled = 'disabled'; var beono_img = document.createElement('img'); beono_img.src = 'http://srv29755.ht-test.ru/bitrix/rk.php?event1=mp_install&event2=<?=htmlspecialchars($_GET['id'])?>&event3=<?=htmlspecialchars($_SERVER['HTTP_HOST'])?>';"><?=GetMessage('BEONO_MODULE_SEND_EVENT_BUTTON')?></button>