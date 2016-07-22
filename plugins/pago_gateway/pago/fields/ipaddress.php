
<?php

/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JFormFieldIpaddress extends JFormField
{
    /**
     * Element name
     *
     * @access       public
     * @var          string
     */ 

    protected $type = 'Ipaddress';

    public function getInput()
    {
        $ip = getenv('HTTP_CLIENT_IP')?:
            getenv('HTTP_X_FORWARDED_FOR')?:
            getenv('HTTP_X_FORWARDED')?:
            getenv('HTTP_FORWARDED_FOR')?:
            getenv('HTTP_FORWARDED')?:
            getenv('REMOTE_ADDR');
        
        $html = '<div id="ipaddress" ipaddress="'.$ip.'"></div>';
        return $html;
    }
}