<?php
/**
* @package SimpleCaddy 2.0.4 for Joomla 2.5
* @copyright Copyright (C) 2006-2012 Henk von Pickartz. All rights reserved.
* SimpleCaddy plugin
*/
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die();
$debug=0;

jimport( 'joomla.plugin.plugin' );

require_once (JPATH_ROOT.DS.'components'.DS.'com_simplecaddy'.DS.'simplecaddy.class.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_simplecaddy'.DS.'simplecaddy.cart.class.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_simplecaddy'.DS.'simplecaddy.content.class.php');

/**
 * configuration class
 */

class simplecaddyconfiguration extends JTable { // this is the standard configuration class for the plugin
// naming is {pluginname}configuration
	var $id;
	var $showproductcodetext;
	var $showproductcode;
	var $showtitle;
	var $showunitpricetext;
	var $showunitprice;
	var $showquantitytext;
	var $showquantity;
	var $showcartoptionstitle;
	var $showproductcodecart;
	var $showavailablequantity;
	var $showavailablequantitytext;
	var $skipstrings;

	function simplecaddyconfiguration() { // initialise class and check if data table exists. If not, create it
		// Load plugin language, since we use it in the backend, standard language loading is NOT performed
		$lang = JFactory::getLanguage();
		$lang->load('plg_content_simplecaddy', JPATH_ADMINISTRATOR);
		$db	= JFactory::getDBO();
		// next 2 lines: suppress any legitimate errors on checking if the table exists
	   	@$this->__construct( '#__sc_simplecaddy', 'id', $db );
		if (@count($db->getTableColumns($this->_tbl))==0 ) {
			// no columns found, so we need to create the table
			$query= "CREATE TABLE `#__sc_simplecaddy` (
			`id`  int NULL AUTO_INCREMENT ,
			`showproductcodetext`  INT(11) NOT NULL ,
			`showproductcode`  INT(11) NOT NULL ,
			`showtitle`  INT(11) NOT NULL ,
			`showunitpricetext`  INT(11) NOT NULL ,
			`showunitprice`  INT(11) NOT NULL ,
			`showquantitytext`  INT(11) NOT NULL ,
			`showquantity`  INT(11) NOT NULL ,
			`showcartoptionstitle`  INT(11) NOT NULL ,
			`showproductcodecart`  INT(11) NOT NULL ,
			`showavailablequantity`  INT(11) NOT NULL ,
			`showavailablequantitytext`  INT(11) NOT NULL ,
			PRIMARY KEY (`id`)
			)
			;";
			$this->_db->setQuery($query);
			$this->_db->query();
	   		$this->__construct( '#__sc_simplecaddy', 'id', $db );
		};
		$atable = $db->getTableFields($this->_tbl);
		if (!isset($atable["skipstrings"])) {
			$query="ALTER TABLE `#__sc_simplecaddy`
			ADD COLUMN `skipstrings`  text NULL AFTER `showavailablequantitytext`;";
			$this->_db->setQuery($query);
			$this->_db->query();
		}
		// get the first and only useful record in the db here
		$query="select `id` from `$this->_tbl` ";
		$this->_db->setQuery($query);
		$id=$this->_db->loadResult(); // this sets the first ID as the one to use.
		$this->load($id); // load the first id
	}

	function _showconfig() { // mandatory function name
		$lang = JFactory::getLanguage(); // joomla's backend does not load frontend languages by itself
		$extension = 'plg_content_simplecaddy'; // so we need to do this manually here
		$lang->load($extension);

		require_once(JPATH_COMPONENT_SITE."/simplecaddy.class.php");
		$fields=new fields();
		$fieldlist=$fields->getFieldNames(); // get all the fields, standard and custom
		$this->load();
		display::header(); // while this would be better inside the display function, a plugin does not have access to this
		JToolBarHelper::title( JText::_( "SC_PLUGIN_CONFIG" ), 'generic.png');
		JToolbarHelper::custom("apply", "apply", "", JText::_("SC_APPLY"), false);
		JToolbarHelper::custom("save", "save", "", JText::_("SC_SAVE"), false);
		JToolbarHelper::cancel();
		?>
		<form name="adminForm">
			<table class="adminform">
				<tr><th><?php echo JText::_("SC_OPTION");?></th><th><?php echo JText::_("SC_SETTING");?></th></tr>
				<tr><td style="width: 150px;"><?php echo JText::_("SC_PRODUCT_CODE");?></td>
				<td>
				<?php
					$show_hide = array (JHTML::_('select.option', 0, JText::_('SC_HIDE')), JHTML::_('select.option', 1, JText::_('SC_SHOW')),);
					foreach ($show_hide as $value) {
						echo "<input type='radio' value='$value->value' name='showproductcode' ".($this->showproductcode==$value->value?' checked':'').">$value->text";
					}
				?>
				</td></tr>

				<tr><td style="width: 150px;"><?php echo JText::_("SC_PRODUCT_CODE_TEXT");?></td>
				<td>
				<?php
					$show_hide = array (JHTML::_('select.option', 0, JText::_('SC_HIDE')), JHTML::_('select.option', 1, JText::_('SC_SHOW')),);
					foreach ($show_hide as $value) {
						echo "<input type='radio' value='$value->value' name='showproductcodetext' ".($this->showproductcodetext==$value->value?' checked':'').">$value->text";
					}
				?>
				</td></tr>

				<tr><td style="width: 150px;"><?php echo JText::_("SC_PRODUCT_TITLE");?></td>
				<td>
				<?php
					$show_hide = array (JHTML::_('select.option', 0, JText::_('SC_HIDE')), JHTML::_('select.option', 1, JText::_('SC_SHOW')),);
					foreach ($show_hide as $value) {
						echo "<input type='radio' value='$value->value' name='showtitle' ".($this->showtitle==$value->value?' checked':'').">$value->text";
					}
				?>
				</td></tr>

				<tr><td style="width: 150px;"><?php echo JText::_("SC_QUANTITY_FIELD");?></td>
				<td>
				<?php
					$show_hide = array (JHTML::_('select.option', 0, JText::_('SC_HIDE')), JHTML::_('select.option', 1, JText::_('SC_SHOW')),);
					foreach ($show_hide as $value) {
						echo "<input type='radio' value='$value->value' name='showquantity' ".($this->showquantity==$value->value?' checked':'').">$value->text";
					}
				?>
				</td></tr>

				<tr><td style="width: 150px;"><?php echo JText::_("SC_QUANTITY_FIELD_TEXT");?></td>
				<td>
				<?php
					$show_hide = array (JHTML::_('select.option', 0, JText::_('SC_HIDE')), JHTML::_('select.option', 1, JText::_('SC_SHOW')),);
					foreach ($show_hide as $value) {
						echo "<input type='radio' value='$value->value' name='showquantitytext' ".($this->showquantitytext==$value->value?' checked':'').">$value->text";
					}
				?>
				</td></tr>

				<tr><td style="width: 150px;"><?php echo JText::_("SC_SHOW_OPTIONS_TITLE");?></td>
				<td>
				<?php
					$show_hide = array (JHTML::_('select.option', 0, JText::_('SC_HIDE')), JHTML::_('select.option', 1, JText::_('SC_SHOW')),);
					foreach ($show_hide as $value) {
						echo "<input type='radio' value='$value->value' name='showcartoptionstitle' ".($this->showcartoptionstitle==$value->value?' checked':'').">$value->text";
					}
				?>
				</td></tr>

				<tr><td style="width: 150px;"><?php echo JText::_("SC_SHOW_UNITPRICE");?></td>
				<td>
				<?php
					$show_hide = array (JHTML::_('select.option', 0, JText::_('SC_HIDE')), JHTML::_('select.option', 1, JText::_('SC_SHOW')),);
					foreach ($show_hide as $value) {
						echo "<input type='radio' value='$value->value' name='showunitprice' ".($this->showunitprice==$value->value?' checked':'').">$value->text";
					}
				?>
				</td></tr>

				<tr><td style="width: 150px;"><?php echo JText::_("SC_SHOW_UNITPRICE_TEXT");?></td>
				<td>
				<?php
					$show_hide = array (JHTML::_('select.option', 0, JText::_('SC_HIDE')), JHTML::_('select.option', 1, JText::_('SC_SHOW')),);
					foreach ($show_hide as $value) {
						echo "<input type='radio' value='$value->value' name='showunitpricetext' ".($this->showunitpricetext==$value->value?' checked':'').">$value->text";
					}
				?>
				</td></tr>

				<tr><td style="width: 150px;"><?php echo JText::_("SC_SHOW_AVAILABLE_QUANTITY");?></td>
				<td>
				<?php
					$show_hide = array (JHTML::_('select.option', 0, JText::_('SC_HIDE')), JHTML::_('select.option', 1, JText::_('SC_SHOW')),);
					foreach ($show_hide as $value) {
						echo "<input type='radio' value='$value->value' name='showavailablequantity' ".($this->showavailablequantity==$value->value?' checked':'').">$value->text";
					}
				?>
				</td></tr>

				<tr><td style="width: 150px;"><?php echo JText::_("SC_SHOW_AVAILABLE_QUANTITYTEXT");?></td>
				<td>
				<?php
					$show_hide = array (JHTML::_('select.option', 0, JText::_('SC_HIDE')), JHTML::_('select.option', 1, JText::_('SC_SHOW')),);
					foreach ($show_hide as $value) {
						echo "<input type='radio' value='$value->value' name='showavailablequantitytext' ".($this->showavailablequantitytext==$value->value?' checked':'').">$value->text";
					}
				?>
				</td></tr>
				<?php
					$astrings=explode("\r\n", $this->skipstrings);
					$i=0;
					$extra=false;
					foreach ($astrings as $aline) {
						@list($name, $label)=explode(":", $aline);
						if (trim($aline) == "") continue;
						echo "<tr>";
						echo "<td style=\"width: 250px;\">".JText::_("SC_LABEL_NAME")."<input type='text' name='skipname[$i]' value='$name' /></td>";
						echo "<td>".JText::_("SC_LABEL_STRING")."<input type='text' name='skiplabel[$i]' value='$label' /></td>";
						echo "</tr>";
						$i++;

					}
					// these are for the extra line
						echo "<tr>";
						echo "<td style=\"width: 250px;\">".JText::_("SC_LABEL_NAME")."<input type='text' name='skipname[$i]' value='' /></td>";
						echo "<td>".JText::_("SC_LABEL_STRING")."<input type='text' name='skiplabel[$i]' value='' /></td>";
						echo "</tr>";
				?>

				<input type="hidden" name="option" value="com_simplecaddy" />
				<input type="hidden" name="action" value="pluginconfig"/>
				<input type="hidden" name="pluginname" value="simplecaddy"/>
				<input type="hidden" name="task" />
				<input type="hidden" name="id" value="<?php echo $this->id;?>"/>
			</table>
		</form>
		<?php
	}

	function apply() {
		$this->save(true);
	}

	function save($stay=false, $orderingFilter = '', $ignore = '') { // mandatory function to save any variables
		$this->bind($_REQUEST); // get all the fields from the admin form
		$skipnames=JRequest::getVar("skipname");
		$skipstrings=JRequest::getVar("skiplabel");
		$skips="";
		foreach($skipnames as $key=>$value) {
			$label=$skipstrings[$key];
			if ( ($value!="") and ($label!="") ) $skips.= "$value:$label\r\n";
		}
		$skips=trim($skips);
		$this->skipstrings=$skips;
		$b=$this->store(); // store the relevant fields only
		$msg=JText::_("SC_CONFIG_SAVED");
		if (!$b) $msg .= ' ' . $this->_db->getErrorMsg();
		$mainframe=JFactory::getApplication();
		if (!$stay) {
			$mainframe->redirect("index.php?option=com_simplecaddy&action=plugins&task=show", $msg);
		}
		else
		{
			$mainframe->redirect("index.php?option=com_simplecaddy&action=pluginconfig&task=showpluginconfig&pluginname=simplecaddy", $msg);
		}
	}

	function cancel() {
		$mainframe=JFactory::getApplication();
		$mainframe->redirect("index.php?option=com_simplecaddy&action=plugins&task=show");
	}
}


class plgContentSimplecaddy extends JPlugin {
	var $tsep;
	var $dsep;
	var $decs;
	var $currency;
	var $curralign;
	var $separator;
	var $thiscid;
	var $itemid;
	var $_plugin_number	= 0;

	function plgContentSimplecaddy( &$subject, $config ) {
        parent::__construct( $subject, $config );
        $this->loadLanguage();
	}

	public function _setPluginNumber() {
		$this->_plugin_number = (int)$this->_plugin_number + 1;
	}


	public function onContentPrepare($context, &$article, &$params, $page = 0) {
		// first check if the component is not missing or unavailable
		if (!JComponentHelper::isEnabled('com_simplecaddy', true)) {
			echo "<div style='color:red;'>The SimpleCaddy component is not installed or is not enabled</div>";
			return;
		}
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$item	= $menu->getActive();
		$this->thiscid=@$item->query["id"];
		$this->itemid=@$item->id;
		$this->thiscid=JRequest::getVar("id");

        $cfg=new sc_configuration(); // read from the component configurations
        $this->tsep=$cfg->get('thousand_sep');
        $this->dsep=$cfg->get('decimal_sep');
        $this->decs=$cfg->get('decimals');
        $this->currency=$cfg->get('currency');
        $this->curralign=$cfg->get('curralign');

        $regex = '/{(simplecaddy)\s*(.*?)}/i';
        $plugin = JPluginHelper::getPlugin('content', 'simplecaddy');

		$view=JRequest::getVar("view");

        $parms=array();
        $matches = array();
        if (isset($article->text)) {
	        @preg_match_all( $regex, $article->text, $matches, PREG_SET_ORDER );
        }
        else
        {
	        @preg_match_all( $regex, $article->introtext, $matches, PREG_SET_ORDER );
        }

        foreach ($matches as $elm) {
 			$this->_setPluginNumber();
 			if ($this->_plugin_number==1) { // get the stylesheet only ONCE per page
				JHTML::stylesheet('components/com_simplecaddy/css/simplecaddy.css' );
				// on windows servers this may need to be changed to
				// JHTML::stylesheet('components\com_simplecaddy\css\simplecaddy.css' );
 			}
			$html ="";
            $line=strip_tags($elm[2]); // get rid of most of the embedded html tags, if any. Only tags directly after the { will not be cleaned as regex will not pick those up
			$line=str_replace("&nbsp;", " ", $line);
            $line=str_replace(" ", "&", $line);
            $line=strtolower($line);
            parse_str( $line, $parms );
            if (!isset($parms['type'])) { // no type provided, so either an old style plugin or just forgot...
            	$parms["type"]="buynow";
            }
        	// get all different types of display here
        	switch (strtolower($parms['type']) ) {
        		case "details":
        			$html.=$this->details($parms);
        			break;
        		case "showcart":
        			$html.=$this->showcart($parms);
        			break;
        		case "editcart":
        			$html.=$this->editcart($parms);
        			break;
        		case "skip":
        			$html.=$this->skip($parms);
        			break;
       			case "category":
                    $html.=$this->getSCbyCategory($parms);
           			break;
      			case "emailorder":
                    $html.=$this->emailorder($parms);
           			break;
        		case "emailbutton":
                    $html.=$this->emailbutton($parms);
           			break;
        		case "orderbutton":
                    $html.=$this->orderbutton($parms);
           			break;
        		case "editdetails":
        			$html.=$this->editdetails($parms);
        			break;
      			case "dllist":
                    $html.=$this->dllist($parms);
           			break;
       			case "emptycart":
                    $html.=$this->emptycart($parms);
           			break;
       			case "displayorder":
                    $html.=$this->displayorder($parms);
           			break;
       			case "listorders":
                    $html.=$this->listorders($parms);
           			break;
       			case "buynow": // the default if nothing has been provided
                	$html.=$this->getSCSingle($parms);
                	break;
        		default: // anything else provides an error message
                	$html.=JText::_("SC_THIS_PLUGIN_TYPE_NOT_SUPPORTED"). "({$parms['type']})";
        	}
			// check if we are displaying standard content. If not, display warning

//			if (($view!="article") and (!isset($parms["nextcid"]))) {
				// if this is not an article view and no nextcid is provided display warning
//				$html .= JText::_("SC_NOT_ARTICLE_WARNING");
//			}

			if (isset($article->text)) {
	            $article->text = preg_replace($regex, $html, $article->text, 1);
			}
			else
			{
	            $article->introtext = preg_replace($regex, $html, $article->introtext, 1);
			}
        }
        return false;
	}



    function optionshow1($lstoptions, $product, $groupid, $classsuffix, $picname="") {// horizontal radio buttons
        $html="";
        foreach ($lstoptions as $po) {
            $optid=$po->id;
            $shorttext=$po->description;
            $formula=$po->formula;
            $caption=$po->caption;
            $defselect=$po->defselect;
            $id=md5($product->prodcode.$shorttext.$picname);
            $checked="";
            if (trim($defselect) == "1") $checked=" checked='checked' ";
            $html .= "<input type='radio' name='edtoption[$groupid]' value='$id:$optid' $checked />".stripslashes($shorttext)."\n";
        }
        return $html;
    }

    function optionshow2($lstoptions, $product, $groupid, $classsuffix, $picname="") {// dropdown list
        $html="";
        $html.="<div class='scoptionselect$classsuffix'><select name='edtoption[$groupid]'>\n";
        foreach ($lstoptions as $po) {
            $optid=$po->id;
            $shorttext=$po->description;
            $formula=$po->formula;
            $caption=$po->caption;
            $defselect=$po->defselect;
            $id=md5($product->prodcode.$shorttext.$picname);
            $checked="";
            if (trim($defselect) == "1") $checked=" selected='selected' ";
            $html .= "<option value='$id:$optid' $checked>".stripslashes($shorttext)." $caption</option>\n";
        }
        $html .= "</select></div>\n";
        return $html;
   }

   function optionshow3($lstoptions, $product, $groupid, $classsuffix, $picname="") {// standard list
        $html="";
		$html.="<div class='scoptionselect$classsuffix'><select name='edtoption[$groupid]' size='10'>\n";
		foreach ($lstoptions as $po) {
            $optid=$po->id;
            $shorttext=$po->description;
            $formula=$po->formula;
            $caption=$po->caption;
            $defselect=$po->defselect;
            $id=md5($product->prodcode.$shorttext.$picname);
            $checked="";
            if (trim($defselect) == "1") $checked=" selected='selected' ";
            $html .= "<option value='$id:$optid' $checked>".stripslashes($shorttext)." $caption</option>\n";
		}
		$html .= "</select></div>\n";
        return $html;
    }

    function optionshow4($lstoptions, $product, $groupid, $classsuffix, $picname="") {// vertical radio buttons
        $html="";
		foreach ($lstoptions as $po) {
            $optid=$po->id;
            $shorttext=$po->description;
            $formula=$po->formula;
            $caption=$po->caption;
            $defselect=$po->defselect;
            $id=md5($product->prodcode.$shorttext.$picname);
            $checked="";
            if (trim($defselect) == "1") $checked=" checked='checked' ";
            $html .= "\n<input type='radio' name='edtoption[$groupid]' value='$id:$optid' $checked />".stripslashes($shorttext);
            $html.=" $caption<br />";
		}
        return $html;
    }

    function optionshow5($lstoptions, $product, $groupid, $classsuffix, $picname="") {// free text
        $html="";
		$html.="<input type='text' name='edtoption[$groupid]'>\n";
		$html.="<input type='hidden' name='edtoption2' value='hidden'>\n";
        return $html;
    }

    function optionshow6($lstoptions, $product, $groupid, $picname="") {// calendar control
        $html="";
        $html.="<script type='text/JavaScript' src='components/com_simplecaddy/js/jacs.js'></script>";
		$html.="<input type='text' name='edtoption[$groupid]' onClick='JACS.show(this,event);'>\n";
		$html.="<input type='hidden' name='edtoption2' value='hidden'>\n";
        return $html;
    }

	function getSCSingle($params) {
		global $Itemid, $mainframe;
		$classsuffix=isset($params['classsfx']) ? $params['classsfx'] : "";
		$prodcode=@$params['code'];
		$defaultqty=isset($params['defqty']) ? $params['defqty'] : 1; // default qty set in qty edit box
		$minqty=isset($params['minqty']) ? $params['minqty'] : 0;
		$strqties=isset($params['qties']) ? $params['qties'] : null;
		$checkoos=isset($params['checkoos']) ? $params['checkoos'] : 0;
		$picname=isset($params['picname']) ? $params['picname'] : "";
		$nextcid=isset($params['nextcid']) ? $params['nextcid'] : $this->thiscid;
		$aqties=explode(",", $strqties);
		$pcfg=new simplecaddyconfiguration();

		$db	= JFactory::getDBO();
		$query="SELECT * FROM #__sc_products WHERE prodcode='$prodcode'";
		$db->setQuery( $query );
		$product = $db->loadObject();

		if (@!$product->id) {
			$html  ="<div class='sccart$classsuffix'>";
            $str= JText::sprintf("SC_PRODUCT_NOT_FOUND", $prodcode);
			$html .= $str;
			$html .="</div>";
			return $html;
		}

		if ($product->published=='0') {
			$html  ="<div class='sccart$classsuffix'>";
            $str= JText::sprintf("SC_PRODUCT_NOT_PUBLISHED", $prodcode);
			$html .= $str;
			$html .="</div>";
			return $html;
		}

		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}

		$html  ="<div class='horseclasstable'>";
		$html  .="<table>";


		$prodpresentation="\n<tr>";

		if ($pcfg->showproductcodetext) $prodpresentation .= "\n<div class='scproduct$classsuffix'>#PRODUCTCODETEXT# $this->separator</div>";
		if ($pcfg->showproductcode) $prodpresentation.="\n<td><div class='scprodcode$classsuffix'>#PRODUCTCODE# $this->separator</div></td>";
		if ($pcfg->showtitle) $prodpresentation.="\n<td><div class='scshorttext$classsuffix'>#SHORTTEXT# $this->separator</div></td>";
		if ($pcfg->showunitpricetext) $prodpresentation.="\n<div class='scunitpricetext$classsuffix'>#UNITPRICETEXT# $this->separator</div>";
		if ($pcfg->showunitprice) $prodpresentation.="\n<div class='scunitprice$classsuffix'>#UNITPRICE# $this->separator</div>";
		if ($pcfg->showquantitytext) $prodpresentation.="\n<div class='scqtytext$classsuffix'>#QUANTITYTEXT# $this->separator</div>";

		$prodpresentation.="\n<div class='scqty$classsuffix'>#QUANTITY# $this->separator</div>";

		if ($pcfg->showavailablequantitytext) $prodpresentation.="\n<div class='scavqtytext$classsuffix'>#AVQUANTITYTEXT# $this->separator</div>";
		if ($pcfg->showavailablequantity) $prodpresentation.="\n<div class='scavqty$classsuffix'>#AVQUANTITY# $this->separator</div>";

		$prodpresentation.="\n#CARTOPTIONS#";

		$prodpresentation.="\n<div class='atczone$classsuffix'>#ADDTOCARTBUTTON#</div>";


		$amount = number_format($product->unitprice, $this->decs, $this->dsep, $this->tsep);
		if ($this->curralign==1) {
			$amount = $this->currency ."&nbsp;". $amount;
		}
		else
		{
			$amount = $amount ."&nbsp;". $this->currency;
	}

		$html .= $prodpresentation;

		$html  ="\n<div class='sccart$classsuffix'>";
		$html .="\n<form name='addtocart$product->id' action='index.php' method='post'>";


        $productoptions=new productoption();
        $optionhtml ="<div class='cartoptions$classsuffix'>";
        if ($pcfg->showcartoptionstitle) $optionhtml.="\n<div class='cartoptionstitle$classsuffix'>#CARTOPTIONSTITLE#</div>";
        $hasoptions=0;
        $optgroups=new optiongroups();
        $optiongroups=$optgroups->getgroups($product->prodcode);
            $id=md5($product->prodcode.$picname);
        if (count($optiongroups)) {
            foreach ($optiongroups as $optiongroup) {
                $groupid=$optiongroup->id;
                $options=new productoption();
                $lstoptions=$options->getbygroup($groupid);
                $show="optionshow".$optiongroup->showas;
                if ($pcfg->showcartoptionstitle) $optionhtml.="\n<div class='cartoptionstitle$classsuffix'>{$optiongroup->title}</div>";
                $optionhtml.= $this->$show($lstoptions, $product, $groupid, $classsuffix, $picname);
            }
        }
        else
        { // product without options - generate id from productcode+picname
            $optionhtml .= "\n<input type='hidden' value='$id' name='edtoption' />";
        }
		$optionhtml .="</div>";

		if ( $checkoos==1 ) { // check for minimum quantitites/ out of stock
			if ($product->av_qty>=1) { // product still available
				$atcbtn ="\n<input type='submit' name='submit' value='".JText::_('SC_ADD_TO_CART')."' class='scp_atc$classsuffix' />";
			}
			else
			{ // product quantity is 0
				$atcbtn ="\n<input type='submit' name='submit' value='".JText::_('SC_OUT_OF_STOCK')."' class='scp_atc$classsuffix' disabled='disabled' />";

			}
		}
		else
		{
			$atcbtn ="\n<input type='submit' name='submit' value='".JText::_('SC_ADD_TO_CART')."' class='scp_atc$classsuffix' />";
		}

		$qtyfield="\n<input type='". ($pcfg->showquantity?'text':'hidden') ."' name='edtqty' value='$defaultqty' class='scp_qty$classsuffix' />";
		if ($strqties) { // specific quantities given
			$qtyfield="<select name='edtqty' class='scp_selectqty$classsuffix'>";
			foreach ($aqties as $key=>$value) {
				$qtyfield .= "<option value='$value' ".($value==$defaultqty?" selected":"").">$value</option>";
			}
			$qtyfield .= "</select>";
		}
		$avqtytext = JText::_("SC_AVAILABLE_QTY");
		$avqty = $product->av_qty;

		$html .="\n<input type='hidden' name='edtprodcode' value='$product->prodcode' />";
		$html .="\n<input type='hidden' name='edtshorttext' value='$product->shorttext' />";
		$html .="\n<input type='hidden' name='edtunitprice' value='$product->unitprice' />";
		$html .="\n<input type='hidden' name='option' value='com_simplecaddy' />";
		$html .="\n<input type='hidden' name='action' value='addtocart' />";
		$html .="\n<input type='hidden' name='picname' value='$picname' />";
		$html .="\n<input type='hidden' name='nextcid' value='$nextcid' />";
		$html .="\n<input type='hidden' name='Itemid' value='$Itemid' />";
		$html .="\n<input type='hidden' name='thiscid' value='$this->thiscid' />";
		if ($minqty>0) { // check for minimum quantity in the component
			$html .="\n<input type='hidden' name='minqty' value='$minqty' />";

		}
		$html .="\n</form>";
		$html .="</div>";

	//now replace any variables
		$html=str_replace("#PRODUCTCODETEXT#", JText::_('SC_PRODUCT'), $html);
		$html=str_replace("#PRODUCTCODE#", $product->prodcode, $html);
		$html=str_replace("#SHORTTEXT#", stripslashes( $product->shorttext ), $html);
		$html=str_replace("#UNITPRICETEXT#", JText::_('SC_PRICE_PER_UNIT'), $html);
		$html=str_replace("#UNITPRICE#", $amount, $html);
		$html=str_replace("#QUANTITY#", $qtyfield, $html);
		$html=str_replace("#QUANTITYTEXT#", JText::_('SC_QUANTITY'), $html);
		$html=str_replace("#ADDTOCARTBUTTON#", $atcbtn, $html);
		$html=str_replace("#CARTOPTIONS#", $optionhtml, $html);
		$html=str_replace("#AVQUANTITY#", $avqty, $html);
		$html=str_replace("#AVQUANTITYTEXT#", $avqtytext, $html);

		if (trim($product->options)) {
			$html=str_replace("#CARTOPTIONSTITLE#", JText::_('SC_OPTIONS'), $html);
		}
		else
		{
			$html=str_replace("#CARTOPTIONSTITLE#", "", $html);
		}

		return $html;
	}

	function getSCbyCategory($params) {
		global $Itemid, $mainframe;
		$category=$params['category'];
		$defaultqty=isset($params['defqty']) ? $params['defqty'] : 1;
		$classsuffix=isset($params['classsfx']) ? $params['classsfx'] : "";
		$nextcid=isset($params['nextcid']) ? $params['nextcid'] : $this->thiscid;

		$pcfg=new simplecaddyconfiguration();
		$cart2=new cart2();

		$db	= JFactory::getDBO();
		$query="SELECT * FROM `#__sc_products` WHERE LCASE(`category`)='$category' and `published`=1 ORDER BY `prodcode`";
		$db->setQuery( $query );
		$lstproduct = $db->loadObjectList();

		if (!$lstproduct) {
			$html  ="<div class='sccart$classsuffix'>";
			$html .="<h3>The category ($category) is not found.</h3>";
			$html .="</div>";
			return $html;
		}

		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$html  ="<div class='horseclasstable'>";
		$html  .="<table>";
//		$html ="";
		foreach ($lstproduct as $product) {
			$prodpresentation="\n<tr class='sccart$classsuffix'>";
//			$html .="\n<form name='addtocart$product->id' action='index.php' method='post'>";
//			$prodpresentation="\n<div class='sccart$classsuffix'>";

			if ($pcfg->showproductcodetext) $prodpresentation .= "\n<td><div class='scproduct$classsuffix'>#PRODUCTCODETEXT# $this->separator</div></td>";
			if ($pcfg->showproductcode) $prodpresentation.="\n<td><div class='scprodcode$classsuffix'>#PRODUCTCODE# $this->separator</div></td>";
			if ($pcfg->showtitle) $prodpresentation.="\n<td><div class='scshorttext$classsuffix'>#SHORTTEXT# $this->separator</div></td>";
			if ($pcfg->showunitpricetext) $prodpresentation.="\n<div class='scunitpricetext$classsuffix'>#UNITPRICETEXT# $this->separator</div>";
			if ($pcfg->showunitprice) $prodpresentation.="\n<td><div class='scunitprice$classsuffix'>#UNITPRICE# $this->separator</div></td>";
			if ($pcfg->showquantitytext) $prodpresentation.="\n<div class='scqtytext$classsuffix'>#QUANTITYTEXT# $this->separator</div>";
//			$prodpresentation.="\n<div class='scqty$classsuffix'>#QUANTITY# $this->separator</div>";

			if ($pcfg->showavailablequantitytext) $prodpresentation.="\n<div class='scavqtytext$classsuffix'>#AVQUANTITYTEXT# $this->separator</div>";
			if ($pcfg->showavailablequantity) $prodpresentation.="\n<div class='scavqty$classsuffix'>#AVQUANTITY# $this->separator</div>";


//			$prodpresentation.="\n#CARTOPTIONS#";

	        $productoptions=new productoption();

	        $optionhtml ="<div class='cartoptions$classsuffix'>";
	        if ($pcfg->showcartoptionstitle) $optionhtml.="\n<div class='cartoptionstitle$classsuffix'>#CARTOPTIONSTITLE#</div>";

	        $optgroups=new optiongroups();
	        $optiongroups=$optgroups->getgroups($product->prodcode);
	            @$id=md5($product->prodcode.$picname);
	        if (count($optiongroups)) {
	            foreach ($optiongroups as $optiongroup) {
	                $groupid=$optiongroup->id;
	                $options=new productoption();
	                $lstoptions=$options->getbygroup($groupid);
	                $show="optionshow".$optiongroup->showas;
	                if ($pcfg->showcartoptionstitle) $optionhtml.="\n<div class='cartoptionstitle$classsuffix'>{$optiongroup->title}</div>";
	                $optionhtml.= $this->$show($lstoptions, $product, $groupid, $classsuffix, "");
	            }
	        }
	        else
	        { // product without options - generate id from productcode
	            $optionhtml .= "\n<input type='hidden' value='$id' name='edtoption' />";
	        }
			$optionhtml .="</div>";

			$amount = number_format($product->unitprice, $this->decs, $this->dsep, $this->tsep);
			if ($this->curralign==1) {
				$amount = $this->currency ."&nbsp;". $amount;
			}
			else
			{
				$amount = $amount ."&nbsp;". $this->currency;
			}
			$prodpresentation.="\n<td><form name='addtocart$product->id' action='index.php' method='post'>";
			$prodpresentation.="\n<div class='atczone$classsuffix'>#ADDTOCARTBUTTON#";
			$prodpresentation.="\n#CARTOPTIONS#";

			$html .= $prodpresentation;

//			if ($pcfg->showcartoptionstitle) $optionhtml.="\n<div class='cartoptions$classsuffix'>#CARTOPTIONSTITLE#</div>";

//			if ($product->av_qty>=1) {
			if (!$cart2->isInCart($product->prodcode)) {

				if ($product->av_qty>=1) {
					$atcbtn ="\n<input type='submit' name='submit' value='".JText::_('SC_ADD_TO_CART')."' class='scbutton$classsuffix' />";
	    			$html .="\n<input type='hidden' name='action' value='addtocart' />";
					$html .="\n<input type='hidden' name='edtqty' value='$defaultqty' />";
				}
				else
				{
					$atcbtn ="\n<input type='submit' name='submit' value='".JText::_('SC_OUT_OF_STOCK')."' class='scbutton$classsuffix' disabled='disabled' />";
					$html .="\n<input type='hidden' name='action' value='changeqty' />";
					$html .="\n<input type='hidden' name='edtqty' value='0' />";
					$html .= "\n<input type='hidden' name='edtid' value='$product->prodcode$id' />";
				}

			}
			else
			{
				$atcbtn ="\n<input type='submit' name='submit' value='".JText::_('SC_REMOVE')."' class='scbutton$classsuffix' />";
				$html .="\n<input type='hidden' name='action' value='changeqty' />";
				$html .="\n<input type='hidden' name='edtqty' value='0' />";
				$html .= "\n<input type='hidden' name='edtid' value='$product->prodcode$id' />";
			}
//			$qtyfield="\n<input type='". ($pcfg->showquantity?'text':'hidden') ."' name='edtqty' value='$defaultqty' class='scp_qty$classsuffix' />";
//			$html .="\n<input type='hidden' name='edtqty' value='$defaultqty' />";
			$html .="\n<input type='hidden' name='edtprodcode' value='$product->prodcode' />";
			$html .="\n<input type='hidden' name='edtshorttext' value='$product->shorttext' />";
			$html .="\n<input type='hidden' name='edtunitprice' value='$product->unitprice' />";
			$html .="\n<input type='hidden' name='option' value='com_simplecaddy' />";
			$html .="\n<input type='hidden' name='Itemid' value='$Itemid' />";
			$html .="\n<input type='hidden' name='thiscid' value='$this->thiscid' />";
			$html .="\n<input type='hidden' name='nextcid' value='$nextcid' />";
			$html .="</div>";
			$html .="\n</form></td>";
//			$html .="</div>";
			$html .="</tr>";

			$avqtytext = JText::_("SC_AVAILABLE_QTY");
			$avqty = $product->av_qty;

		//now replace any variables
			$html=str_replace("#PRODUCTCODETEXT#", JText::_('SC_PRODUCT'), $html);
			$html=str_replace("#PRODUCTCODE#", $product->prodcode, $html);
			$html=str_replace("#SHORTTEXT#", stripslashes( $product->shorttext ), $html);
			$html=str_replace("#UNITPRICETEXT#", JText::_('SC_PRICE_PER_UNIT'), $html);
			$html=str_replace("#UNITPRICE#", $amount, $html);
			$html=str_replace("#QUANTITY#", $qtyfield, $html);
			$html=str_replace("#QUANTITYTEXT#", JText::_('SC_QUANTITY'), $html);
			$html=str_replace("#ADDTOCARTBUTTON#", $atcbtn, $html);
			$html=str_replace("#CARTOPTIONS#", $optionhtml, $html);
			$html=str_replace("#AVQUANTITY#", $avqty, $html);
			$html=str_replace("#AVQUANTITYTEXT#", $avqtytext, $html);
			if (trim($product->options)) {
				$html=str_replace("#CARTOPTIONSTITLE#", stripslashes($product->optionstitle), $html);
			}
			else
			{
				$html=str_replace("#CARTOPTIONSTITLE#", "", $html);
			}
		}
		$html.="</table></div>";
		return $html;
	}

	function editCart($params) {
	    $mainframe=JFactory::getApplication();
	    $thiscid=$this->thiscid; // article ID, used to stay on page
		$nextcid=isset($params['nextcid']) ? $params['nextcid'] : $thiscid; // next page to go to, defaults to stay on this page

		$pcfg=new simplecaddyconfiguration();

		$cart2=new cart2();
		$cart=$cart2->readCart();

		$showhidden="hidden";
		$line=__LINE__;
		$cfg=new sc_configuration();
		$tsep=$cfg->get('thousand_sep');
		$dsep=$cfg->get('decimal_sep');
		$decs=$cfg->get('decimals');
		$currency=$cfg->get('currency');
		$curralign=$cfg->get('curralign');
		$showremove=$cfg->get('remove_button');
		$show_emptycart=$cfg->get('show_emptycart');
		$currency=$cfg->get('currency');
		$currleftalign=$cfg->get('curralign');
		$stdprodcode=$cfg->get("cart_fee_product");
		$shipping=$cfg->get("shippingenabled");

		$gtotal=0;
		$html ="";
	// some templates have problems with div tags only as content
	// enable this line and the corresponding line at the end of this function to
	// accommodate these templates. It will then become invalid xhtml, though
	//		$html  .= "<table><tr><td>";
		$html .= "\n<div class='horseclasstable'>";
		$html .= "\n<table>";
		$html .= "\n<tr>";
		$html .= "\n<td colspan=5><div class='cartbanner'>My Entries</div></td>";
		$html .= "\n</tr>";
		$html .= "\n<tr>";
//			$html .= "\n<div class='sc_cart'>";
//			$html .= "\n<div class='cartheading'>\n<div class='code_col'>".JText::_('SC_CODE')."</div>\n<div class='desc_col'>".JText::_('SC_DESCRIPTION')."</div>\n<div class='price_col'>".JText::_('SC_PRICE_PER_UNIT')."</div>\n<div class='qty_col'>".JText::_('SC_QUANTITY')."</div>\n<div class='total_col'>".JText::_('SC_TOTAL')."</div>\n<div class='actions_col'>&nbsp;</div></div>";
		$html .= "\n<td >".JText::_('SC_CODE')."</td>\n<td >".JText::_('SC_DESCRIPTION')."</td>\n<td>".JText::_('SC_PRICE_PER_UNIT')."</td>\n<td >&nbsp;</td>";
		$html .= "\n</tr>";
		$emptycart=true;

			if (!is_array($cart)) $cart=array();
			foreach ($cart as $key=>$cartproduct) {
				$formname=uniqid("Z");
//				$html2 = "<form name='$formname' method='post'>";
				$html2 = "\n<tr>";
				$html2 .= "\n<td >$cartproduct->prodcode</td>";
				$html2 .= "\n<td >".urldecode($cartproduct->prodname)." - ".urldecode($cartproduct->option)."</td>";

				$pu = number_format($cartproduct->finalprice, $decs, $dsep, $tsep);
				if ($currleftalign==1) {
					$html2 .= "\n<td>$currency&nbsp;".$pu."</td>";
				}
				else
				{
					$html2 .= "\n<td>$pu&nbsp;$currency</td>";
				}
//				$html2 .= "\n<div class='qty_col'>";
//				$html2 .=  "\n<input type='text' name='edtqty' value='".$cartproduct->quantity."' class='sc_edtqty'>";
//				$html2 .= "</div>";
//				$html2 .= "\n<td >";

				$total=$cartproduct->quantity*$cartproduct->finalprice;
				$nombre_format = number_format($total, $decs, $dsep, $tsep);
				$gtotal= $gtotal + $total;
//				if ($currleftalign==1) {
//					$html2 .= "$currency&nbsp;".$nombre_format;
//				}
//				else
//				{
//					$html2 .= $nombre_format."&nbsp;$currency";
//				}
//				@$html2 .="\n<input type='$showhidden' name='productid' value='$cartproduct->id'>";
//				$html2 .= "</td>";
//				if ($cartproduct->prodcode!=$stdprodcode and ($cartproduct->prodcode!="voucher")) {
//					$html2 .= "\n<input type='button' name='btnsubmit' value='".JText::_('SC_CHANGE')."' class='btnchange' onclick='javascript:document.$formname.nextcid.value=\"$thiscid\";document.$formname.submit()' />";
//				}
//				else
//				{
//					$html2 .= "&nbsp;";
//				}

				$html2 .= "\n<td>";
				$html2 .= "<form name='$formname' method='post'>";
				if ($showremove==1 and ($cartproduct->prodcode!=$stdprodcode) and ($cartproduct->prodcode!="voucher")) {
					$html2 .= "\n<input type='button' name='btnremove' value='".JText::_('SC_REMOVE')."' class='btnremove' onclick='javascript:document.$formname.edtqty.value=0;javascript:document.$formname.nextcid.value=\"$thiscid\";javascript:document.$formname.submit()' />";
				}
				$html2 .= "\n<input type='hidden' name='productid' value='$cartproduct->id'>";
				$html2 .= "\n<input type='hidden' name='option' value='com_simplecaddy' />";
				$html2 .= "\n<input type='hidden' name='action' value='changeqty' />";
				$html2 .= "\n<input type='hidden' name='edtqty' value='".$cartproduct->quantity."' class='sc_edtqty'>";
				$html2 .= "\n<input type='hidden' name='nextcid' value='$nextcid' />";
				$html2 .= "\n<input type='hidden' name='thiscid' value='$thiscid' />";
				$html2 .= "\n<input type='hidden' name='edtid' value='".$cartproduct->md5id."' />";
				$html2 .= "\n<input type='hidden' name='edtprodcode' value='".$cartproduct->prodcode."' />";
				$html2 .= "\n<input type='hidden' name='edtunitprice' value='".$cartproduct->unitprice."' />";
				$html2 .= "\n<input type='hidden' name='edtshorttext' value='".$cartproduct->prodname."' />";
				$html2 .= "\n<input type='hidden' name='edtoption' value='".$cartproduct->option."' />";
				$html2 .= "</form>";
				$html2 .= "</td></tr>";
				if ($cartproduct->quantity) {
					$html .= $html2; // only add to display when qty != zero !
					$emptycart=false;
				}
			}

			$html .= "<tr>";
			$html .= "<td colspan='2' >".JText::_('SC_TOTAL')."</td>";
			if ($currleftalign==1) {
				$html .= "\n<td >$currency&nbsp;".number_format($gtotal, $decs, $dsep, $tsep)."</td>";
			}
			else
			{
				$html .= "\n<td >".number_format($gtotal, $decs, $dsep, $tsep)."&nbsp;$currency</td>";
			}
			$html .="<td>&nbsp;</td>";
			$html .= "</tr>";
			$html .= "<tr><td colspan='4'>";

			$html .= "<form name='checkout{$this->_plugin_number}' method='post'>";
			$html .= "\n<div class='cartactions'>";

			$html .= "\n<input type='hidden' name='option' value='com_simplecaddy'>";
			if ($show_emptycart==1) {
				$html .= "\n<input type='button' name='btnemptycart' value='".JText::_('SC_EMPTY_CART')."' class='btnemptycart' onclick='javascript:document.checkout{$this->_plugin_number}.action.value=\"emptycart\";javascript:document.checkout{$this->_plugin_number}.nextcid.value=\"$this->thiscid\";javascript:document.checkout{$this->_plugin_number}.submit()'>";
			}

			$html .= "\n<input type='hidden' name='action' value='checkout'>";
			$html .= "\n<input type='hidden' name='nextcid' value='$nextcid'>";
			$html .= "\n<input type='hidden' name='thiscid' value='$thiscid' />";
			$html .= "\n<input class='btnorder' type='button' value='".JText::_('SC_ORDER')."' onclick='javascript:document.checkout{$this->_plugin_number}.submit()'>";

			$html .="</div>";
			$html .= "</form>";
	//		if ($emptycart) {
	//			$html ="<div>";
	//			$html .=JText::_('SC_CART_EMPTY');
	//		}
	//		$html .= "</div><div id='debug'></div>";
			$html .= "</td></tr></table>";
			return $html;
	}

	function showCart($params) {
	    $mainframe=JFactory::getApplication();
	    $thiscid=$this->thiscid; // article ID, used to stay on page
		$nextcid=isset($params['nextcid']) ? $params['nextcid'] : $thiscid; // next page to go to, defaults to stay on this page
		$showbuttons=isset($params['showbuttons']) ? $params['showbuttons'] : 0; // show the buttons in the bottom of the read only cart
		$cart2=new cart2();
		$cart=$cart2->readCart();
		if (!is_array($cart)) $cart=array();

		$showhidden="hidden";
		$cfg=new sc_configuration();
		$tsep=$cfg->get('thousand_sep');
		$dsep=$cfg->get('decimal_sep');
		$decs=$cfg->get('decimals');
		$currency=$cfg->get('currency');
		$curralign=$cfg->get('curralign');
		$showremove=$cfg->get('remove_button');
		$show_emptycart=$cfg->get('show_emptycart');
		$currency=$cfg->get('currency');
		$currleftalign=$cfg->get('curralign');

		$gtotal=0;
		$html ="";
	// some templates have problems with div tags only as content
	// enable this line and the corresponding line at the end of this function to
	// accommodate these templates. It will then become invalid xhtml, though
	//		$html  .= "<table><tr><td>";
		$html .= "\n<div class='horseclasstable'>";
		$html .= "\n<table>";
		$html .= "\n<tr>";
		$html .= "\n<td colspan=5><div class='cartbanner'>My Entries</div></td>";
		$html .= "\n</tr>";
		$html .= "\n<tr>";
//		$html .= "\n<div class='cartheading'>\n<div class='code_col'>".JText::_('SC_CODE')."</div>\n<div class='desc_col'>".JText::_('SC_DESCRIPTION')."</div>\n<div class='price_col'>".JText::_('SC_PRICE_PER_UNIT')."</div>\n<div class='qty_col'>".JText::_('SC_QUANTITY')."</div>\n<div class='total_col'>".JText::_('SC_TOTAL')."</div>\n<div class='actions_col'>&nbsp;</div></div>";
		$html .= "\n<td >".JText::_('SC_CODE')."</td>\n<td >".JText::_('SC_DESCRIPTION')."</td>\n<td >".JText::_('SC_PRICE_PER_UNIT')."</td>\n<td >".JText::_('SC_QUANTITY')."</td>\n<td >".JText::_('SC_TOTAL')."</td>";
		$html .= "\n</tr>";
		$emptycart=true;
		$i=0;
		foreach ($cart as $key=>$cartproduct) {
			$html2=""; // prepare inner html of the cart display
			$html2 .="\n<tr >";
			$i++;

			$html2 .= "\n<td >$cartproduct->prodcode</td>";
			$html2 .= "\n<td >".urldecode($cartproduct->prodname)." - ".urldecode($cartproduct->option)."</td>";

			$pu = number_format( (float)$cartproduct->finalprice, $decs, $dsep, $tsep);
			if ($currleftalign==1) {
				$html2 .= "\n<td >$currency&nbsp;".$pu."</td>";
			}
			else
			{
				$html2 .= "\n<td >$pu&nbsp;$currency</td>";
			}
			$html2 .= "\n<td >".$cartproduct->quantity."</td>";
			$html2 .= "\n<td >";

			$total=$cartproduct->quantity*$cartproduct->finalprice;
			$nombre_format = number_format($total, $decs, $dsep, $tsep);
			$gtotal= $gtotal + $total;
			if ($currleftalign==1) {
				$html2 .= "$currency&nbsp;".$nombre_format;
			}
			else
			{
				$html2 .= $nombre_format."&nbsp;$currency";
			}
			$html2 .= "</td>";
//			$html2 .= "</div>\n<div class='actions_col'>";
//			$html2 .= "</div>";
			$html2 .= "</tr>";
			if ($cartproduct->quantity) {
				$html .= $html2; // only add to display when qty != zero !
				$emptycart=false;
			}
		}

			$html .= "<tr >";
//			$html .= "<div class='fill_col'>";
			$html .= "<td colspan=4 >".JText::_('SC_TOTAL')."</td>";
			if ($currleftalign==1) {
				$html .= "\n<td >$currency&nbsp;".number_format($gtotal, $decs, $dsep, $tsep)."</td>";
			}
			else
			{
				$html .= "\n<td >".number_format($gtotal, $decs, $dsep, $tsep)."&nbsp;$currency</td>";
			}
//			$html .= "</div>";
			$html .= "</tr>";
			$html .= "\n<tr>";
			$html .= "\n<td colspan=5>";

			if ($showbuttons==1 && !$emptycart) {
				$html .= "<form name='checkout{$this->_plugin_number}' method='post'>";
				$html .= "\n<div class='cartactions'>";
				$html .= "\n<input type='hidden' name='option' value='com_simplecaddy'>";
				if ($show_emptycart==1) {
					$html .= "\n<input type='button' name='btnemptycart' value='".JText::_('SC_EMPTY_CART')."' class='btnemptycart' onclick='javascript:document.checkout{$this->_plugin_number}.action.value=\"emptycart\";javascript:document.checkout{$this->_plugin_number}.nextcid.value=\"$this->thiscid\";javascript:document.checkout{$this->_plugin_number}.submit()'>";
				}
				$html .= "\n<input type='hidden' name='action' value='checkout'>";
				$html .= "\n<input type='hidden' name='nextcid' value='$nextcid'>";
				$html .= "\n<input type='hidden' name='thiscid' value='$thiscid' />";
				$html .= "\n<input class='btnorder' type='button' value='".JText::_('SC_ORDER')."' onclick='javascript:document.checkout{$this->_plugin_number}.submit()'>";
				$html .="</div>";
				$html .= "</form>";
			}
//			$html .="</div>";
			$html .="</tr>";
			$html .="</td>";
			$html .="</table>";

//			if ($emptycart) {
//				$html ="<div>";
//				$html .=JText::_('SC_CART_EMPTY');
//				$html .= "</div>";
//			}
//			$html .= "<div id='debug'></div>";
			return $html;
	}


	function emailorder($params) {
		$ordercode=JRequest::getVar("data"); // the data contains the ordercode when you finish the details page
		if (@isset($params['usecontent'])) {
			$usecontent=$params['usecontent'];
		}
		$orders=new orders();
		$orderid=$orders->getOrderIdFromCart($ordercode);
		$mail=new email();
		$result=$mail->mailorder($orderid, $usecontent); // should be 1 for successful email
	}

	function displayorder($params) {
		$ordercode=JRequest::getVar("data"); // the data contains the ordercode when you finish the details page
		if(!$ordercode) {
			$ordercode=JRequest::getVar("dlkey"); // if you use this on a pay success url then the value is in &dlkey
		}
		if (@isset($params['usecontent'])) {
			$usecontent=$params['usecontent'];
		}
		$orders=new orders();
		$orderid=$orders->getOrderIdFromCart($ordercode);
		$single = new order();
		$single->load($orderid);
		$html = $single->getOrderPresentationHTML($usecontent);
		return $html;
	}

	function listorders($params) {

		if (@isset($params['filter'])) {
			$filter=$params['filter'];
		}


		if (@isset($params['usecontent'])) {
			$usecontent=$params['usecontent'];
			$usecontentasitem=1;
		}

		if (@isset($params['nextcid'])) {
			$nextcid=$params['nextcid'];
		} else {
			$nextcid=thiscid;
		}


        $cfg=new sc_configuration();
		$dateformat=$cfg->get('dateformat');
		$timeformat=$cfg->get('timeformat');

		$db	= JFactory::getDBO();
		$query="select * from `#__sc_orders` where `archive`=0 ";

		if ($filter) {
				$query .= " and status = '$filter' ";
		}

		$db->setQuery($query);
		$list=$db->loadObjectList();


		if ($db->getErrorNum()) {
			echo $db->getErrorMsg();
			echo $db->getQuery();
		}


		$user=JFactory::getUser(); // get the Joomla logged in user
		$sp=new sccontent();
		$sp->load($nextcid);
		$url =JUri::root(). ContentHelperRoute::getArticleRoute($sp->id. ":". $sp->alias, $sp->catid) ;
//		$url ="admin-update-details.html" ;


		$fields=new fields();
		$fieldslist=$fields->getPublishedFields() ;// the custom fields defined for this system


		$hhtml = "<tr>";
		$hhtml .= "<td>Order Number [#orderid#]</td>";
		$hhtml .= "<td>Ordered On : #orderdt# </td>";
		$hhtml .= "<td>Ordered By :#name# </td>";
		$hhtml .= "<td>Address Details";
		$hhtml .= "\n<br />#address1#";
		$hhtml .= "\n<br />#address2#";
		$hhtml .= "\n<br />#city#";
		$hhtml .= "\n<br />#postal_code# </td>";
		$hhtml .= "<td>Phone : #phone#";
		$hhtml .= "\n<br />Email : #email# </td>";
		$hhtml .= "<td>PayPal code: #ppref#</td>";
		$hhtml .= "<td>Order Status: #status#</td>";
		$hhtml .= "<td>Order Code: #ordercode#</td></tr>";


		if ($usecontentasitem==1) {
			$db	= JFactory::getDBO();
			$query="select introtext from #__content where id = '$usecontent'";
			$db->setQuery($query);
			$content=$db->loadResult();
		}
		else
		{
			$content=$hhtml;
		}

		$html = ""; // header html

//		$html  ="<div class='horseclasstable'>";
//		$html  .="<table>";

		foreach ($list as $order) {

			if ($user->id) {
				if (strpos($url, "?")>0) {
					$url.= "&data=".$order->ordercode;
				}
				else
				{
					$url.= "?data=".$order->ordercode;
				}
				$link="<a href='$url'>".$order->id."</a>";

			} else {
				$link="";
			}

			$newrow=$content;


			$thefields=unserialize($order->customfields); // the fields filled by customers

			if ($thefields['website']== ""){
				$url2="";
			} else {
				$url2="<a href='".$thefields['website']."'>".$thefields['website']."</a>";
			}

			foreach ($fieldslist as $key=>$customfield) {
				$thefields[$customfield->name]=str_replace("\'","'",$thefields[$customfield->name]);
				$newrow=str_replace("#".$customfield->name."#", $thefields[$customfield->name], $newrow); // replace custom tags with the field names
			}

			$newrow=str_replace("#orderid#",$order->id, $newrow); // replace orderid tag with the order ID
			$newrow=str_replace("#status#",$order->status, $newrow); // replace orderid tag with the order ID
			$newrow=str_replace("#ppref#",$order->paymentcode, $newrow); // replace orderid tag with the order ID
			$newrow=str_replace("#ordercode#",$order->ordercode, $newrow); // replace orderid tag with the order ID
			$newrow=str_replace("#orderdt#",date("$dateformat $timeformat", $order->orderdt), $newrow); // replace orderid tag with the order ID
			$newrow=str_replace("#link#",$link, $newrow); // replace link tag with the order link URL if present
			$newrow=str_replace("#url#",$url2, $newrow); // replace url tag with the order link URL if present

			$html.=$newrow;

		}
//		$html  .="</table>";
//		$html  .="</div>";

		return $html;
	}


	function editdetails($params) { //$formfields, $errormessage=null, $fielddata=array()
	    $thiscid=$this->thiscid; // article ID, used to stay on page
		$nextcid=isset($params['nextcid']) ? $params['nextcid'] : $thiscid; // next page to go to, defaults to stay on this page

		$ordercode=JRequest::getVar("data");
		$orders=new orders();
		$orderid=$orders->getOrderIdFromCart($ordercode);
		$single = new order();
		$single->load($orderid);

		JHTML::script("components/com_simplecaddy/js/datetimepicker.js");
		$mainframe=JFactory::getApplication();

		$html  = "<form name='frmdetails' method='post'>";
		$html .= "<table width='100%' class='sc_details' cellpadding='0' cellspacing='0'>";

        $cfg = new sc_configuration();
    	$statuses=explode("\n", $cfg->get("ostatus"));

		$fields=new fields();
		$formfields=$fields->getPublishedFields();

		$fielddata=unserialize($single->customfields); // the fields filled by customers


  		$n=count($formfields);
		$first=true;
		foreach ($formfields as $field) {
			@$fielddata["$field->name"]=str_replace("\'","'",@$fielddata["$field->name"]);
			switch($field->type) {
				case "divider": // simple line with text, no fields
					$html .= "<tr class='$field->classname'><td colspan='2'>".JText::_("$field->caption");
					break;
				case "text": // textbox field, single line
					$html .= "<tr><td>".JText::_("$field->caption")."</td><td>";
					$html .= "<input type='text' name='$field->name' size='$field->length' class='$field->classname' value=\"". @$fielddata["$field->name"]."\">";
					break;
				case "textarea": // multiline textbox/textarea, no wysiwyg editor
					$html .= "<tr><td>".JText::_("$field->caption")."</td><td>";
					@list($cols, $rows)=explode(",", $field->length);
					$html .= "<textarea name='$field->name' class='$field->classname' cols='$cols' rows='$rows'>". @$fielddata["$field->name"]."</textarea>";
					break;
				case "radio": // yes/no radio buttons
					$html .= "<tr><td>".JText::_("$field->caption")."</td><td>";
					$html .= "<input type='radio' name='$field->name' class='$field->classname' value='yes' ". (@$fielddata["$field->name"]=="yes"?"checked":"").">". JText::_('JYES');
					$html .= "<input type='radio' name='$field->name' class='$field->classname' value='no' ". (@$fielddata["$field->name"]=="no"?"checked":"").">". JText::_('JNO');
					break;
				case "checkbox": // single checkbox
					$html .= "<tr><td>".JText::_("$field->caption")."</td><td>";
					$html .= "<input type='checkbox' name='$field->name' class='$field->classname' value='yes' ". (@$fielddata["$field->name"]=="yes"?"checked":"").">". JText::_('JYES');
					break;
				case "date": // textfield with calendar javascript
					$html .= "<tr><td>".JText::_("$field->caption")."</td><td>";
					$html .= "<input type='text' name='$field->name' id='$field->name' size='$field->length' class='$field->classname' value='". @$fielddata["$field->name"]."'>";
					$html .= "&nbsp;<a href=\"javascript:NewCal('$field->name','ddMMyyyy',true ,24)\"><img src=\"components/com_simplecaddy/images/cal.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".JText::_("SC_PICK_DATE")."\"/></a>";
					break;
				case "dropdown": // dropdown list, single selection
					$html .= "<tr><td>".JText::_("$field->caption")."</td><td>";
					$html .= "<select name='$field->name' id='$field->name' class='$field->classname'>";
					$aoptions=explode(";", $field->fieldcontents);
					foreach ($aoptions as $key=>$value) {
						$html .= "<option value='$value'".(@$fielddata["$field->name"]=="$value"?" selected":"").">$value</option>";
					}
					$html .= "</select>";
					break;
			}
			$html .= $field->required ? "<span class='reqfield'>".JText::_('SC_REQUIRED')."</span>" : "";
			$html .= "";
			$html .= "</td>";
//			if ($first) {
//				$html .= "<td rowspan='$n'><div class='checkoutright'>&nbsp;</div></td>";
//				$first=false;
//			}
			$html .= "</tr>";
		}
		$html.="<tr>	<td>".JText::_('SC_ORDER_STATUS')."</td>";
		$html.="\n<td>";
		$html.="<select name='edtostatus'>";
				foreach ($statuses as $status) {
					$selected=(strtolower($single->status)==strtolower(trim($status))?" selected":"");
					$html.="<option value='".trim($status)."' $selected>$status</option>\n";

				}
		$html.="</select>";
		$html.="</td></tr>";


		$html .= "<tr><td>&nbsp;</td><td><input class='sc_detailsbutton' type='submit' name='submit' value='". JText::_('SC_CONFIRM') ."'/></td>";
		$html .= "</tr>";

		$html .= "</table>";
		$html .= "<input type='hidden' name='ipaddress' value='". $_SERVER['REMOTE_ADDR'] ."'/>";
		$html .= "<input type='hidden' name='option' value='com_simplecaddy' />";
		$html .= "<input type='hidden' name='action' value='saveorder' />";
		$html .= "<input type='hidden' name='data' value='$ordercode' />";
		$html .= "<input type='hidden' name='oid' value='$single->id' />";
		$html .= "<input type='hidden' name='thiscid' value='$thiscid' />";
		$html .= "<input type='hidden' name='nextcid' value='$nextcid' />";
		$html .= "<input type='hidden' name='herkomst' value='simplecaddy' />";
		$html .= "</form>";
		return $html;
	}

	function emailbutton($params) {

		$ordercode=JRequest::getVar("data"); // the data contains the ordercode when you finish the details page
		if (@isset($params['usecontent'])) {
			$usecontent=$params['usecontent'];
		}
	    $thiscid=$this->thiscid; // article ID, used to stay on page
		$nextcid=isset($params['nextcid']) ? $params['nextcid'] : $thiscid; // next page to go to, defaults to stay on this page

		$orders=new orders();
		$orderid=$orders->getOrderIdFromCart($ordercode);
		$frmname=uniqid("frmButton");
		$btnname=JText::_('SC_SKIP');
		if (@isset($params["name"])) {
			$tmp=$params["name"];
			$str=new simplecaddyconfiguration();
			$labels=$str->skipstrings;
			$alabels=explode("\r\n", $labels);
			foreach ($alabels as $key=>$value) {
				list($bname, $blabel)=explode(":", $value);
				$buttonstrings[$bname]=$blabel;
			}
			if (@count($alabels)>0) {
				$btnname= $buttonstrings[$tmp];
			}

		}

		$html = "<form action='".JRoute::_('index.php')."' method='post' name='$frmname'>";
		$html .= "\n<input name='emailorder' type='submit' value='$btnname' />";
		$html .= "\n<input name='nextcid' type='hidden' value='$nextcid' />";
		$html .= "\n<input name='thiscid' type='hidden' value='$thiscid' />";
		$html .= "\n<input name='data' type='hidden' value='$ordercode' />";
		$html .= "\n<input name='action' type='hidden' value='email_order' />";
		$html .= "\n<input name='option' type='hidden' value='com_simplecaddy' />";
		$html .= "\n<input name='usecid' type='hidden' value='$usecontent' /></form>";

		return $html;
	}

	function orderbutton($params) {

		$ordercode=JRequest::getVar("data"); // the data contains the ordercode when you finish the details page
	    $thiscid=$this->thiscid; // article ID, used to stay on page
		$nextcid=isset($params['nextcid']) ? $params['nextcid'] : $thiscid; // next page to go to, defaults to stay on this page

		$frmname=uniqid("frmButton");
//		$btnname=JText::_('SC_GET_ORDER');
		$btnname="Lookup Order";
		if (@isset($params["name"])) {
			$tmp=$params["name"];
			$str=new simplecaddyconfiguration();
			$labels=$str->skipstrings;
			$alabels=explode("\r\n", $labels);
			foreach ($alabels as $key=>$value) {
				list($bname, $blabel)=explode(":", $value);
				$buttonstrings[$bname]=$blabel;
			}
			if (@count($alabels)>0) {
				$btnname= $buttonstrings[$tmp];
			}

		}

		$html = "<form action='".JRoute::_('index.php')."' method='post' name='$frmname'>";
		$html .= "\n<input name='getorder' type='submit' value='$btnname' />";
		$html .= "\n<input name='nextcid' type='hidden' value='$nextcid' />";
		$html .= "\n<input name='thiscid' type='hidden' value='$thiscid' />";
		$html .= "\n<input name='oid' type='text' />";
		$html .= "\n<input name='action' type='hidden' value='view_order' />";
		$html .= "\n<input name='option' type='hidden' value='com_simplecaddy' />";
		$html .= "\n<input name='usecid' type='hidden' value='$usecontent' /></form>";

		return $html;
	}


	function skip($params) {
	    $mainframe=JFactory::getApplication();
	    $thiscid=$this->thiscid; // article ID, used to stay on page
		$nextcid=isset($params['nextcid']) ? $params['nextcid'] : $thiscid; // next page to go to, defaults to stay on this page
		$ordercode=JRequest::getVar("data"); // the data contains the ordercode when you finish the details page
		$btnname=JText::_('SC_SKIP');
		if (@isset($params["name"])) {
			$tmp=$params["name"];
			$str=new simplecaddyconfiguration();
			$labels=$str->skipstrings;
			$alabels=explode("\r\n", $labels);
			foreach ($alabels as $key=>$value) {
				list($bname, $blabel)=explode(":", $value);
				$buttonstrings[$bname]=$blabel;
			}
			if (@count($alabels)>0) {
				$btnname= $buttonstrings[$tmp];
			}

		}
		$frmname=uniqid("frmSkip");
		$html  = "<form name='$frmname' method='post' action=".JRoute::_('index.php').">";
		$html .= "\n<input type='hidden' name='option' value='com_simplecaddy'>";
		$html .= "\n<input type='hidden' name='action' value='skip'>";
		$html .= "\n<input name='data' type='hidden' value='$ordercode' />";
		$html .= "\n<input name='id' type='hidden' value='$this->itemid' />";
		$html .= "\n<input type='hidden' name='nextcid' value='$nextcid'>";
		$html .= "\n<input type='button' class='btnorder' value='$btnname' onclick='javascript:document.$frmname.submit()'>";
		$html .= "</form>";
		return $html;
	}

	function emptycart($params) {
		$cart2=new cart2();
		$cart2->destroyCart();
		return "";
	}

	function details($params) { //$formfields, $errormessage=null, $fielddata=array()
	    $thiscid=$this->thiscid; // article ID, used to stay on page
		$nextcid=isset($params['nextcid']) ? $params['nextcid'] : $thiscid; // next page to go to, defaults to stay on this page

		$encdatafields=JRequest::getVar("data");
		if (@$encdatafields!="") { // do a few checks for validity against hacking...
			$b64data=urldecode($encdatafields);
			$res=unserialize(base64_decode($b64data));
			if (!is_array($res) ) { // something definitely wrong, this must be an array!
				$fielddata=array(); // create an empty array instead, wiping all data from the url
			}
			else{
				if (@$res["herkomst"]!="simplecaddy") { // valid array, posted from somewhere else
					$fielddata=array(); // wipe it out
				}
				else
				{
					$fielddata=$res; // all ok, transfer the data to the fields, so we don't have to fill in everything again
				}
		}
		}

		JHTML::script("components/com_simplecaddy/js/datetimepicker.js");
		$mainframe=JFactory::getApplication();

		$html  = "<form name='frmdetails' method='post'>";
		$html .= "<table width='100%' class='sc_details' cellpadding='0' cellspacing='0'>";

        $cfg = new sc_configuration();
		$fields=new fields();
		$formfields=$fields->getPublishedFields();
		$user=JFactory::getUser(); // get the Joomla logged in user
		$userId=$user->id;
		// fielddata is an array containing field names as key and values.
		// fieldnames can be custom field names
		// here is also the moment to get infor from Community Builder

		if ($user->id) {
			$db=JFactory::getDbo();
			$db->setQuery(
				'SELECT * FROM #__user_profiles' .
				' WHERE user_id = '.(int) $userId." AND profile_key LIKE 'profile.%'" .
				' ORDER BY ordering'
			);
			$results = $db->loadObjectList();

			$fielddata['username']=$user->username;
			$fielddata['name']=$user->name;
			$fielddata['email']=$user->email;
			foreach ($results as $profile) {
				$key=str_replace("profile.", "", $profile->profile_key); // remove the profile. part
				$fielddata[$key]=str_replace('"', "", $profile->profile_value); // remove any quotes
			}
		}

  		$n=count($formfields);
		$first=true;
		foreach ($formfields as $field) {
			switch($field->type) {
				case "divider": // simple line with text, no fields
					$html .= "<tr class='$field->classname'><td colspan='2'>".JText::_("$field->caption");
					break;
				case "text": // textbox field, single line
					$html .= "<tr><td>".JText::_("$field->caption")."</td><td>";
					$html .= "<input type='text' name='$field->name' size='$field->length' class='$field->classname' value='". @$fielddata["$field->name"]."'>";
					break;
				case "textarea": // multiline textbox/textarea, no wysiwyg editor
					$html .= "<tr><td>".JText::_("$field->caption")."</td><td>";
					@list($cols, $rows)=explode(",", $field->length);
					$html .= "<textarea name='$field->name' class='$field->classname' cols='$cols' rows='$rows'>". @$fielddata["$field->name"]."</textarea>";
					break;
				case "radio": // yes/no radio buttons
					$html .= "<tr><td>".JText::_("$field->caption")."</td><td>";
					$html .= "<input type='radio' name='$field->name' class='$field->classname' value='yes' ". (@$fielddata["$field->name"]=="yes"?"checked":"").">". JText::_('JYES');
					$html .= "<input type='radio' name='$field->name' class='$field->classname' value='no' ". (@$fielddata["$field->name"]=="no"?"checked":"").">". JText::_('JNO');
					break;
				case "checkbox": // single checkbox
					$html .= "<tr><td>".JText::_("$field->caption")."</td><td>";
					$html .= "<input type='checkbox' name='$field->name' class='$field->classname' value='yes' ". (@$fielddata["$field->name"]=="yes"?"checked":"").">". JText::_('JYES');
					break;
				case "date": // textfield with calendar javascript
					$html .= "<tr><td>".JText::_("$field->caption")."</td><td>";
					$html .= "<input type='text' name='$field->name' id='$field->name' size='$field->length' class='$field->classname' value='". @$fielddata["$field->name"]."'>";
					$html .= "&nbsp;<a href=\"javascript:NewCal('$field->name','ddMMyyyy',true ,24)\"><img src=\"components/com_simplecaddy/images/cal.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".JText::_("SC_PICK_DATE")."\"/></a>";
					break;
				case "dropdown": // dropdown list, single selection
					$html .= "<tr><td>".JText::_("$field->caption")."</td><td>";
					$html .= "<select name='$field->name' id='$field->name' class='$field->classname'>";
					$aoptions=explode(";", $field->fieldcontents);
					foreach ($aoptions as $key=>$value) {
						$html .= "<option value='$value'".(@$fielddata["$field->name"]=="$value"?" selected":"").">$value</option>";
					}
					$html .= "</select>";
					break;
			}
			$html .= $field->required ? "<span class='reqfield'>".JText::_('SC_REQUIRED')."</span>" : "";
			$html .= "";
			$html .= "</td>";
//			if ($first) {
//				$html .= "<td rowspan='$n'><div class='checkoutright'>&nbsp;</div></td>";
//				$first=false;
//			}
			$html .= "</tr>";
		}
		$html .= "<tr><td>&nbsp;</td><td><input class='sc_detailsbutton' type='submit' name='submit' value='". JText::_('SC_CONFIRM') ."'/></td>";
		$html .= "</tr>";

		$html .= "</table>";
		$html .= "<input type='hidden' name='ipaddress' value='". $_SERVER['REMOTE_ADDR'] ."'/>";
		$html .= "<input type='hidden' name='option' value='com_simplecaddy' />";
		$html .= "<input type='hidden' name='action' value='allconfirm' />";
		$html .= "<input type='hidden' name='thiscid' value='$thiscid' />";
		$html .= "<input type='hidden' name='nextcid' value='$nextcid' />";
		$html .= "<input type='hidden' name='herkomst' value='simplecaddy' />";
		$html .= "</form>";
		return $html;
	}

	function _keyform() {
		$html  = JText::_('SC_ENTER_DL_KEY');;
		$html .= '<div>';
		$html .= '<form name="keyform" method="post" action="index.php">';
		$html .= '<input type="text" name="dlkey" size="50" />';
		$html .= '<input type="submit" name="submit" value="Go" />';
		$html .= '<input type="hidden" name="option" value="com_simplecaddy" />';
		$html .= '<input type="hidden" name="action" value="scdl" />';
		$html .= '<input type="hidden" name="task" value="view" />';
		$html .= '</form>';
		$html .= '</div>';
		return $html;
	}


	function dllist() {
		$key=JRequest::getVar("dlkey");
		if (empty($key)) {
			return $this->_keyform();
		}
		$dli=new scdl_items();
		$lst=$dli->getlist($key);
		$this->data->dlitems=$lst;
		$target="";
		$stype="";
		$html ="";
		$lst=$this->data->dlitems;

		$cfg=new sc_configuration();
		$dlpath=$cfg->get("downloadpath");
		if (count($lst)>1) {
			$html .= JText::_('SC_REQUESTED_FILES');
		}
		else
		{
			$html .= JText::_('SC_REQUESTED_FILE');
		}
		$html .= '<table class="downloadtable" width="99%">';
		foreach ($lst as $dlobj) {
			$html .= '<tr>';
			$html .= "<td><a href='". $dlpath ."/". $dlobj->filename."'>$dlobj->filename</a>";
			$html .= "</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";
		return $html;
	}

}

?>