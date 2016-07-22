<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldCurrencylist extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Currencylist';

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		// Base name of the HTML control.
		$ctrl  = $name;

		// Construct the various argument calls that are supported.
		$attribs       = ' ';

		if ($v = $this->size)
		{
				$attribs       .= 'size="' . $v . '"';
		}

		if ($v = $this->style)
		{
				$attribs       .= 'style="' . $v . '"';
		}

		if ($v = $this->element['class'])
		{
				$attribs       .= 'class = "' . $v . '"';
		}
		else
		{
				$attribs       .= 'class = "inputbox"';
		}

		if ($m = $this->multiple)
		{
				$attribs       .= ' multiple = "true"';
		}

		if ($m = $this->disabled)
		{
				$attribs       .= ' disabled = "disabled"';
		}

		$key = 'id';
		$val = 'name';

		$countries = [
	        "usd",
	        "aed",
	        "afn",
	        "all",
	        "amd",
	        "ang",
	        "aoa",
	        "ars",
	        "aud",
	        "awg",
	        "azn",
	        "bam",
	        "bbd",
	        "bdt",
	        "bgn",
	        "bif",
	        "bmd",
	        "bnd",
	        "bob",
	        "brl",
	        "bsd",
	        "bwp",
	        "bzd",
	        "cad",
	        "cdf",
	        "chf",
	        "clp",
	        "cny",
	        "cop",
	        "crc",
	        "cve",
	        "czk",
	        "djf",
	        "dkk",
	        "dop",
	        "dzd",
	        "egp",
	        "etb",
	        "eur",
	        "fjd",
	        "fkp",
	        "gbp",
	        "gel",
	        "gip",
	        "gmd",
	        "gnf",
	        "gtq",
	        "gyd",
	        "hkd",
	        "hnl",
	        "hrk",
	        "htg",
	        "huf",
	        "idr",
	        "ils",
	        "inr",
	        "isk",
	        "jmd",
	        "jpy",
	        "kes",
	        "kgs",
	        "khr",
	        "kmf",
	        "krw",
	        "kyd",
	        "kzt",
	        "lak",
	        "lbp",
	        "lkr",
	        "lrd",
	        "lsl",
	        "ltl",
	        "mad",
	        "mdl",
	        "mga",
	        "mkd",
	        "mnt",
	        "mop",
	        "mro",
	        "mur",
	        "mvr",
	        "mwk",
	        "mxn",
	        "myr",
	        "mzn",
	        "nad",
	        "ngn",
	        "nio",
	        "nok",
	        "npr",
	        "nzd",
	        "pab",
	        "pen",
	        "pgk",
	        "php",
	        "pkr",
	        "pln",
	        "pyg",
	        "qar",
	        "ron",
	        "rsd",
	        "rub",
	        "rwf",
	        "sar",
	        "sbd",
	        "scr",
	        "sek",
	        "sgd",
	        "shp",
	        "sll",
	        "sos",
	        "srd",
	        "std",
	        "svc",
	        "szl",
	        "thb",
	        "tjs",
	        "top",
	        "try",
	        "ttd",
	        "twd",
	        "tzs",
	        "uah",
	        "ugx",
	        "uyu",
	        "uzs",
	        "vnd",
	        "vuv",
	        "wst",
	        "xaf",
	        "xcd",
	        "xof",
	        "xpf",
	        "yer",
	        "zar",
	        "zmw"
	    ];
		
		$options = array();

		foreach ($countries as $k)
		{
			$options[] = array(
				'id' => $k,
				'name' => $k
			);
		}

		if(!is_array($value))
		{
			$value = explode(",", $value);
		}

		$html = JHTML::_('select.genericlist', $options, $ctrl, $attribs, $key, $val, $value, $this->id);

		return $html;
	}
}
