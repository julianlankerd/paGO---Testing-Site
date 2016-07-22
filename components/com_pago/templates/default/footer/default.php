<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/ 
defined('_JEXEC') or die('Restricted access');?>
	</div><!-- end pg-content -->
    <div id="pg-footer" class="clearfix">
        <?php if( isset( $this->modules->modules[ 'pago_nav' ] ) ) : ?>
			<?php echo $this->modules->get_position( 'pago_footer' ) ?>
        <?php endif; ?>
    </div><!-- end pg-footer -->
</div>
