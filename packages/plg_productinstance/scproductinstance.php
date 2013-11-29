<?php
/**
 * @copyright Henk von Pickartz 2011-2013
 * SimpleCaddy Paypal processor
 * @version 2.0.4 for Joomla 2.5
 */

// No direct access allowed to this file
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * This plugin has three parts:
 * top part is getting the SimpleCaddy functions library
 * in the function onContentPrepare are the Joomla functions to manage the plugin in the content
 * the function showPayPalScreen displays and manages PayPal specifics
 *
 */
// Import Joomla! Plugin library file
jimport('joomla.plugin.plugin');

require_once (JPATH_ROOT.DS.'components'.DS.'com_simplecaddy'.DS.'simplecaddy.class.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_simplecaddy'.DS.'simplecaddy.pi.class.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_simplecaddy'.DS.'simplecaddy.cart.class.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_simplecaddy'.DS.'simplecaddy.content.class.php');



//The Content plugin Loadmodule
class plgContentScinstance extends JPlugin {
	var $debugshow="hidden"; // just for testing purposes
	var $_plugin_number	= 0;

	function plgContentScinstance( &$subject, $config ) {
        parent::__construct( $subject, $config );
        $this->loadLanguage(); // necessary or not, let's make sure we get the language file
	}

	public function _setPluginNumber() {
		$this->_plugin_number = (int)$this->_plugin_number + 1; // only the first occurrence of the plugin should load css
	}

	function onContentPrepare($context, &$article, &$params, $page = 0 ) {
		if (!JComponentHelper::isEnabled('com_simplecaddy', true)) { // check for the component install
			echo "<div style='color:red;'>The SimpleCaddy component is not installed or is not enabled</div>";
			return;
		}

        $regex = '/{(scinstance)\s*(.*?)}/i'; // the plugin code to get from content

        $parms=array();
        $matches = array();
        preg_match_all( $regex, $article->text, $matches, PREG_SET_ORDER );
        foreach ($matches as $elm) {
 			$this->_setPluginNumber();
 			if ($this->_plugin_number==1) { // get the stylesheet only ONCE per page
				JHTML::stylesheet('components/com_simplecaddy/css/simplecaddy.css' );
				// on windows servers this may need to be changed to
				// JHTML::stylesheet('components\com_simplecaddy\css\simplecaddy.css' );
 			}
			$html ="";
			$line=str_replace("&nbsp;", " ", $elm[2]);
            $line=str_replace(" ", "&", $line);
            $line=strtolower($line);
            parse_str( $line, $parms );

            if (!isset($parms['type'])) { // no type provided, or just forgot...
            	$parms["type"]="list";
            }
        	// get all different types of display here, define manually to avoid hacking of the code
        	switch (strtolower($parms['type']) ) {
       			case "list":
                    $html.=$this->listinstances($parms);
           			break;
       			case "assign":
                    $html.=$this->assignorder($parms);
           			break;
           		default: // anything else provides an error message
                	$html.=JText::_("SC_THIS_PLUGIN_TYPE_NOT_SUPPORTED"). "({$parms['type']})";
        	}

            $article->text = preg_replace($regex, $html, $article->text, 1);
        }
        return true;
	}


	function listinstances($params) {

		if (@isset($params['filter'])) {
			$filter=$params['filter'];
		}

//		$o=new orders();
		$pi=new product_instance();
		$list=$pi->getInstanceListByStatus($filter);

		$html = "";

		$html .= "<table class='scprodinsttable' width='100%' border='1'>\n";
		$html .= "<tr><th>".JText::_('SCPI_NAME')."</th><th>".JText::_('SCPI_TYPE')."</th><th>".JText::_('SCPI_ORDER')."</th></tr>";


		foreach ($list as $prodinst) {

//			$dets=$o->getOrderDetails($prodinst->orderid);

			$html.="<tr><td>$prodinst->instancename</td>";
			$html.="<td>$prodinst->prodcode</td>";
			$html.="<td>$prodinst->orderid</td></tr>";

		}
		$html.="</table>";

		return $html;
	}


	function assignorder($params) {
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



		// get a list of products on the order and for each one show a dropdown of available instances
		// and a form to post these back to the handler.
        $odetails=new orderdetail();
		$detailslist=$odetails->getDetailsByOrderId($this->id);
		$pi=new product_instance();

		$fhtml = "<form  method='post' action='index.php' name='addtocart34'>"; // detail html
		$fhtml .= "<table width='100%' border='1'>\n";
		$fhtml .= "<tr><th>".JText::_('SCPI_CODE')."</th><th>".JText::_('SCPI_DESCRIPTION')."</th><th>".JText::_('SCPI_SELECT')."</th></tr>";
		foreach ($detailslist as $detail) {
			$lst=$pi->getFreeInstanceListByProdcode($detail->prodcode);
			$desc = $detail->shorttext;
			if(!$detail->option == "-") {
				$desc .= " - ".$detail->option;
				}
			$fhtml .= "<tr><td>$detail->prodcode</td>\n";
			$fhtml .= "<td>$desc</td>\n";


			$fhtml .= "<select name='$detail->prodcode.$detail->id' id='$detail->prodcode.$detail->id' >";
// 					get current valid from instance
			$current=$pi->getInstanceByDetailId($detail->id);
            $fhtml .= "<option value='$instance->id'>".$current->instancename."</option>";
			foreach ($lst as $instance) {
				$fhtml .= "<option value='$instance->id'>".$instance->instancename."</option>";
			}
			$fhtml .="</select>";



		}


		$fhtml .= "</table>\n";
		$fhtml .= "<input class='sc_detailsbutton' type='submit' name='submit' value='". JText::_('SCPI_CONFIRM') ."'/>";

		$fhtml .= "<input type='hidden' name='option' value='com_simplecaddy' />";
		$fhtml .= "<input type='hidden' name='action' value='assign' />";
		$fhtml .= "<input type='hidden' name='data' value='$ordercode' />";
		$fhtml .= "<input type='hidden' name='oid' value='$single->id' />";
		$fhtml .= "<input type='hidden' name='thiscid' value='$thiscid' />";
		$fhtml .= "<input type='hidden' name='nextcid' value='$nextcid' />";
		$fhtml .= "<input type='hidden' name='herkomst' value='simplecaddy' />";
		$fhtml .= "</form>";




		return $html;
	}


}
?>