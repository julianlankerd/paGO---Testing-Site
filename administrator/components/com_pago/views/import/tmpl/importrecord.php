<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

PagoHtml::behaviour_jquery('jqueryui');
PagoHtml::apply_layout_fixes();

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'helpers' . DS . 'menu_config.php';
PagoHtml::pago_top($menu_items, 'tabs');
$option = JFactory::getApplication()->input->get('option', '', 'string');
?>
<script language="javascript" type="text/javascript">
	var xmlhttp;

	function GetXmlHttpObject()
	{
		if (window.XMLHttpRequest)
		{
			// code for IE7+, Firefox, Chrome, Opera, Safari
			return new XMLHttpRequest();
		}

		if (window.ActiveXObject)
		{
			return new ActiveXObject("Microsoft.XMLHTTP");
		}

		return null;
	}

	window.onload = function ()
	{
		importdata(1);
	}

	function importdata(new_line)
	{
		xmlhttp = GetXmlHttpObject();

		if (xmlhttp == null)
		{
			alert("Your browser does not support XMLHTTP!");

			return;
		}

		var url = 'index.php?option=com_pago&view=import&task=importpgRecords&json=1&new_line=' + new_line;
		url = url + "&sid=" + Math.random();

		xmlhttp = GetXmlHttpObject();

		xmlhttp.onreadystatechange = function ()
		{
			if (xmlhttp.readyState == 4)
			{
				var response    = xmlhttp.responseText;
				var arrResponse = response.split("`_`");
				if (arrResponse[0] != "")
				{
					importdata(arrResponse[0]);

					if (document.getElementById('pgImportResponse') && document.getElementById('divDataStatus'))
					{
						/*document.getElementById('divDataStatus').innerHTML = arrResponse[0];*/
						document.getElementById('divDataStatus').style.display = '';
						document.getElementById('pgImportResponse').style.display = '';
					}

				}
				else
				{
					window.location.href = 'index.php?option=com_pago&view=import&records='+arrResponse[1];
					if (document.getElementById('divPgLoading'))
					{
						document.getElementById('divPgLoading').style.display = 'none';
					}
	
					if (document.getElementById('pgImportResponse') && document.getElementById('divDataStatus'))
					{
						document.getElementById('divDataStatus').innerHTML = arrResponse[1];
						document.getElementById('divDataStatus').style.display = '';
						document.getElementById('pgImportResponse').style.display = '';
					}

					if (document.getElementById('importCompleted'))
					{
						document.getElementById('importCompleted').innerHTML = "Complete Process";
						document.getElementById('importCompleted').style.display = '';
					}
				}
			}
			else
			{
				if (document.getElementById('divPgLoading')) {
					document.getElementById('divPgLoading').style.display = '';
				}
			}
			
		};

		xmlhttp.open("GET", url, true);
		xmlhttp.send(null);
	}
</script>
<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">
	<div class="pg-table-wrap">
		
			<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
			<thead>
				<tr class="pg-main-heading">
					<td colspan="8">
						<div class="pg-background-color">
							<?php echo JText::_('COM_PAGO_IMPORT_LOG'); ?>
						</div>
					</td>
				</tr>
			</thead>
			<tbody>
			<tr height="20px">
					<td>
						<div class="import_field" id="pgImportResponse" style="display: none;"><?php echo JText::_('COM_PAGO_IMPORT_OK'); ?> &nbsp;&nbsp;&nbsp;<span
					id="divDataStatus"></span></div>
					</td>
				</tr>
				<tr>
					<td>
						<div id="divpgpreviewlog"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div id="importCompleted" style="display:none;"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div id="divPgLoading"><img
								src="<?php echo JURI::root() ?>/components/com_pago/images/spinner.gif">
						</div>
					</td>
				</tr>
				</tbody>
			</table>
		
	</div>
	<input type="hidden" name="option" value="com_pago"/>
</form>
