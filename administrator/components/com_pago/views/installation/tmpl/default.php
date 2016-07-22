<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
$tmpl_path = JURI::base() . 'components/com_pago/views/installation/tmpl/';

//$doc->addStyleSheet($tmpl_path . 'css/styles.css');

?>

<div id="container">

    <!--<div id="stepbar">
        <div class="t">
            <div class="t">
                <div class="t"></div>
            </div>
        </div>
        <div class="m">
            <h1 class="steps">Steps</h1>
            <div id="stepFirst" class="steps<?php //if($activeSteps == 1) echo " on"; ?>">1 : <?php echo JText::_( 'PAGO_INST_WLCME' ); ?></div>
            <div class="steps<?php //if($activeSteps == 2) echo " on"; ?>">2 : <?php echo JText::_( 'PAGO_INST_CHKREQS' ); ?></div>
            <div class="steps<?php //if($activeSteps == 3) echo " on"; ?>">3 : <?php echo JText::_( 'PAGO_INST_EXTS' ); ?></div>
            <div id="stepLast" class="steps<?php //if($activeSteps == 0) echo " on"; ?>">4 : <?php echo JText::_( 'PAGO_INST_DONE' ); ?></div>
            <div class="box"></div>
        </div>
        <div class="b">
            <div class="b">
                <div class="b"></div>
            </div>
        </div>
    </div>-->

    <div id="main">

       <!-- <div class="t">
            <div class="t">
                <div class="t"></div>
            </div>
        </div>
        <div class="m">
            <h1 class="steps"><?php echo JText::_( 'PAGO_INSTALLER_TITLE' ); ?></h1>
        </div>
        <div class="b">
            <div class="b">
                <div class="b"></div>
            </div>
        </div>
        <br />-->


            <?php if( !empty( $this->exceptions ) ): $this->disable_continue = 'disabled="disabled"' ?>
            <?php endif ?>

            <div style="background: none repeat scroll 0% 0% rgb(251, 251, 251); border: 1px solid rgb(204, 204, 204); -moz-border-radius: 5px 5px 5px 5px; padding: 15px; " class="install-body">
                <h2><?php echo JText::_( 'PAGO_BASE_EXTNS_TITLE' ); ?></h2>
                <p><?php echo JText::_( 'PAGO_BASE_EXTNS_DESC' ); ?></p>

                    <table class="adminform">
                        <tbody>
                            <tr>
                                <td width="100%"><?php echo JText::_( 'PAGO_BASE_EXTNS_UPGRADE_NOTICE' ); ?></td>
                                <td align="right"></td>
                            </tr>
                        </tbody>
                    </table>
                    <table cellspacing="1" class="adminlist">
                        <thead>
                            <tr>
                                <th class="title"><?php echo JText::_( 'PAGO_BASE_EXTNS_NAME' ); ?></th>
                                <th width="10%" class="title"><?php echo JText::_( 'PAGO_BASE_EXTNS_TYPE' ); ?></th>
                                <th width="10" align="center" class="title"><?php echo JText::_( 'PAGO_BASE_EXTNS_VERSION' ); ?></th>
                                <th width="10" class="title"><?php echo JText::_( 'PAGO_BASE_EXTNS_STATUS' ); ?></th>
                                <th width="10" class="title"><?php echo JText::_( 'PAGO_BASE_EXTNS_ACTION' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($this->base_extensions as $k=>$extn): ?>
                            <tr class="row0">
                                <td><span class="bold"><?php echo $extn['name'] ?></span></td>
                                <td><?php echo $extn['type'] ?></td>
                                <td align="center"><?php echo $extn['installed_version'] ?>-><?php echo $extn['upgrade_version'] ?></td>
                                <td align="center"><span class="editlinktip hasTip"> <img src="images/tick.png"> </span></td>
                                <td align="center">
									<?php if( $extn['install_type'] != 'disabled' ): ?>

                                    <form id="installform<?php echo $k ?>" name="installform<?php echo $k ?>" method="post" action="?option=com_pago">
                                    	<input type="hidden" value="<?php echo $k ?>" name="extension">
                                        <input type="hidden" value="install_extension" name="task">
                                        <div class="button1-left">
                                            <div onclick="document.installform<?php echo $k ?>.submit();" class="next">

                                                <input type="submit" value="<?php echo JText::_( $extn['install_type'] ); ?>" onclick="" class="button-next">
                                                <span style="margin-left: 20px;"></span>
                                            </div>
                                        </div>
                                    </form>
                                    <?php else: ?>
                                    <?php echo JText::_( 'PAGO_NONE' ); ?>
                                    <?php endif ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

            </div>
            <!--<br />
            <div style="float:right">
				<?php if( empty( $this->exceptions ) ): ?>
                <form id="installform" name="installform" method="post" action="?option=com_pago">
                    <div class="button1-left">
                        <div onclick="document.installform.submit();" class="next">
                            <input type="submit" value="<?php echo JText::_( 'CONTINUE' ); ?>" onclick="" class="button-next">
                            <span style="margin-right: 30px;"></span> </div>
                    </div>
                </form>
                <?php else: ?>
                <form id="installform" name="installform" method="post" action="?option=com_pago&view=installation">
                    <input type="hidden" value="check_requirements" name="task">
                    <div class="button1-left">
                        <div onclick="document.installform.submit();" class="next">
                            <input type="submit" value="<?php echo JText::_( 'RETRY' ); ?>" onclick="" class="button-next">
                            <span style="margin-right: 30px;"></span> </div>
                    </div>
                </form>
                <?php endif ?>
            </div>-->
            <div style="clear:both"></div>

    </div>
</div>

<!-- make sure user is not logged out due to inactivity -->
<?php echo JHTML::_('behavior.keepalive');