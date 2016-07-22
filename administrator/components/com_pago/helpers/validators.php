<?php defined( '_JEXEC' ) or die();
/**
* @brief Form generator
* @package Another Form generator
* @author Gergely "Garpeer" Aradszki | garpeer [ $ ] gmail [ , ] com
* @license GPLv3
*
*
* Copyright (C) 2010  Gergely Aradszki
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*
*
* Just some examples showing text/checkbox/regexp validation.
* You should extend and improve this class to make it fit for your own needs.
*/
class Validator {
    /**
     * @brief sample textValidator
     * @param <type> $value
     * @param <type> $args
     *
     * @bug getting the length of a utf-8 strings can be problematic, since not all hosting providers support multibyte functions
     */
    public function textValidator($value, $args) {
        $min=$args[0];
        $max=$args[1];
        if ($value=="") {
            return("You must fill in this field");
        }else {            
            $length=strlen(html_entity_decode($value,ENT_QUOTES, "utf-8"));
            if (($min)&&($length<$min)) {
                return("too short (min. $min chars)");
            }
            if (($max)&&($length>$max)) {
                return("too long (max. $max chars)");
            }
        }
		
		return false;
    }
    /**
     * @brief sample termValidator
     * @param <type> $value     
     */
    public function termValidator($value) {
        if ($value!="on") {
           return(" You must accept the terms.");
        }
		
		return false;
    }
    /**
     * @brief sample termValidator
     * @param <type> $id
     * @param <type> $args
     */
    public function regExpValidator($value, $args) {
        if ($value=="") {
            return("You must fill in this field");
        }
        if (preg_match($args[0], $value)) {
           return false;
        }else{
            return ("Not a valid e-mail address");
        }
		
		return false;
    }
    /**
     * @brief check protection code
     * @param $code fix code
     * @param $args arguments
     */
    public function jsProtector($code, $args){
        if ($code!=$args[0]) {
            return ("Wrong protection code (JS may be turned off, or you are not human)");
        }
        return false;
    }
}
?>