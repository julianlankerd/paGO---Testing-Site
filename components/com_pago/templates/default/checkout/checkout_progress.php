<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<div class="pg-checkout-progress clearfix">
    <ol class="pg-checkout-steps clearfix">
        <li class="pg-checkout-step pg-step1<?php echo ($this->step == 1) ? ' current' : ''; ?><?php echo ($this->step > 1) ? ' past' : ''; ?>">Login</li>
        <li class="pg-checkout-step pg-step2<?php echo ($this->step == 2) ? ' current' : ''; ?><?php echo ($this->step > 2) ? ' past' : ''; ?>">Shipping</li>
        <li class="pg-checkout-step pg-step3<?php echo ($this->step == 3) ? ' current' : ''; ?><?php echo ($this->step > 3) ? ' past' : ''; ?>">Payment</li>
        <li class="pg-checkout-step pg-step4<?php echo ($this->step == 4) ? ' current' : ''; ?>">Confirm</li>
    </ol>
</div>
