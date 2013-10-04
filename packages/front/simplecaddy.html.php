<?php
/**
* @package SimpleCaddy 2.0.4 for Joomla 2.5
* @copyright Copyright (C) 2006-2013 Henk von Pickartz. All rights reserved.
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class display {
	function view_prod2($alist) {
	global $mainframe;
	$stylesheet= JPATH_COMPONENT.DS.'css'.DS.'simplecaddy.css';
		?>
		<link rel="stylesheet" href="<?php echo $stylesheet ?>" type="text/css" />
		<script type="text/javascript" language="javascript">
			function insertCode(plugincode) {
				var sccode = '{simplecaddy code='+plugincode+'}';
				window.parent.jInsertEditorText(sccode, 'text');
				window.parent.document.getElementById('sbox-window').close();
			}
			function insertCat(plugincode) {
				var sccode = '{simplecaddy category='+plugincode+'}';
				window.parent.jInsertEditorText(sccode, 'text');
				window.parent.document.getElementById('sbox-window').close();
			}
		</script>
		<?php
		echo "<table class='codelist' width='100%'>";
		echo "<tr><th>".JText::_('SC_CLICK_CODE')."</th></tr>";
		$k=0;
		foreach ($alist as $product) {
			echo "<tr class='row$k'><td>$product->category&nbsp;<a class='codelist' href='#' onclick=\"insertCode('$product->prodcode');\">$product->shorttext (code: $product->prodcode)</a></td></tr>";
			$k=1-$k;
		}
		echo "</table>";
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

	function view_prod($rows, $name, $categories, $content) {
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
	
}
?>
