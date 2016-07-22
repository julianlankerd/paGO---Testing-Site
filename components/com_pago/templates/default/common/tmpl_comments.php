<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
$config = Pago::get_instance( 'config' )->get('global');
$show_comments = $config->get('comments.show_comments');
$guest_comment = $config->get('comments.comment_guest_submition');
$replay_comment = $config->get('comments.comment_replay');

if($comments){
        foreach ($comments as $comment) {
            ?>
                <div class="pg-show-comment" id="comment_<?php echo $comment->id;?>">
                    <?php $avatar = PagoHelper::getAvatar($comment->author_id); ?>
                    <?php if(strlen($comment->author_web_site) > 5){ ?> 
                        <a target="_blank" href = "<?php echo $comment->author_web_site;?>" class="pg_user_image pg-comment-author-image">
                            <img src='<?php echo $avatar['avatarPath']; ?>'>
                        </a>
                    <?php }else{ ?>
                        <span class="pg_user_image pg-comment-author-image">
                            <img src='<?php echo $avatar['avatarPath']; ?>'>
                        </span>
                    <?php } ?>
                    <div class = "pg-comment-info">
                        <?php if(strlen($comment->author_web_site) > 5){ ?> 
                            <a target="_blank" href = "<?php echo $comment->author_web_site;?>" class="pg-comment-author-name"><?php echo $comment->author_name;?></a>
                        <?php }else{ ?>
                            <span class="pg-comment-author-name"><?php echo $comment->author_name;?></span>
                        <?php } ?>
                        <div class="pg-comment-message">
                            <?php echo $comment->text;?>
                            <?php if($replay_comment == 1){ ?>
                                <?php if($guest_comment == 1){ ?>
                                    <?php 
                                        $commentName = $user->guest ? 'guest' : 'member';
                                    ?>
                                    <a href = "javascript:void(0)" class = "reply_comment fa fa-reply <?php echo $commentName;?>"></a>
                                    <div class="pg-reply-comment-container">
                                        <form name="addComment" method="post" action="<?php echo JRoute::_( 'index.php' ) ?>">
                                            <?php $replayAvatar = PagoHelper::getAvatar(); ?>
                                            <span class="pg_user_image pg-comment-author-image reply" >
                                                <img src="<?php echo $replayAvatar['avatarPath']; ?>">
                                            </span>
                                            <textarea rows="1" name="comment_message" placeholder = "<?php echo JTEXT::_('PAGO_COMMENT_WRITE_MESSAGE')?>"></textarea><br/>
                                            <input type="hidden"  name="comment_parentId" value="0" >

                                            <input type="hidden" name="comment_name" value='<?php echo $user->guest ? '':$user->name?>'>    
                                            <input type="hidden" name="comment_email" value='<?php echo $user->guest ? '':$user->email?>'>  
                                
                                            <input type="hidden" name="comment_web_site">

                                            <input type="button" class="addCommentBtn reply pg-green-text-btn" name="addCommentBtn" value="<?php echo JTEXT::_('PAGO_COMMENT_REPLY');?>" >
                                            <div></div>                                            
                                        </form>
                                    </div>
                                <?php } ?>
                            </div>
                        
                            <div class="pg-show-comments-replies">
                                <?php 
                                    if(isset($comment->replays)){
                                        ?>
                                            <?php
                                            foreach ($comment->replays as $reply) {
                                                ?>
                                                    <div class="pg-show-comments-reply clearfix">  
                                                        <?php $repAvatar = PagoHelper::getAvatar($reply->author_id); ?> 
                                                        <?php if(strlen($reply->author_web_site) > 5){ ?>   
                                                            <a target="_blank" href = "<?php echo $reply->author_web_site;?>" class="pg-comment-author-image reply" style="background: url('<?php echo $repAvatar['avatarPath']; ?>')"></a>
                                                        <?php }else{ ?>
                                                            <span class="pg_user_image pg-comment-author-image reply" >
                                                                 <img src='<?php echo $repAvatar['avatarPath']; ?>'>
                                                            </span>
                                                        <?php } ?>                                  
                                                        <div class = "pg-comment-info">
                                                            <?php if(strlen($reply->author_web_site) > 5){ ?>   
                                                                <a target="_blank" href = "<?php echo $reply->author_web_site;?>" class="pg-comment-author-name"><?php echo $reply->author_name;?></a>
                                                            <?php }else{ ?>
                                                                <span class="pg-comment-author-name"><?php echo  $reply->author_name;?></span>
                                                            <?php } ?>
                                                            <div class="pg-comment-re-message"><?php echo $reply->text;?></div>
                                                        </div>
                                                    </div>
                                                <?php
                                            }
                                            ?>
                                        <?php
                                    }
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class = "clearfix"></div>
                </div>  
            <?php
        }   
    }
?>