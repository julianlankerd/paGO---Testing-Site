<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php $uri =& JFactory::getURI(); ?>
<div class="pg-share clearfix">
    <h2 class="pg-title">Share:</h2>
    <ul class="pg-options">
        <?php if( $this->tmpl_params->get( 'show_share_email', 1 ) ) { ?>
            <li class="email-icon icon">
                <a href="#" title="Email to a Friend" rel="nofollow">Email</a>
            </li>
        <?php } ?>
        <?php if( $this->tmpl_params->get( 'show_share_print', 1 ) ) { ?>
            <li class="print-icon icon">
                <a target="_blank" rel="nofollow" href="#" title="Print this Page">Print</a>
            </li>
        <?php } ?>
        <?php if( $this->tmpl_params->get( 'show_share_facebook', 1 ) ) { ?>
            <?php
            $this->document->addScriptDeclaration( '
                function fbs_click() {
                u=location.href;
                t=document.title;
                window.open( \'http://www.facebook.com/sharer.php?u=\' +encodeURIComponent(u)+\'&t=\'+encodeURIComponent(t), \'sharer\', \'toolbar=0,status=0,width=626,height=436\' );
                return false;
                }
            ' );
            ?>
            <li class="facebook-icon icon">
                <a rel="nofollow" href="http://www.facebook.com/share.php?u=<?php echo $uri->toString(); ?>" onclick="return fbs_click()" target="_blank">Share</a>
            </li>
        <?php } ?>
        <?php if( $this->tmpl_params->get( 'show_share_twitter', 1 ) ) { ?>
            <li class="twitter-icon icon">
                <a href="http://twitter.com/home?status=Currently reading <?php echo $uri->toString(); ?>" title="Click to send this page to Twitter!" target="_blank" rel="nofollow">Twitter</a>
            </li>
        <?php } ?>
        <?php if( $this->tmpl_params->get( 'show_share_facebook_like', 1 ) ) { ?>
        <li class="facebook-like">
            <iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo $uri->toString(); ?>&amp;layout=button_count&amp;show_faces=false&amp;width=50&amp;action=like&amp;colorscheme=light&amp;height=22" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:50px; height:22px;" allowTransparency="true">
                Your browser does not support inline frames or is currently configured not to display inline frames. Content can be viewed at actual source page: <a href="http://www.facebook.com/plugins/like.php?href=<?php echo $uri->toString(); ?>" rel="nofollow" target="_blank">http://www.facebook.com/plugins/like.php?href=<?php echo $uri->toString(); ?></a>
            </iframe>
        </li>
        <?php } ?>
    </ul>
</div>