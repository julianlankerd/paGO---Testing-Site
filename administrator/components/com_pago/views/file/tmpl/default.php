<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->row->title; ?></h2>
<form action="index.php" method="post">

	<label for="fulltext">Description</label>
	<?php
	// parameters : areaname, content, width, height, cols, rows
	echo $this->editor->display( 'filetext',  $this->row->fulltext , '100%', '400', '45', '20' ) ;
	?>
	<br />
	<br />

	<input type="hidden" name="option" value="com_pago" />
	<input type="hidden" name="view" value="file" />
	<input type="hidden" name="controller" value="file" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="submit" value="Save" />
</form>
