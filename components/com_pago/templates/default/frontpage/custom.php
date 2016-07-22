<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/ 
defined('_JEXEC') or die('Restricted access'); ?>

<div style="border:1px solid #999;padding:10px;margin-bottom:20px">
    <div style="width:37%;float:left;border-right:1px solid #999;padding:10px 0 0 20px">    	
		<?php echo $this->modules->get_position( 'pago_frontpage_topleft' ) ?>
    </div>
    <div style="width:49%;float:right;padding:10px 20px 0 0">
		<?php echo $this->modules->get_position( 'pago_frontpage_topright' ) ?>
    </div>
    <div style="clear:both;"><!-- --></div>
</div>

<div style="border:1px solid #999;padding:10px;margin-bottom:20px">
    <?php echo $this->modules->get_position( 'pago_frontpage_body' ) ?>
</div>