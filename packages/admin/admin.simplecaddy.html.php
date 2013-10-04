<?php
/**
* @package SimpleCaddy 2.0 for Joomla 2.5
* @copyright Copyright (C) 2006-2012 Henk von Pickartz. All rights reserved.
* Main display admin file
*/
// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class display {
	static function showExport() {
		display::header();
		?>
		<form method="post" name="adminForm" action="index.php">
			<input type="hidden" name="option" value="com_simplecaddy" />
			<input type="hidden" name="action" value="orders" />
			<input type="hidden" name="task" value="show" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
		echo "Your export can be downloaded : <a href='components/com_simplecaddy/exports/export.txt'>here</a>";
	}

	static function MainMenu($message="") {
		display::header();
		JToolBarHelper::title( JText::_( 'SIMPLECADDY_CONTROL_CENTER' ));
		$cfg=new sc_configuration();
	?>
		<table style="border: none;">
		<tr>
		<td style="width: 55%; vertical-align: top;">

		<div id="cpanel">

			<div style="float:left;">
				<div class="icon">
				<a title="<?php echo JText::_('SC_PRODUCTS'); ?>" href="index.php?option=com_simplecaddy&amp;action=products&amp;task=show" >
				<img src="components/com_simplecaddy/images/products.png" alt="<?php echo JText::_('SC_MANAGE_PRODUCTS');?>" align="middle" name="image" border="0" /><br />
				<?php echo JText::_('SC_PRODUCTS'); ?></a>
				</div>
			</div>

			<div style="float:left;">
				<div class="icon">
				<a title="<?php echo JText::_('SC_ORDERS'); ?>" href="index.php?option=com_simplecaddy&amp;action=orders&amp;task=show">
				<img src="components/com_simplecaddy/images/orders.png" alt="<?php echo JText::_('SC_MANAGE_ORDERS');?>" align="middle" name="image" border="0" /><br />
				<?php echo JText::_('SC_ORDERS'); ?></a>
				</div>
			</div>

			<div style="float:left;">
				<div class="icon">
				<a title="<?php echo JText::_('SC_CONTENT'); ?>" href="index.php?option=com_simplecaddy&amp;action=sccontents&amp;task=show">
				<img src="components/com_simplecaddy/images/sccontentmngr.png" alt="<?php echo JText::_('SC_MANAGE_CONTENT');?>" align="middle" name="image" border="0" /><br />
				<?php echo JText::_('SC_CONTENT'); ?></a>
				</div>
			</div>

			<div style="float:left;">
				<div class="icon">
				<a title="<?php echo JText::_('SC_OPTION_FIELDS'); ?>" href="index.php?option=com_simplecaddy&amp;action=fields&amp;task=show">
				<img src="components/com_simplecaddy/images/fields.png" alt="<?php echo JText::_('SC_MANAGE_FIELDS');?>" align="middle" name="image" border="0" /><br />
				<?php echo JText::_('SC_OPTION_FIELDS'); ?></a>
				</div>
			</div>

			<div style="float:left;">
				<div class="icon">
				<a title="<?php echo JText::_('SC_CONFIGURATION'); ?>" href="index.php?option=com_simplecaddy&amp;action=configuration&amp;task=show">
				<img src="components/com_simplecaddy/images/config.png" alt="<?php echo JText::_('SC_CONFIGURATION');?>" align="middle" name="image" border="0" /><br />
				<?php echo JText::_('SC_CONFIGURATION'); ?></a>
				</div>
			</div>

		<?php if ($cfg->get("use_phocagallery")==1) { ?>
			<div style="float:left;">
				<div class="icon">
				<a title="<?php echo JText::_('SC_PG'); ?>" href="index.php?option=com_simplecaddy&amp;action=scphocag&amp;task=show">
				<img src="components/com_simplecaddy/images/p-pg.png" alt="<?php echo JText::_('SC_PG');?>" align="middle" name="image" border="0" /><br />
				<?php echo JText::_('SC_PG'); ?></a>
				</div>
			</div>
		<?php } ?>

			<div style="float:left;">
				<div class="icon">
				<a title="Plugins" href="index.php?option=com_simplecaddy&amp;action=plugins&amp;task=show">
				<img src="components/com_simplecaddy/images/plugins.png" alt="<?php echo JText::_('SC_PLUGINS'); ?>" align="middle" name="image" border="0" /><br />
				<?php echo JText::_('SC_PLUGINS'); ?></a>
				</div>
			</div>

			<div style="float:left;">
				<div class="icon">
				<a title="About" href="index.php?option=com_simplecaddy&amp;action=about&amp;task=show">
				<img src="components/com_simplecaddy/images/about.png" alt="<?php echo JText::_('SC_ABOUT'); ?>" align="middle" name="image" border="0" /><br />
				<?php echo JText::_('SC_ABOUT'); ?></a>
				</div>
			</div>

		</div>

		</td>
		<td style="vertical-align: top;">
		<?php
			echo "$message";
		?>
		</td>
		</tr>
		</table>
	<?php
	}

	static function AFFooter() {
		?>
		<div style="margin-top: 10px;"><div style="text-align: center">
		<?php echo JText::_('SC_FOR_MORE_INFORMATION_CLICK_HERE');?>
		<a href="http://atlanticintelligence.net" target="_blank"><?php echo JText::_('SC_INFORMATION');?></a>
		<br /><a href="http://demo25.atlanticintelligence.net" target="_blank"><?php echo JText::_('SC_CLICK_HERE_FOR_DEMO');?></a>
		</div></div>
		<?php
	}

	static function showAbout() {
		global $mainframe;
		jimport('joomla.filesystem.path');
		display::header();
		JToolBarHelper::title( JText::_( 'SimpleCaddy' ));
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'Main', false,  false );
	?>
		<form name="adminForm">
		<input type="hidden" name="task" />
		<input type="hidden" name="option" value="com_simplecaddy" />
		</form>
		<div style="text-align: left">
		<h2><img src="components/com_simplecaddy/images/sc_logo15.png"  />SimpleCaddy 2.0 for Joomla 2.5.x</h2>
		Adds basic shopping cart functionality to any page of Joomla content.
		<br/>Featuring
		  <ul>
			  <li>Add to cart</li>
			  <li>Simple shop mechanism</li>
			  <li>Simple order management</li>
			  <li>Simple store management</li>
			  <li>Individual item options</li>
			  <li>Formulas to define prices of individual options</li>
			  <li>Plugins for added functionality</li>
		  </ul>
		  </div>
		  <p>SimpleCaddy (c)Henk von Pickartz, 2006-2013</p>
	<?php
	}

	static function showSortArrows($fieldname) {
		echo "<a href=\"javascript:submitme('$fieldname,ASC')\"><img src=\"components/com_simplecaddy/images/uparrow.png\" border=\"0\" /></a>";
		echo "<a href=\"javascript:submitme('$fieldname,DESC')\"><img src=\"components/com_simplecaddy/images/downarrow.png\" border=\"0\" /></a>";
	}

	static function showProducts(&$arows, $field=null, $order=null) {
	global $mainframe, $mosConfig_list_limit, $mosConfig_absolute_path;
		display::header();
		JToolBarHelper::title( JText::_( 'SIMPLECADDY_PRODUCTS' ));
		JToolBarHelper::custom( 'duplicate', 'copy.png', 'copy_f2.png', 'Duplicate', true,  false );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'Main', false,  false );
		$cfg=new sc_configuration();
		$currency=$cfg->get("currency");
		$tsep=$cfg->get("thousand_sep");
		$decsep=$cfg->get("decimal_sep");
		$decs=$cfg->get("decimals");
		$pageNav=$arows['nav'];
		$rows=$arows['lst'];
		$lists=$arows['lists'];
		$search=JRequest::getVar("search");
		?>
		<script language="javascript">
			function submitme(option) {
			a=option.split(",");
			document.adminForm.field.value=a[0];
			document.adminForm.order.value=a[1];
			document.adminForm.submit();
			}
		</script>
		<form method="post" name="adminForm" action="index.php">

			<table>
				<tr>
					<td width="100%">
						<?php echo JText::_( 'Filter' ); ?>:
						<?php echo $lists['category'];?>
					</td>
				</tr>
			</table>


				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
					<tr>
						<th width="20">
							<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
						</th>

						<th class="title" nowrap="nowrap"><?php echo JText::_('SC_CODE') ?>&nbsp;<?php echo display::showSortArrows("prodcode");?></th>
						<th class="title" nowrap="nowrap"><?php echo JText::_('SC_DESCRIPTION') ?>&nbsp;<?php echo display::showSortArrows("shorttext");?></th>
						<th class="title" nowrap="nowrap"><?php echo JText::_('SC_PRODUCT_CATEGORY') ?>&nbsp;<?php echo display::showSortArrows("category");?></th>
						<th class="title tdright" nowrap="nowrap"><?php echo JText::_('SC_PRICE_PER_UNIT') ?>&nbsp;<?php echo display::showSortArrows("unitprice");?></th>
						<th class="title tdright" nowrap="nowrap"><?php echo JText::_('SC_NUM_IN_STORE') ?>&nbsp;<?php echo display::showSortArrows("av_qty");?></th>
						<th class="title tdcenter" nowrap="nowrap"><?php echo JText::_('SC_PUBLISHED') ?>&nbsp;<?php echo display::showSortArrows("published");?></th>
						<th class="title" nowrap="nowrap">&nbsp;</th>

					</tr>
				<?php
				$k = 0;
				for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row = &$rows[$i];
				?>
					<tr class="<?php echo "row$k"; ?>">
						<td width="20">
							<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td width="10%">
							<a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','edit')">
							<?php
							echo $row->prodcode; ?>
							</a>
						</td>
						<td width="40%">
							<?php echo $row->shorttext; ?>
						</td>
						<td width="40%">
							<?php echo $row->category; ?>
						</td>
						<td width="10%" class="tdright">
							<?php
							echo number_format($row->unitprice, $decs, $decsep, $tsep);
							?>
						</td>
						<td width="10%" class="tdright">
							<?php echo $row->av_qty; ?>
						</td>
						<td align="left" width="10%" class="tdcenter">
							<?php
								$published 	= JHTML::_('grid.published', $row, $i );
								echo $published;
							?>
						</td>
						<td>
							&nbsp;
						</td>
			<?php
				$k = 1 - $k; }

			?>
				</tr>
				<tr><td colspan="7">
					<?php
						echo $pageNav->getListFooter();
					?>
				</td></tr>
			</table>
			<input type="hidden" name="option" value="com_simplecaddy" />
			<input type="hidden" name="action" value="products" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="field" value="<?php echo $field;?>" />
			<input type="hidden" name="order" value="<?php echo $order;?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}

	static function showFields(& $arows ) {
	global $mainframe, $mosConfig_list_limit, $mosConfig_absolute_path;
		display::header();
		JToolBarHelper::title( JText::_( 'SIMPLECADDY_CHECKOUT_FIELDS' ));
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'Main', false,  false );
	if (!$arows) {
		echo "Custom fields have not been installed in your SimpleCaddy, <a href='index.php?option=com_simplecaddy&action=update'>click here to Update</a>";
		return;
	}
	$pageNav=$arows['nav'];
	$rows=$arows['lst'];
	?>
		<form method="post" name="adminForm" action="index.php">
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
					<tr>
						<th width="20">
							<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
						</th>
						<th class="title" nowrap="nowrap"><?php echo JText::_('SC_FIELDNAME') ?></th>
						<th class="title" nowrap="nowrap"><?php echo JText::_('SC_FIELDCAPTION') ?></th>
						<th class="title tdright" nowrap="nowrap"><?php echo JText::_('SC_FIELDTYPE') ?></th>
						<th class="title tdright" nowrap="nowrap"><?php echo JText::_('SC_FIELDLENGTH') ?></th>
						<th class="title tdcenter" nowrap="nowrap"><?php echo JText::_('SC_FIELDCLASS') ?></th>
						<th class="title tdcenter" nowrap="nowrap"><?php echo JText::_('SC_FIELDORDERING') ?></th>
						<th class="title tdcenter" nowrap="nowrap"><?php echo JText::_('SC_FIELDREQUIRED') ?></th>
						<th class="title tdcenter" nowrap="nowrap"><?php echo JText::_('SC_FIELDPUBLISHED') ?></th>
						<th class="title" nowrap="nowrap">&nbsp;</th>

					</tr>
				<?php
				$k = 0;
				for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row = &$rows[$i];
				?>
					<tr class="<?php echo "row$k"; ?>">
						<td width="20">
							<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td width="40%">
							<a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','edit')">
							<?php
							echo $row->name; ?>
							</a>
						</td>
						<td width="40%">
							<?php echo $row->caption; ?>
						</td>
						<td width="10%" class="tdright">
							<?php
							echo  JText::_($row->type);
							?>
						</td>
						<td width="10%" class="tdright">
							<?php echo $row->length; ?>
						</td>
						<td width="40%">
							<?php echo $row->classname; ?>
						</td>
						<td width="10%" class="tdright">
							<?php
							echo $row->ordering;
							?>
						</td>
						<td width="10%" class="tdright">
							<?php
							echo ($row->required?JText::_('SC_YES'):JText::_('SC_NO'));
							?>
						</td>
						<td align="left" width="10%" class="tdcenter">
							<?php
								$published 	= JHTML::_('grid.published', $row, $i );
								echo $published;
							?>
						</td>
						<td>
							&nbsp;
						</td>
			<?php
				$k = 1 - $k; }

			?>
				</tr>
				<tr><td colspan="9">
					<?php
						echo $pageNav->getListFooter();
					?>
				</td></tr>
			</table>
			<input type="hidden" name="option" value="com_simplecaddy" />
			<input type="hidden" name="action" value="fields" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}

	static function editProduct(&$a) {
	global $mainframe;
		$document	= JFactory::getDocument();

		$document->addScript( JURI::root(true).'/administrator/components/com_simplecaddy/js/caddy.js');
		display::header();

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
        $edit = ($cid!=array(0));
		$text = ( $edit ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );
		JToolBarHelper::title( JText::_( "SC_SIMPLECADDY_$text" ), 'generic.png');

		JToolBarHelper::save( 'save', 'Save & Close' );
		JToolBarHelper::apply();
		if ( $edit ) {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		} else {
			JToolBarHelper::cancel();
		}

		$cfg=new sc_configuration();
		$currency=$cfg->get("currency");
		$tsep=$cfg->get("thousand_sep");
		$decsep=$cfg->get("decimal_sep");
		$decs=$cfg->get("decimals");
		$scats=$cfg->get("prodcats");
		$curalign=$cfg->get("curralign");
		$cats=explode("\r\n", $scats);
        $optiongroups=new optiongroups();
        $lstoptgroups=$optiongroups->getgroups($a->prodcode);

	?>

		<form method="post" name="adminForm" action="index.php">
		<table class="adminform" width="100%"><tr><th><?php echo ($a->id ? JText::_('SC_EDIT') : JText::_('SC_NEW'))."&nbsp;".JText::_('SC_PRODUCT');?></th><th>&nbsp;</th></tr>
		<tr>
			<td width="185"><?php echo JText::_('SC_PRODUCT_CODE');?></td>
			<td><input type="text" name="prodcode" value="<?php echo $a->prodcode; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_PRODUCT_NAME');?></td>
			<td><input type="text" name="shorttext" value="<?php echo $a->shorttext; ?>" size="60"/></td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_PRODUCT_CATEGORY');?></td>
			<td>
			<select name="category">
			<?php
				foreach ($cats as $cat) {
					echo "<option value='$cat' ".($cat==$a->category ? ' selected' : '').">$cat</option>";
				}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_AVAILABLE_QTY');?></td>
			<td>
			<input type="text" name="av_qty" value="<?php echo $a->av_qty; ?>" size="6"/>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_PRICE_PER_UNIT');?></td>
			<td>
			<?php
			if ($curalign==1) echo $currency;
			?>
			<input size="10" type="text" name="unitprice" value="<?php echo $a->unitprice; ?>"/>
			<?php
				if ($curalign==0) echo $currency;
				echo JText::_('SC_DO_NOT_FORMAT_YOUR_PRICE');
			?>
			</td>
		</tr>
		<?php if ($cfg->get("show_shipping_fields")==1) { ?>
		<tr>
			<td><?php echo JText::_('SC_SHIPPING_POINTS');?></td>
			<td>
			<input type="text" name="shippoints" value="<?php echo $a->shippoints; ?>" size="5"/>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_SHIPPING_LENGTH');?></td>
			<td>
			<input type="text" name="shiplength" value="<?php echo $a->shiplength; ?>" size="5"/>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_SHIPPING_WIDTH');?></td>
			<td>
			<input type="text" name="shipwidth" value="<?php echo $a->shipwidth; ?>" size="5"/>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_SHIPPING_HEIGHT');?></td>
			<td>
			<input type="text" name="shipheight" value="<?php echo $a->shipheight; ?>" size="5"/>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_SHIPPING_WEIGHT');?></td>
			<td>
			<input type="text" name="shipweight" value="<?php echo $a->shipweight; ?>" size="5"/>
			</td>
		</tr>
		<?php } ?>
		<?php if ($cfg->get("use_downloadables")==1) { ?>
		<tr>
		<td><?php echo JText::_('SC_DOWNLOADABLE');?></td>
		<td>
			<?php
				$show_hide = array (JHTML::_('select.option', 0, JText::_('No')), JHTML::_('select.option', 1, JText::_('Yes')),);
				foreach ($show_hide as $value) {
					echo "<input type='radio' value='$value->value' name='downloadable' ".($a->downloadable==$value->value?' checked':'').">$value->text";
				}
			?>
		</td>
		</tr>
		<tr>
		<td><?php echo JText::_('SC_FILENAME');?></td>
		<td><input size="20" type="text" name="filename" value="<?php echo $a->filename; ?>"/></td>
		</tr>
		<?php } ?>
		<tr><td><?php echo JText::_('SC_PUBLISHED');?></td>
		<td>
			<?php
				$show_hide = array (JHTML::_('select.option', 0, JText::_('No')), JHTML::_('select.option', 1, JText::_('Yes')),);
				foreach ($show_hide as $value) {
					echo "<input type='radio' value='$value->value' name='published' ".($a->published==$value->value?' checked':'').">$value->text";
				}
			?>
		</td>
		</tr>
		</table>
        <?php // check if the product exists before adding options
            if ($a->id) { ?>
		<table class="adminform" border="1"><tr><th><?php echo JText::_('SC_OPTIONS');?>&nbsp;<input type="button" name="addbtn" onclick="submitbutton('addoptgroup')" value="<?php echo JText::_('Add Option');?>" /></th><th width="270"><?php echo JText::_('SC_SHOW_AS');?></th><th width="60"><?php echo JText::_('SC_ORDER');?></th><th width="120"><?php echo stripslashes( JText::_('SC_IND_OPTIONS'));?></th><th>&nbsp;</th></tr>
        <?php
        $showas=new optionsshowas();

            foreach ($lstoptgroups as $optgroup) {
                echo "\n<tr>";
                echo "<td><a rel=\"{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}\" class='modal' href='index.php?option=com_simplecaddy&action=optiongroups&task=show&optgrid=$optgroup->id&productid=$a->id&tmpl=component'>$optgroup->title</a></td>";
                echo "<td>";
                echo $showas->type[$optgroup->showas];
                echo "</td>";
                echo "<td>$optgroup->disporder</td>";
                if (($optgroup->showas !=5) and ($optgroup->showas !=6)) { // exclude the ones without options
                    echo "<td><a rel=\"{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}\" class='modal' href='index.php?option=com_simplecaddy&action=options&task=showindoptions&optgrid=$optgroup->id&tmpl=component&productid=$a->id'>".JText::_('SC_IND_OPTIONS')."</a></td>";
                }
                else
                { // no options? just display an empty cell for table alignment
                    echo "<td>&nbsp;</td>";
                }
                echo "<td><a class='button' href='index.php?option=com_simplecaddy&action=optiongroups&task=remove&optgrid=$optgroup->id&productid=$a->id'>".JText::_('Remove option')."</td>";
                echo "</tr>";
            }
        ?>
        </table>
        <?php
        }
        else
        {
            echo JText::_("SC_SAVE_FIRST");
        }
        ?>

		<input type="hidden" name="id" value="<?php echo $a->id; ?>" />
		<input type="hidden" name="option" value="com_simplecaddy" />
		<input type="hidden" name="action" value="products" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
	<?php
	}

	static function editField(&$a) {
		display::header();
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
        $edit = ($cid!=array(0));
		$text = ( $edit ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );
		JToolBarHelper::title( JText::_( "SIMPLECADDY_$text" ), 'generic.png');

		JToolBarHelper::save( 'save', 'Save' );
		JToolBarHelper::apply();
		if ( $edit ) {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		} else {
			JToolBarHelper::cancel();
		}
	?>
		<form method="post" name="adminForm" action="index.php">
		<table><tr><td style="width:40%; vertical-align: top;">
		<table class="adminform"><tr><th><?php echo ($a->id ? JText::_('SC_EDIT') : JText::_('SC_NEW'))."&nbsp;".JText::_('SC_OPTION_FIELDS');?></th><th>&nbsp;</th></tr>
		<tr>
			<td width="185"><?php echo JText::_('SC_FIELDNAME');?></td>
			<td><input type="text" name="name" value="<?php echo $a->name; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_FIELDCAPTION');?></td>
			<td><input type="text" name="caption" value="<?php echo $a->caption; ?>"/></td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_FIELDTYPE');?></td>
			<td>
			<select name="type">
				<option value="text" <?php echo $a->type == "text" ?"selected" : ""; ?>><?php echo JText::_('Text');?></option>
				<option value="textarea" <?php echo $a->type == "textarea" ? "selected" : ""; ?>><?php echo JText::_('Multiline Text');?></option>
				<option value="radio" <?php echo $a->type == "radio" ? "selected" : ""; ?>><?php echo JText::_('Yes/No');?></option>
				<option value="checkbox" <?php echo $a->type == "checkbox" ? "selected" : ""; ?>><?php echo JText::_('Checkbox');?></option>
				<option value="date" <?php echo $a->type == "date" ? "selected" : ""; ?>><?php echo JText::_('Date');?></option>
				<option value="dropdown" <?php echo $a->type == "dropdown" ? "selected" : ""; ?>><?php echo JText::_('Dropdown');?></option>
				<option value="divider" <?php echo $a->type == "divider" ? "selected" : ""; ?>><?php echo JText::_('Divider');?></option>
			</select>
			</td>
		</tr>
		<tr>
		<td><?php echo JText::_('SC_FIELDCONTENTS');?></td>
		<td><input type="text" name="fieldcontents" value="<?php echo $a->fieldcontents; ?>"/></td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_FIELDLENGTH');?></td>
			<td>
			<input type="text" name="length" value="<?php echo $a->length; ?>"/>
			</td>
		</tr>
		<tr><td><?php echo JText::_('SC_PUBLISHED');?></td>
		<td>
			<?php
				$show_hide = array (JHTML::_('select.option', 0, JText::_('No')), JHTML::_('select.option', 1, JText::_('Yes')),);
				foreach ($show_hide as $value) {
					echo "<input type='radio' value='$value->value' name='published' ".($a->published==$value->value?' checked':'').">$value->text";
				}
			?>
		</td>
		</tr>
		<tr>
		<td width="185"><?php echo stripslashes( JText::_('SC_FIELDCLASS'));?></td>
		<td>
		<input type="text" name="classname" value="<?php echo $a->classname; ?>" />
		</td>
		</tr>
		<tr>
		<td width="185"><?php echo stripslashes( JText::_('SC_FIELDORDERING'));?></td>
		<td>
		<input type="text" name="ordering" value="<?php echo $a->ordering; ?>" />
		</td>
		</tr>
		<tr><td><?php echo JText::_('SC_FIELDREQUIRED');?></td>
		<td>
			<?php
				$show_hide = array (JHTML::_('select.option', 0, JText::_('No')), JHTML::_('select.option', 1, JText::_('Yes')),);
				foreach ($show_hide as $value) {
					echo "<input type='radio' value='$value->value' name='required' ".($a->required==$value->value?' checked':'').">$value->text";
				}
			?>
		</td>
		</tr>
		</table>
		</td>
		<td width="60%" valign="top">
		<table class="adminform" width="100%">
		<tr><th colspan="2"><?php echo JText::_('SC_HELP');?></th></tr>
		<tr>
		<td valign="top" width="150"><?php echo JText::_("SC_FIELDNAME");?></td><td valign="top"><?php echo JText::_("SC_HELP_FIELDNAME");?></td>
		</tr>
		<tr>
		<td valign="top"><?php echo JText::_('SC_FIELDCAPTION');?></td><td valign="top"><?php echo JText::_("SC_HELP_CAPTION");?></td>
		</tr>
		<tr>
		<td valign="top"><?php echo JText::_('SC_FIELDTYPE');?></td><td valign="top"><?php echo JText::_("SC_HELP_TYPE");?></td>
		</tr>
		<tr>
		<td valign="top"><?php echo JText::_('SC_FIELDCONTENTS');?></td><td valign="top"><?php echo JText::_("SC_HELP_FIELDCONTENTS");?></td>
		</tr>
		<tr>
		<td valign="top"><?php echo JText::_('SC_FIELDLENGTH');?></td><td valign="top"><?php echo JText::_("SC_HELP_LENGTH");?></td>
		</tr>
		<tr>
		<td valign="top"><?php echo JText::_('SC_PUBLISHED');?></td><td valign="top"><?php echo JText::_("SC_HELP_PUBLISHED");?></td>
		</tr>
		<tr>
		<td valign="top"><?php echo JText::_('SC_FIELDCLASS');?></td><td valign="top"><?php echo JText::_("SC_HELP_CLASSNAME");?></td>
		</tr>
		<tr>
		<td valign="top"><?php echo JText::_('SC_FIELDORDERING');?></td><td valign="top"><?php echo JText::_("SC_HELP_ORDER");?></td>
		</tr>
		<tr>
		<td valign="top"><?php echo JText::_('SC_FIELDREQUIRED');?></td><td valign="top"><?php echo JText::_("SC_HELP_REQUIRED");?></td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $a->id; ?>" />
		<input type="hidden" name="option" value="com_simplecaddy" />
		<input type="hidden" name="action" value="fields" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
	<?php
	}

	static function showOrders( & $lists, $field=null, $order=null) {
	global $mainframe, $mosConfig_list_limit, $mosConfig_absolute_path;
		JToolBarHelper::title( JText::_( 'SIMPLECADDY_ORDERS' ));
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::custom( 'export', 'archive', 'archive' , 'Export', true,  false );
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'Main', false,  false );

		$cfg=new sc_configuration();
		$currency=$cfg->get("currency");
		$tsep=$cfg->get("thousand_sep");
		$decsep=$cfg->get("decimal_sep");
		$decs=$cfg->get("decimals");
		$pageNav=$lists['nav'];
		$rows=$lists['lst'];

	?>
		<script language="javascript">
			function submitme(option) {
			a=option.split(",");
			document.adminForm.field.value=a[0];
			document.adminForm.order.value=a[1];
			document.adminForm.submit();
			}
		</script>
		<form method="post" name="adminForm" action="index.php">
			<table>
				<tr>
					<td width="100%">
						<?php echo JText::_( 'Filter' ); ?>:
						<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
						<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
					</td>
				</tr>
			</table>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
					<tr>
						<th width="20">
							<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
						</th>
						<th class="title" nowrap="nowrap">ID</th>
						<th class="title" nowrap="nowrap"><?php echo JText::_('SC_NAME') ?>&nbsp;<?php echo display::showSortArrows("name");?></th>
						<th class="title" nowrap="nowrap"><?php echo JText::_('SC_EMAIL') ?>&nbsp;<?php echo display::showSortArrows("email");?></th>
						<th class="title tdright" nowrap="nowrap"><?php echo JText::_('SC_DATE') ?>&nbsp;<?php echo display::showSortArrows("orderdt");?></th>
						<th class="title tdright" nowrap="nowrap"><?php echo JText::_('SC_TOTAL') ?>&nbsp;<?php echo display::showSortArrows("total");?></th>
						<th class="title" nowrap="nowrap"><?php echo JText::_('SC_ORDER_STATUS') ?>&nbsp;<?php echo display::showSortArrows("status");?></th>
						<th class="title" nowrap="nowrap">&nbsp;</th>
					</tr>
				<?php
				$k = 0;
				for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row = &$rows[$i];
				?>
					<tr class="<?php echo "row$k"; ?>">
						<td width="20">
							<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td width="20">
							<a href="#view" onclick="return listItemTask('cb<?php echo $i;?>','view')">
							<?php echo $row->id; ?>
							</a>
						</td>
						<td width="10%">
							<a href="#view" onclick="return listItemTask('cb<?php echo $i;?>','view')">
							<?php echo $row->name; ?>
							</a>
						</td>
						<td width="10%">
							<?php echo "<a href='mailto:$row->email'>$row->email</a>"; ?>
						</td>
						<td width="10%" class="tdright">
							<?php
							echo date("d-m-Y", $row->orderdt); ?>
						</td>
						<td width="10%" class="tdright">
						<?php
							echo number_format($row->total + $row->tax, $decs, $decsep, $tsep);
						?>
						</td>
						<td>
							<?php echo "<span class='".strtolower($row->status)."'>$row->status</span>"; ?>
						</td>
						<td>
							&nbsp;
						</td>
			<?php
				$k = 1 - $k; }

			?>
				</tr>
			</table>
			<?php
				echo $pageNav->getListFooter();
				$field=JRequest::getVar( 'field', '');
				$order=JRequest::getVar( 'order', '');
			?>
			<input type="hidden" name="option" value="com_simplecaddy" />
			<input type="hidden" name="action" value="orders" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="field" value="<?php echo $field;?>" />
			<input type="hidden" name="order" value="<?php echo $order;?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}

	static function editOrder($a, $items, $pageNav) {
	global $mainframe, $mosConfig_list_limit, $mosConfig_absolute_path;
		display::header();
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
        $edit = ($cid!=array(0));
		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title( JText::_( "SimpleCaddy $text" ), 'generic.png');

		JToolBarHelper::save( 'save', 'Save' );
		JToolBarHelper::apply();
		if ( $edit ) {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		} else {
			JToolBarHelper::cancel();
		}
    	$cfg=new sc_configuration();
    	$currency=$cfg->get("currency");
    	$tsep=$cfg->get("thousand_sep");
    	$decsep=$cfg->get("decimal_sep");
    	$decs=$cfg->get("decimals");
    	$align=$cfg->get("curralign"); // before amount==1
    	// hardcoded fields from old simplecaddy <1.7
//    	$standardfields=array(); //array("name", "username", "address1", "postal_code", "city", "telephone", "ipaddress", "region", "country", "email" );
    	$statuses=explode("\n", $cfg->get("ostatus"));
    	$useshipping=$cfg->get("shippingenabled");
	?>
		<form method="post" name="adminForm" action="index.php">
		<div style="vertical-align: top;">
		<div class="orderblock">
		<table class="adminform">
		<tr><th class="title" style="width: 250px;"><?php echo JText::_('SC_ORDER');?></th><th><?php echo $a->id;?></th></tr>
		<tr>
			<td><?php echo JText::_('SC_DATE');?></td>
			<td>
			<?php echo date("d-m-Y H:i:s", $a->orderdt); ?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_USERID');?></td>
			<td><?php echo $a->j_user_id; ?></td>
		</tr>
<!--		<tr>
			<td><?php echo JText::_('SC_NAME');?></td>
			<td><?php echo $a->name; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_EMAIL');?></td>
			<td><?php echo "<a href='mailto:$a->email'>$a->email</a>";?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_ADDRESS');?></td>
			<td>
			<?php echo $a->address; ?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_ZIPCODE');?></td>
			<td>
			<?php echo $a->codepostal; ?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_CITY');?></td>
			<td>
			<?php echo $a->city; ?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_PHONE');?></td>
			<td>
			<?php echo $a->telephone; ?>
			</td>
		</tr> -->
		<tr>
			<td><?php echo JText::_('SC_IP_ADDRESS');?></td>
			<td>
			<?php
	           $iplink = '&nbsp;<a href="http://whois.domaintools.com/'.$a->ipaddress.'" target="_blank" class="scbutton">'.JText::_("SC_CHECKIP")."</a>";
				echo $a->ipaddress;
				echo $iplink;
 			?>
 			<input type="hidden" name="ipaddress" value="<?php echo $a->ipaddress;?>" />
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_SHIP_REGION');?></td>
			<td>
			<?php
				echo $a->shipRegion; ?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_SHIP_COST');?></td>
			<td>
			<?php
				if ($align==1) echo $currency. "&nbsp;";
				echo number_format($a->shipCost, $decs, $decsep, $tsep);
				if ($align==0) echo "&nbsp;". $currency;
				?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_SUBTOTAL');?></td>
			<td>
			<?php
				if ($align==1) echo $currency. "&nbsp;";
				echo number_format($a->total, $decs, $decsep, $tsep);
				if ($align==0) echo "&nbsp;". $currency;
				?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_TAX');?></td>
			<td>
			<?php
				if ($align==1) echo $currency. "&nbsp;";
				echo number_format($a->tax, $decs, $decsep, $tsep);
				if ($align==0) echo "&nbsp;". $currency;
				?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_TOTAL');?></td>
			<td>
			<?php
				if ($align==1) echo $currency. "&nbsp;";

				echo number_format($a->total + $a->tax + $a->shipCost, $decs, $decsep, $tsep);
				if ($align==0) echo "&nbsp;". $currency;
				?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_PAYMENT_ID');?></td>
			<td>
			<?php
				echo $a->paymentcode;
				?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_ORDERCODE');?></td>
			<td>
			<?php
				echo $a->ordercode;
				?>
			</td>
		</tr>
		</table>
		</div>
		<div class="orderblock">
		<table class="adminform">
		<?php
		if (@$a->customfields) {
			echo "<tr><th>".JText::_('Custom fields')."</th><th>&nbsp;</th></tr>";
			$fields=new fields();
			$fieldlist=$fields->getPublishedFields();
			$acfields=unserialize($a->customfields);
			foreach ($fieldlist as $field) {

				$scfield=str_replace(array("[", "]"), "", $field->name);
				if (isset($acfields[$scfield])) {

					if (is_array($acfields[$scfield]))
					{
						echo "<tr>";
						echo "<td>$field->name</td>";
						echo "<td>==".@print_r($acfields[$scfield], 1)."==</td>";
					echo "</tr>";
					}
					else
					{
						switch($field->type) {
							case "text": // textbox field, single line
								echo "<tr><td>".JText::_("$field->caption")."</td><td>";
								echo "<input type='text' name='$field->name' size='$field->length' class='$field->classname' value='". $acfields["$field->name"]."'>";
								break;
							case "textarea": // multiline textbox/textarea, no wysiwyg editor
								echo "<tr><td>".JText::_("$field->caption")."</td><td>";
								@list($cols, $rows)=explode(",", $field->length);
								echo "<textarea name='$field->name' class='$field->classname' cols='$cols' rows='$rows'>". $acfields["$field->name"]."</textarea>";
								break;
							case "radio": // yes/no radio buttons
								echo "<tr><td>".JText::_("$field->caption")."</td><td>";
								echo "<input type='radio' name='$field->name' class='$field->classname' value='yes' ". ($acfields["$field->name"]=="yes"?"checked":"").">". JText::_('JYES');
								echo "<input type='radio' name='$field->name' class='$field->classname' value='no' ". ($acfields["$field->name"]=="no"?"checked":"").">". JText::_('JNO');
								break;
							case "checkbox": // single checkbox
								echo "<tr><td>".JText::_("$field->caption")."</td><td>";
								echo "<input type='checkbox' name='$field->name' class='$field->classname' value='yes' ". ($acfields["$field->name"]=="yes"?"checked":"").">". JText::_('JYES');
								break;
							case "date": // textfield with calendar javascript
								echo "<tr><td>".JText::_("$field->caption")."</td><td>";
								echo "<input type='text' name='$field->name' id='$field->name' size='$field->length' class='$field->classname' value='". $acfields["$field->name"]."'>";
								echo "&nbsp;<a href=\"javascript:NewCal('$field->name','ddMMyyyy',true ,24)\"><img src=\"components/com_simplecaddy/images/cal.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".JText::_("SC_PICK_DATE")."\"/></a>";
								break;
							case "dropdown": // dropdown list, single selection
								echo "<tr><td>".JText::_("$field->caption")."</td><td>";
								echo "<select name='$field->name' id='$field->name' class='$field->classname'>";
								$aoptions=explode(";", $field->fieldcontents);
								foreach ($aoptions as $key=>$value) {
									echo "<option value='$value'".($acfields["$field->name"]=="$value"?" selected":"").">$value</option>";
								}
								echo "</select>";
								break;
						}
						echo "</td>";
						echo "</tr>";
					}
				}
			}
		}
		?>
		<tr>
		<td><a href="index.php?option=com_simplecaddy&action=orders&task=email&oid=<?php echo $a->id?>" class="scbutton"><?php echo JText::_("SC_RESEND_ORDER_CONFIRMATION_EMAIL");?></a></td>
		</tr>
		<tr>
			<td><?php echo JText::_('SC_ORDER_STATUS');?></td>
			<td>
			<?php
				echo "<select name='edtostatus'>";
				foreach ($statuses as $status) {
					$selected=(strtolower($a->status)==strtolower(trim($status))?" selected":"");
					echo "<option value='".trim($status)."' $selected>$status</option>\n";

				}
				echo "</select>";
			?>
			</td>
		</tr>
		</table>
		</div>
		</div>
		<div class="detailblock">
		<table class="adminlist" >
		<tr><th colspan="8"><?php echo JText::_('SC_DETAILS');?></th></tr>
		<tr>
			<th class="title"><?php echo JText::_('SC_CODE');?></th>
			<th class="title"><?php echo JText::_('SC_QUANTITY');?></th>
			<th class="title tdright"><?php echo JText::_('SC_PRICE_PER_UNIT');?></th>
			<th class="title tdright"><?php echo JText::_('SC_TOTAL');?></th>
			<th class="title"><?php echo JText::_('SC_PRODUCT_NAME');?></th>
			<th class="title"><?php echo JText::_('SC_PRODUCT_OPTION');?></th>
			<th class="title"><?php echo JText::_('SC_ACTION');?></th>
			<th class="title">&nbsp;</th>
		</tr>
		<?php
		$autodec=$cfg->get("autodecfromstore");
		$k = 0;
		for ($i=0, $n=count( $items ); $i < $n; $i++) {
		$row = &$items[$i];
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td width="10%">
					<?php echo $row->prodcode; ?>
				</td>
				<td width="30">
					<?php
						echo $row->qty;
					?>
				</td>
				<td width="10%" class="tdright">
					<?php
						echo number_format($row->unitprice, $decs, $decsep, $tsep);
					?>
				</td>
				<td class="tdright">
					<?php
						echo number_format($row->total, $decs, $decsep, $tsep);
					?>
				</td>
				<td width="40%">
					<?php echo $row->shorttext; ?>
				</td>
				<td>
					<?php echo $row->option; ?>&nbsp;
				</td>
				<td>
					<?php
					if ($autodec==0) echo "<a class=\"scbutton\" href=\"index.php?option=com_simplecaddy&action=products&task=decstore&pid=$row->prodcode&qty=$row->qty&order=$a->id\">".JText::_('SC_DECSTORE')."</a>";
					?>
				</td>
				<td>
					&nbsp;
				</td>
				<?php
					$k = 1 - $k; }
				?>
			</tr>
			<?php
				$field=JRequest::getVar( 'field', '');
				$order=JRequest::getVar( 'order', '');
			?>
		</table>
		</div>
		<input type="hidden" name="id" value="<?php echo ($a->id?"$a->id":"-1"); ?>">
		<input type="hidden" name="option" value="com_simplecaddy" />
		<input type="hidden" name="action" value="orders" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="order" value="<?php echo $order; ?>" />
		<input type="hidden" name="field" value="<?php echo $field; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
	<?php
	}

	static function header() {
	global $mainframe;
        JHTML::_('behavior.modal');
		JHTML::stylesheet( 'simplecaddy.css', 'administrator/components/com_simplecaddy/css/' );
	}

	static function view_prod($rows, $name, $categories, $content) {
		$lang = JFactory::getLanguage();
		$lang->load('plg_editors-xtd_scbutton', JPATH_ADMINISTRATOR);
	    $document=JFactory::getDocument();
		JHTML::stylesheet( 'simplecaddy.css', 'administrator/components/com_simplecaddy/css/' );
		?>
		<script>
		function hide(eid) {
			document.getElementById("prodcodes").style.visibility='hidden';
			document.getElementById("categories").style.visibility='hidden';
			document.getElementById("divqties").style.visibility='hidden';
			document.getElementById("divdefqty").style.visibility='hidden';
			document.getElementById("divcheckoos").style.visibility='hidden';
			document.getElementById("divshowbuttons").style.visibility='hidden';
			document.getElementById("divclasssfx").style.visibility='visible';
			if(eid=="buynow") {
				document.getElementById("prodcodes").style.visibility='visible';
				document.getElementById("divqties").style.visibility='visible';
				document.getElementById("divdefqty").style.visibility='visible';
				document.getElementById("divcheckoos").style.visibility='visible';
			}

			if(eid=="category") {
				document.getElementById("categories").style.visibility='visible';
				document.getElementById("divqties").style.visibility='visible';
				document.getElementById("divdefqty").style.visibility='visible';
				document.getElementById("divcheckoos").style.visibility='visible';
			}
			if(eid=="showcart") {
				document.getElementById("divshowbuttons").style.visibility='visible';
			}
			if(eid=="details") {
				document.getElementById("divclasssfx").style.visibility='hidden';
			}
		}

		function setqties(obj) {
			document.getElementById("lstquantities").style.visibility='visible';
			if(obj=="any") {
				document.getElementById("lstquantities").style.visibility='hidden';
			}

		}
		</script>
		<form name="plgtypes">
			<div class="scex_div">
				<div class="scex_span"><?php echo JText::_("SCEX_FUNCTION_TYPE");?></div>
				<select name="plgtype" id="plgtype" onchange="hide(this.value)">
					<option value="buynow">Buy now!</option>
					<option value="showcart">Show Cart</option>
					<option value="editcart">Edit Cart</option>
					<option value="skip">Skip</option>
					<option value="category">Show Category</option>
					<option value="emailorder">Email order</option>
					<option value="details">Show details</option>
					<option value="dllist">Show download list</option>
				</select>
			</div>
			<div id="prodcodes" class="scex_div">
				<div class="scex_span"><?php echo JText::_("SCEX_AVAILABLE_PRODS");?></div>
				<select name="prodcode" id="prodcode">
				<?php
					foreach ($rows as $row) {
						echo "<option value='$row->prodcode'>$row->shorttext (code: $row->prodcode)</option>";
					}
				?>
				</select>
			</div>

			<div id="categories" class="scex_div" style="visibility: hidden;">
				<div class="scex_span"><?php echo JText::_("SCEX_AVAILABLE_CATEGORIES");?></div>
				<select name="category" id="category">
				<?php
				print_r($categories);
					foreach ($categories as $key=>$row) {
						echo "<option value='$row'>$row</option>";
					}
				?>
				</select>
			</div>

			<div id="contentlist" class="scex_div">
				<div class="scex_span"><?php echo JText::_("SCEX_NEXTCID");?></div>
				<select id="content" name="content">
					<?php
					foreach ($content as $c) {
						echo "<option value='$c->id'>$c->title</option>";
					}
					?>
				</select>
			</div>
			<div class="scex_div" id="divqties">
			<div class="scex_span"><?php echo JText::_("SCEX_QUANTITIES");?></div>
			<select name="qties" onchange="setqties(this.value)" id="qties">
			<option value="any"><?php echo JText::_("SCEX_ANY_QUANTITY");?></option>
			<option value="discreet"><?php echo JText::_("SCEX_FIXED_QUANTITIES");?></option>
			</select>
			<div id="lstquantities" style="visibility: hidden; display: inline-block;"><?php echo JText::_("SCEX_QUANTITIES");?><input type="text" size="60" name="qties" value="" id="theqties" /></div>
			</div>
			<div class="scex_div" id="divdefqty">
			<div class="scex_span"><?php echo JText::_("SCEX_DEFQTY");?></div>
			<input type="text" size="5" name="defqty" value="1" id="defqty" />
			</div>
			<div class="scex_div" id="divcheckoos">
			<div class="scex_span"><?php echo JText::_("SCEX_CHECKOOS");?></div>
			<select name="checkoos" id="checkoos">
			<option value="0"><?php echo JText::_("SCEX_NO");?></option>
			<option value="1"><?php echo JText::_("SCEX_YES");?></option>
			</select>
			</div>
			<div class="scex_div" id="divshowbuttons" style="visibility: hidden;">
			<div class="scex_span"><?php echo JText::_("SCEX_SHOW_BUTTONS");?></div>
			<select name="showbuttons" id="showbuttons">
			<option value="0"><?php echo JText::_("SCEX_NO");?></option>
			<option value="1"><?php echo JText::_("SCEX_YES");?></option>
			</select>
			</div>

			<div class="scex_div" id="divclasssfx">
			<div class="scex_span"><?php echo JText::_("SCEX_CLASS_SUFFIX");?></div>
			<input type="text" name="classsfx" value="" id="classsfx" />
			</div>
		</form>
		<script>
		function makecode() {
			var plgtype=document.getElementById("plgtype").value;
			var plgcode="{simplecaddy type="+plgtype;
			if (plgtype=="buynow") {
				var plgcode=plgcode+" code="+document.getElementById("prodcode").value;
			}
			if (plgtype=="category") {
				var plgcode=plgcode+" category="+document.getElementById("category").value;
			}
			if ((plgtype=="buynow") || (plgtype=="category")){
				var qty=document.getElementById("qties").value;
				if (qty!="any") {
					var plgcode=plgcode+" qties="+document.getElementById("theqties").value;
				}
				var defqty=document.getElementById("defqty").value;
				if (defqty>1) {
					var plgcode=plgcode+" defqty="+document.getElementById("defqty").value;
				}
				var checkoos=document.getElementById("checkoos").value;
				if (checkoos==1) {
					var plgcode=plgcode+" checkoos=1";
				}
			}
			if (plgtype=="showcart"){
				var showbuttons=document.getElementById("showbuttons").value;
				if (showbuttons==1) {
					var plgcode=plgcode+" showbuttons=1";
				}

			}
			var plgcode=plgcode+" nextcid="+document.getElementById("content").value;

				var classsfx=document.getElementById("classsfx").value;
				if (classsfx!="") {
					var plgcode=plgcode+" classsfx="+document.getElementById("classsfx").value;
				}
			var plgcode=plgcode + "}";
			document.getElementById("sccode").value=plgcode;
			return plgcode;
		}
		</script>
		<div class="scex_div">
		<form action="index.php?option=com_simplecaddy&action=insertplugincode" method="post">
		<div class="scex_div">
		<div class="scex_span"><?php echo JText::_("SCEX_CURRENT_CODE");?></div>
		<input type="text" size="120" name="sccode" value="" id="sccode" /></div>
		<div class="scex_div">
		<button name="codeview" onclick="makecode();return false;"><?php echo JText::_("SCEX_VIEW_CODE");?></button>
		<button name="btn" onclick="window.parent.jInsertEditorText(makecode(), '<?php echo $name; ?>');return false;"><?php echo JText::_('SCEX_INSERT') ?></button>

		<button type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('SCEX_CLOSE') ?></button>
		</div>
		</form></div>
		<?php
	}

	static function view_prod2($alist) {
	global $mainframe;
    $document=JFactory::getDocument();
	JHTML::stylesheet( 'simplecaddy.css', 'administrator/components/com_simplecaddy/css/' );

	$e_name	=	JRequest::getVar( 'e_name' );
		?>
		<link rel="stylesheet" href="<?php echo $stylesheet ?>" type="text/css" />
		<script type="text/javascript" language="javascript">
			function insertCode(plugincode) {
				var editor = '<?php echo $e_name; ?>';
				var sccode = '{simplecaddy code='+plugincode;
                var compo=document.getElementById("component").value;
                if (compo=="com_phocagallery") {// special for PhocaGallery
                    var alias=window.parent.document.getElementById("alias").value;
                    sccode = sccode + " picname="+alias;
                }
                var clssfx=document.getElementById("clssfx").value;
                if (clssfx!="") sccode = sccode + " classsfx="+clssfx;
                var defqty=document.getElementById("defqty").value;
                if (defqty!="") sccode = sccode + " defqty="+defqty;
                var minqty=document.getElementById("minqty").value;
                if (minqty!="") sccode = sccode + " minqty="+minqty;
                var qties=document.getElementById("qties").value;
                if (qties!="") sccode = sccode + " qties="+qties;
                var checkoos=document.getElementById("checkoos").value;
                if (checkoos!="") sccode = sccode + " checkoos=1";
                sccode = sccode + '}';
				window.parent.jInsertEditorText(sccode, editor);
				window.parent.document.getElementById('sbox-window').close();
			}
			function insertCat(plugincode) {
				var editor = '<?php echo $e_name; ?>';
				var sccode = '{simplecaddy category='+plugincode+'}';
				window.parent.jInsertEditorText(sccode, editor);
				window.parent.document.getElementById('sbox-window').close();
			}
		</script>
        <table class='codelist' width='100%'>
        <tr>
        <td>
            <?php
                echo JText::_("SC_CLASS_SUFFIX");
            ?>
        </td>
        <td><input type="text" name="clssfx" id="clssfx" /></td>
        </tr>
        <tr>
        <td>
            <?php
                echo JText::_("SC_DEF_QTY");
            ?>
        </td>
        <td><input type="text" name="defqty" id="defqty" /></td>
        </tr>
        <tr>
        <td>
            <?php
                echo JText::_("SC_MIN_QTY");
            ?>
        </td>
        <td><input type="text" name="minqty" id="minqty" /></td>
        </tr>
        <tr>
        <td>
            <?php
                echo JText::_("SC_QTIES");
            ?>
        </td>
        <td><input type="text" name="qties" id="qties" /></td>
        </tr>
        <tr>
        <td>
            <?php
          		$cfg=new sc_configuration();
                if ($cfg->get("checkminqty") == "1") {
                    $disabled="";
                    echo JText::_("SC_CHECK_OOS");
                }
                else
                {
                    $disabled=" disabled";
                    echo JText::_("SC_NO_CHECK_OOS");
                }

            ?>
        </td>
        <td>

            <input type="checkbox" name="checkoos" id="checkoos" <?php echo $disabled;?> /></td>
        </tr>
        </table>
        <table class='codelist' width='100%'>
        <input type="hidden" name="component" id="component" value="<?php echo JRequest::getVar("component");?>" />
		<?php
		echo "<tr><th>".JText::_('SC_CLICK_CODE')."</th></tr>";
		$k=0;

		foreach ($alist as $product) {
   			echo "<tr class='row$k'><td>$product->category&nbsp;<a class='codelist' href='#' onclick=\"insertCode('$product->prodcode');\">$product->shorttext (code: $product->prodcode)</a></td></tr>";
			$k=1-$k;
		}
        ?>
		</table>

        <?php
		$cfg=new sc_configuration();
		$aclist=$cfg->get("prodcats");
		$clist=explode("\r\n", $aclist);
		echo "<table class='codelist' width='100%'>";
		echo "<tr><th>".JText::_('SC_CLICK_CATEGORY')."</th></tr>";
		$k=0;
		foreach ($clist as $key=>$cat) {
			echo "<tr class='row$k'><td><a class='codelist' href='#' onclick=\"insertCat('$cat');\">$cat</a></td></tr>";
			$k=1-$k;
		}
		echo "</table>";
	}

    static function showoptgroup($optiongroup, $prodid) { // shows option group edit screen
        $showas=new optionsshowas();
        ?>
        <p><h3><?php echo JText::_("SC_OPTIONGROUP");?></h3></p>
       <form name="frmoptgroup" action="index.php" method="post" target="_parent" >
        <table border="0" width="100%">
        <tr><td>&nbsp;</td>
        <td align="right">
        <input type="button" onclick="document.frmoptgroup.submit();" value="<?php echo JText::_('SC_SAVE');?>" />
        </td>
        </tr>
        <tr>
        <td><?php echo JText::_('SC_GROUPTITLE');?></td>
        <td><input type="text" name="title" value="<?php echo $optiongroup->title; ?>" /></td>
        </tr>
        <tr>
        <td><?php echo JText::_('SC_DISPLAYORDER');?></td>
        <td><input name="disporder" size="1" value="<?php echo $optiongroup->disporder; ?>" type="text" /></td>
        </tr>
        <tr>
 		<td><?php echo JText::_('SC_SHOW_AS');?>
		</td>
		<td>
			<select name="showas">
            <?php
                foreach ($showas->type as $key=>$value) {
                    echo "\n<option value='$key' ".($optiongroup->showas==$key?" selected":"").">". $showas->type[$key] . " </option>";
                }
            ?>
			</select>
		</td>
        </tr>

        </table>
        <input type="hidden" name="productid" value="<?php echo $prodid;?>" />
        <input type="hidden" name="id" value="<?php echo $optiongroup->id;?>" />
        <input type="hidden" name="prodcode" value="<?php echo $optiongroup->prodcode;?>" />
        <input type="hidden" name="option" value="com_simplecaddy" />
        <input type="hidden" name="action" value="optiongroups" />
        <input type="hidden" name="task" value="saveoptiongroup" />
        </form>
        <?php
    }

    static function showindoptions(&$rows, $optgrid, $productid) { // shows individual options
		$document	=& JFactory::getDocument();
		$document->addScript( JURI::root(true).'/administrator/components/com_simplecaddy/js/caddy.js');
        $og=new optiongroups();
        $og->load($optgrid);
        ?>
        <form name="frmindoptions" method="post" action="index.php">
		<table border="1" class="adminform" width="100%"><tr><th colspan="2"><?php echo JText::_('SC_OPTIONS');?></th></tr>
		<tr>
		<td width="185"><?php echo stripslashes( JText::_('SC_OPTIONS_TITLE'));?></td>
		<td>
        <?php echo $og->title;?>
		</td>
		</tr>
        <tr>
        <td>&nbsp;</td>
        <td align="right">
			<input type="button" name="savebtn" value="<?php echo JText::_('SC_SAVE');?>" onclick="return document.frmindoptions.submit();return window.parent.SqueezeBox.close();" />
			&nbsp;
			<input type="button" name="closebtn" value="<?php echo JText::_('SC_CLOSE');?>" onclick="return window.parent.SqueezeBox.close();" />
		</td>

        </tr>
		<tr>
		<td><?php echo stripslashes( JText::_('SC_IND_OPTIONS'));?>
		</td>
		<td>

			<input type="button" name="addbtn" onclick="addRow()" value="<?php echo JText::_('Add Option');?>" />&nbsp;<input type="button" name="delbtn" onclick="deleteRow()" value="<?php echo JText::_('Remove option');?>" />
			<table id="mine" border="1" class="adminform">
			<tr><th width="20">#</th><th width="40"><?php echo JText::_('Description') ?></th><th width="40"><?php echo JText::_('Formula');?></th><th width="40"><?php echo JText::_('Caption');?></th><th width="20"><?php echo JText::_("Display order");?></th><th width="80"><?php echo JText::_('Default select');?></th><th>&nbsp;</th></tr>
			<?php
                //if (is_array($rows))
                {
				foreach ($rows as $key=>$line) {

					echo "<tr>";
					echo "<td><input type='checkbox' name='tid$key' id='tid$key' value='$line->id'></td>";
					echo "<td><input type='hidden' name='optionid[]' value='$line->id'><input type='text' size='30' name='optionshorttext[]' value='$line->description' ></td>" ;
					echo "<td><input type='text' name='optionformula[]' value='$line->formula' ></td>" ;
					echo "<td><input type='text' name='optioncaption[]' value='$line->caption' ></td>" ;
					echo "<td><input type='text' name='optiondisporder[]' value='$line->disporder' size='1'></td>" ;
					echo "<td><input type='radio' name='optiondefselect' value='$key' ".($line->defselect=="1"?"checked":"")."></td>" ;
					echo "</tr>";
				}
                }
			?>

			</table>
			<input type="hidden" name="rows" value="" id="rows" />
			<input type="hidden" name="rows2" value="" id="rows2" />
		</td>
		</tr>
		</table>
		<input type="hidden" name="option" value="com_simplecaddy" />
		<input type="hidden" name="optgrid" value="<?php echo $optgrid;?>" />
		<input type="hidden" name="productid" value="<?php echo $productid;?>" />
		<input type="hidden" name="action" value="options" />
		<input type="hidden" name="task" value="saveoptions" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
        <?php
    }

    static function showPluginlist($rows) {
		display::header();
		JToolBarHelper::title( JText::_( "SC_SIMPLECADDY_PLUGINS" ), 'generic.png');
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'Main', false,  false );
    	echo "<table class='adminlist'>";
    	echo "<tr><th>".JText::_("SC_PLUGIN_NAME")."</th><th>".JText::_("SC_ENABLED")."</th></tr>";
    	foreach ($rows as $row) {
    		echo "<tr><td>";
			echo "<a href='index.php?option=com_simplecaddy&action=pluginconfig&task=showpluginconfig&pluginname=$row->element'>$row->name</a>";
    		echo "<td>";
    		$toenable=1-$row->enabled;
    		echo "<a href='index.php?option=com_simplecaddy&action=pluginconfig&task=enableplugin&id=$row->extension_id&enable=$toenable'>";
			echo "<img class='plugin_enabled$row->enabled' /></a>";
			echo "</td></tr>";
    	}
    	echo "</table>";
		?>
		<form name="adminForm">
		<input type="hidden" name="option" value="com_simplecaddy" />
		<input type="hidden" name="action" value="" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
    }

}