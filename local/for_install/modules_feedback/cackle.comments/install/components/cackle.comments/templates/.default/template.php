<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<div id="mc-container">
    <div id="mc-content">
        <?php if ((strtolower($DB->type)=="mysql")): ?>
        <ul id="cackle-comments">
            <?php

            $obj = $arResult['CACKLE_OBJ'];

            foreach ($obj as $comment): ?>
                <li  id="cackle-comment-<?php echo $comment['id']; ?>">
                    <div id="cackle-comment-header-<?php echo $comment['comment_id']; ?>" class="cackle-comment-header">
                        <cite id="cackle-cite-<?php echo $comment['id']; ?>">
                            <?php if($comment['autor']) : ?>
                            <a id="cackle-author-user-<?php echo $comment['id']; ?>" href="#" target="_blank" rel="nofollow"><?php echo $comment['autor']; ?></a>
                            <?php else : ?>
                            <span id="cackle-author-user-<?php echo $comment['id']; ?>"><?php echo $comment['name']; ?></span>
                            <?php endif; ?>
                        </cite>
                    </div>
                    <div id="cackle-comment-body-<?php echo $comment['id']; ?>" class="cackle-comment-body">
                        <div id="cackle-comment-message-<?php echo $comment['id']; ?>" class="cackle-comment-message">
                            <?php echo $comment['text']; ?>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>

        </ul>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    cackle_widget = window.cackle_widget || [];
    cackle_widget.push({widget: 'Comment', id: '<? echo $arResult['SITE_ID']; ?>', channel: '<? echo $arResult['MC_CHANNEL']; ?>',
    <?php if ($arResult['SSO_PARAM'] == 1) : ?> ssoAuth: '<?php echo $arResult['SSO']; ?>' <?php endif;?>   });
    (function() {
        var mc = document.createElement('script');
        mc.type = 'text/javascript';
        mc.async = true;
        mc.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cackle.me/widget.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(mc, s.nextSibling);
    })();
</script>









