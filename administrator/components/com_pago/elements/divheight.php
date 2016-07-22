
<?php

/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JFormFieldDivheight extends JFormField
{
    /**
     * Element name
     *
     * @access       public
     * @var          string
     */ 

    protected $type = 'Divheight';

    public function getInput()
    {
        $node = $this->element;
        $height = $node["height"];
    
        $html = "<div style='min-height:".$height."px'></div>";
        return $html;
    }
}