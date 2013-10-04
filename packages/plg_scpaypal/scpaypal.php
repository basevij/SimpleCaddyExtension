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

class scpaypalconfiguration extends JTable { // this is the standard configuration class for the plugin
// naming is {pluginname}configuration
	var $id;
	var $ppenvironment;
	var $reselleremail;
	var $paypalcurrency;
	var $returnsuccess;
	var $returnfail;
	var $dlpage;
	var $validstatus;
	var $invalidstatusmsg;

	function scpaypalconfiguration(){
/** Firsty DB Query:

$db =& JFactory::getDbo();
$db->setQuery(
    'SELECT sef, title_native' .
    ' FROM #__languages' .
    ' ORDER BY sef ASC'
);
$options = $db->loadObjectList();
$selected = "en_GB"
Secondly in your layout:

echo JHtml::_('select.options', $options, 'sef', 'title_native', $selected);
This will output HTML <select> tag with "English" selected by default

EDIT: Should you want to use it in JForm (Joomla > 1.6) there is a field type called "contentlanguage"

<field name="languages" type="contentlanguage" />
*/
		$lang = JFactory::getLanguage();
		$lang->load('plg_content_scpaypal', JPATH_ADMINISTRATOR);
		$db	= JFactory::getDBO();
		// next 2 lines: suppress any legitimate errors on checking if the table exists
	   	@$this->__construct( '#__sc_paypal', 'id', $db );
		if (@count($db->getTableColumns($this->_tbl))==0 ) {
			// no columns found, so we need to create the table
			$query= "CREATE TABLE `#__sc_paypal` (
			`id`  int NULL AUTO_INCREMENT ,
			`ppenvironment`  INT(11) NULL ,
			`reselleremail`  varchar(255) NULL ,
			`paypalcurrency`  varchar(32) NULL ,
			`returnsuccess`  TEXT NULL ,
			`returnfail`  TEXT NULL ,
			`overrideipnurl` TEXT NULL,
			`dlpage`  text NULL ,
			`validstatus` TEXT NULL,
			`invalidstatusmsg` TEXT NULL,
			PRIMARY KEY (`id`)
			)
			;";
			$this->_db->setQuery($query);
			$this->_db->query();
		};
		$cols=$db->getTableColumns($this->_tbl);

		if ($cols["returnsuccess"]=="varchar") {
			$query="ALTER TABLE `#__sc_paypal`
					MODIFY COLUMN `returnsuccess`  text NULL AFTER `paypalcurrency`,
					MODIFY COLUMN `returnfail`  text NULL AFTER `returnsuccess`; ";
			$this->_db->setQuery($query);
			$this->_db->query();
		}

		if (@count($cols) <=8 ) {
			$query="ALTER TABLE `#__sc_paypal`
					ADD COLUMN `validstatus`  text NULL AFTER `dlpage`,
					ADD COLUMN `invalidstatusmsg`  text NULL AFTER `validstatus`; ";
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
		$extension = 'plg_content_scpaypal'; // so we need to do this manually here
		$lang->load($extension);
		$ppcurrency=array();
		$ppcurrency["AUD"]="Australian Dollars (AUD)";
		$ppcurrency["BRL"]="Brazilian Real (BRL) (Domestic use only)";
		$ppcurrency["CAD"]="Canadian dollar (CAD)";
		$ppcurrency["CHF"]="Swiss Franc (CHF)";
		$ppcurrency["CZK"]="Czech Koruna (CZK)";
		$ppcurrency["DKK"]="Danish Krone (DKK)";
		$ppcurrency["EUR"]="Euros (EUR)";
		$ppcurrency["GBP"]="Pound Sterling (GBP)";
		$ppcurrency["HKD"]="Hong Kong Dollar (HKD)";
		$ppcurrency["HUF"]="Hungarian Forint (HUF)";
		$ppcurrency["ILS"]="Israeli New Sheqel (ILS)";
		$ppcurrency["JPY"]="Yen (YEN)";
		$ppcurrency["MXN"]="Mexican Pesos (MXN)";
		$ppcurrency["MYR"]="Malaysian Ringgit (MYR) (Domestic use only)";
		$ppcurrency["NOK"]="Norwegian Krone (NOK)";
		$ppcurrency["NZD"]="New Zealand Dollar (NZD)";
		$ppcurrency["PHP"]="Philippine Peso (PHP)";
		$ppcurrency["PLN"]="Polish Zloty (PLN)";
		$ppcurrency["SEK"]="Swedish Krona (SEK)";
		$ppcurrency["SGD"]="Singapore Dollar (SGD)";
		$ppcurrency["THB"]="Thai Baht (THB)";
		$ppcurrency["TRY"]="Turkish Lira (TRY)";
		$ppcurrency["TWD"]="Taiwan New Dollar (TWD)";
		$ppcurrency["USD"]="US Dollar (USD)";

		require_once(JPATH_COMPONENT_SITE."/simplecaddy.class.php");
		$fields=new fields();
		$fieldlist=$fields->getFieldNames(); // get all the fields, standard and custom
		$this->load();

		$db = JFactory::getDbo();
		$query="SELECT sef, title_native, lang_code FROM #__languages ORDER BY sef ASC";
		$db->setQuery($query);
		$languages = $db->loadObjectList();

		display::header(); // while this would be better inside the display function, a plugin does not have access to this
		JToolBarHelper::title( JText::_( "SCPP_CONFIGURATION" ), 'generic.png');
		JToolbarHelper::custom("save", "save", "", JText::_("SCPP_SAVE"), false);
		JToolbarHelper::cancel();
		?>
		<form name="adminForm">
			<table class="adminform">
				<tr><td style="width: 450px;"><?php echo JText::_("SCPP_ENVIRONMENT");?></td>
				<td>
					<?php
						$show_hide = array (JHTML::_('select.option', 0, JText::_('SCPP_SANDBOX')), JHTML::_('select.option', 1, JText::_('SCPP_LIVE')),);
						foreach ($show_hide as $value) {
							echo "<input type='radio' value='$value->value' name='ppenvironment' ".($this->ppenvironment==$value->value?' checked':'').">$value->text";
						}
					?>
				</td></tr>
				<tr><td><?php echo JText::_("SCPP_RESELLER_EMAIL");?></td>
				<td><input type="text" name="reselleremail" value="<?php echo $this->reselleremail; ?>" size="100" /></td></tr>
				<tr><td><?php echo JText::_("SCPP_CURRENCY");?></td><td>
				<select name="paypalcurrency">
				<?php
					foreach ($ppcurrency as $key=>$f) {
						echo "<option value='$key'".($key==$this->paypalcurrency?" selected":"").">$f</option>";
					}
				?>
				</select>
				</td></tr>
				<?php
				$aretsuc=unserialize($this->returnsuccess);
				$aretfail=unserialize($this->returnfail);
				foreach($languages as $language) { ?>
					<tr><td><?php echo JText::_("SCPP_RETURNSUCCESS"); echo " " . $language->title_native; ?></td><td>
					<input type="text" name="returnsuccess[<?php echo $language->lang_code;?>]" value="<?php echo $aretsuc[$language->lang_code]; ?>" size="150" />
					</td></tr>
					<tr><td><?php echo JText::_("SCPP_RETURNFAIL"); echo " " . $language->title_native;?></td><td>
					<input type="text" name="returnfail[<?php echo $language->lang_code;?>]" value="<?php echo $aretfail[$language->lang_code]; ?>" size="150" />
					</td></tr>
				<?php } ?>

					<tr><td><?php echo JText::_("SCPP_IPNOVERRIDE"); ?></td><td>
					<input type="text" name="overrideipnurl" value="<?php echo $this->overrideipnurl; ?>" size="150" />
					</td></tr>

				<tr><td> <?php echo JText::_("SCPP_VALIDSTATUS");?></td><td>
				<input type="text" name="validstatus" value="<?php echo $this->validstatus; ?>" size="30" />
				</td></tr>
				<tr><td><?php echo JText::_("SCPP_INVALIDSTATUSMSG"); ?></td><td>
				<textarea type="textarea" name="invalidstatusmsg" cols="100" rows="6" /><?php echo $this->invalidstatusmsg; ?></textarea>
				</td></tr>


				<input type="hidden" name="option" value="com_simplecaddy" />
				<input type="hidden" name="action" value="pluginconfig"/>
				<input type="hidden" name="pluginname" value="scpaypal"/>
				<input type="hidden" name="task" />
				<input type="hidden" name="id" value="<?php echo $this->id;?>"/>
			</table>
		</form>
		<p>
		Paypal uses your email on this page. Please make sure to also add {emailcloak=off} to the page to make sure the email is not scrambled by Joomla!
		</p>
		<?php
	}

	function save($stay=false, $orderingFilter = '', $ignore = '') { // mandatory function to save any variables
		$this->bind($_REQUEST); // get all the fields from the admin form
		$this->returnsuccess=serialize($_REQUEST["returnsuccess"]);
		$this->returnfail=serialize($_REQUEST["returnfail"]);
		$b=$this->store(); // store the relevant fields only
		if ($b) {
			$msg=JText::_("SCPP_CONFIG_SAVED");
		}
		else
		{
			$dbmsg=$this->_db->getErrorMsg();
			$msg=JText::_("SCPP_CONFIG_NOT_SAVED") . $dbmsg;
		}
		$mainframe=JFactory::getApplication();
		$mainframe->redirect("index.php?option=com_simplecaddy&action=plugins&task=show", $msg);
	}

	function cancel() {
		$mainframe=JFactory::getApplication();
		$mainframe->redirect("index.php?option=com_simplecaddy&action=plugins&task=show");
	}
}



// Import Joomla! Plugin library file
jimport('joomla.plugin.plugin');
require_once (JPATH_ROOT.'/components/com_simplecaddy/simplecaddy.class.php'); // mandatory

//The Content plugin Loadmodule
class plgContentScpaypal extends JPlugin {
	var $debugshow="hidden"; // just for testing purposes
	var $_plugin_number	= 0;

	function plgContentScpaypal( &$subject, $config ) {
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

        $regex = '/{(scpaypal)\s*(.*?)}/i'; // the plugin code to get from content

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
 			if ($this->_plugin_number>1 ) { // there should be only ONE payment plugin per page...
	            $article->text = preg_replace($regex, JText::_("SC_EXTRA_PLUGINS_REMOVED"), $article->text, 1);
		 		return true;
 			}
			$line=str_replace("&nbsp;", " ", $elm[2]);
            $line=str_replace(" ", "&", $line);
            $line=strtolower($line);
            parse_str( $line, $parms );

            if (!isset($parms['type'])) { // no type provided, or just forgot...
            	$parms["type"]="checkout";
            }
        	// get all different types of display here, define manually to avoid hacking of the code
        	switch (strtolower($parms['type']) ) {
        		case "ipn":
        			$html=$this->paypalIPN($parms);
        			break;
        		case "paysuccess":
        			$html=$this->showPayPalSuccess($parms);
        			break;
        		case "payfail":
        			$html=$this->showPayPalFail($parms);
        			break;
       			case "checkout": // the default if nothing has been provided
                	$html=$this->showPayPalscreen($parms);
                	break;
        		default: // anything else provides an error message
                	$html=JText::_("SC_THIS_PLUGIN_TYPE_NOT_SUPPORTED"). "({$parms['type']})";
        	}
            $article->text = preg_replace($regex, $html, $article->text, 1);
        }
        return true;
	}

	function showPayPalScreen($parms) { // display of any PayPal specific stuff goes here
		$cfg=new scpaypalconfiguration();
		if (!$cfg->reselleremail) {
			$html = "Reseller email is not entered, please add this to the configuration first. PayPal cannot be used!";
			return $html;
		}
		$currency = $cfg->paypalcurrency;
		$environment = $cfg->ppenvironment;
		$ipnoverride= $cfg->overrideipnurl;


		$ordercode=JRequest::getVar("data"); // the data contains the ordercode when you finish the details page

		$orders=new orders();
		$orderid=$orders->getOrderIdFromCart($ordercode);
		$order=new order();
		$order->load($orderid);

		// add the details to the order
		$gtotal=0; //define the grand total
		$pptax=0; // define the tax for paypal
		$taxrate=0;
		if ($order->status <> $cfg->validstatus) {
			if(!$cfg->invalidstatusmsg) {
				$html= "Order does not have the correct status for payment <br> Contact your vendor for details";
			} else {
				$html= $cfg->invalidstatusmsg;
			}
			return $html;
		}

        if ($cfg->ppenvironment==1) { // live
    		$html = "<form action='https://www.paypal.com/cgi-bin/webscr' method='post' name ='ppform' target='paypal'>";
        }
        else
        { // sandbox
            $html = "<form action='https://www.sandbox.paypal.com/cgi-bin/webscr' method='post' name ='ppform' target='_blank'>";
        }
   		$html .="<input type=\"hidden\" name=\"cmd\" value=\"_cart\">";
   		$html .="<input type=\"hidden\" name=\"upload\" value=\"1\">";
   		$html .="<input type=\"hidden\" name=\"business\" value=\"$cfg->reselleremail\">";
		$html .="<input type=\"hidden\" name=\"currency_code\" value=\"$currency\">";
		$html .="<input type=\"hidden\" name=\"rm\" value=\"2\">";
		$fieldnumber=0; // PayPal field numbering variable
        $odetails=new orderdetail();
        $lst=$odetails->getDetailsByOrderId($orderid);
		foreach ($lst as $product) {
            // create a post field and field value for PayPal
            if($product->total>0) {
            	switch ($product->prodcode) {
            		case "shipping":
 						$html .="<input type=\"hidden\" name=\"shipping_1\" value=\"".number_format($product->unitprice, 2,".", "")."\">";
 	          			break;
    				case "tax":
						$html .= "<input type=\"hidden\" name=\"tax_cart\" value=\"".number_format($product->unitprice, 2,".", "").'">';//
    					break;
    				default:
						$fieldnumber = $fieldnumber +1 ; //increment the field number (could also be done with $fieldnumber++)
						$html .= "<input type='hidden' name='item_name_".$fieldnumber. "' value='".$product->shorttext." (".$product->prodcode.") ".$product->option. "'>";
						$html .= "<input type='hidden' name='amount_".$fieldnumber. "' value='".number_format($product->unitprice, 2,".", ""). "'>";
						$html .= "<input type='hidden' name='quantity_".$fieldnumber. "' value='".$product->qty. "'>";
				}
            }
            else // price <0 so transfer it as a discount amount instead of a product
            {
                $html .= "<input type='hidden' name='discount_amount_cart' value='".abs($product->total). "'>";
            }
            $gtotal += $product->total;
		}
		$html .="<input type=\"hidden\" name=\"custom\" value=\"$orderid\">";


        // these are the return urls to go to when coming back from paypal
        $asuccessurl= unserialize( $cfg->returnsuccess );
		$lang = JFactory::getLanguage(); // joomla's backend does not load frontend languages by itself
        $default = $lang->getDefault();

		$successurl=$asuccessurl[$default];
        $afailurl= unserialize($cfg->returnfail);
        $failurl=$afailurl[$default];

		if (strpos($successurl, "?")>0) {
			$successurl .= "&dlkey=$order->ordercode";
		}
		else
		{
			$successurl .= "?dlkey=$order->ordercode";
		}

		$html .="<input type=\"hidden\" name=\"cancel_return\" value=\"$failurl\">";
		$html .="<input type=\"hidden\" name=\"return\" value=\"$successurl\">";

		if($ipnoverride) {
			$html .="<input type=\"hidden\" name=\"notify_url\" value=\"$cfg->overrideipnurl\">";
			}

 		// PayPal requires you use their logo to check out. Check the PayPal site for other button types
 		// look here for more buttons from PayPal https://www.paypal.com/newlogobuttons
        // look here for the rules of usage of the paypal logos and pay buttons:
        //https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/pdf/merchant_graphic_guidelines.pdf

		/** customizers, do your stuff here!
		You may add all kinds of fields now to the paypal "cart" to customize your heading in PayPal and so on.
		None of these novelties have been added here, but if you want to customize the appearance of your presence in Paypal,
		Here is the place.
		*/

 		$html .= '<p>
        <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;"> <span style="font-size:11px; font-family: Arial, Verdana;">The safer, easier way to pay.</span>
        <p>';

        // additional PayPal info
        // be careful to 'escape' any " with \ !
/**
        $html .="<p>
<!-- PayPal Logo --><table border=\"0\" cellpadding=\"10\" cellspacing=\"0\" align=\"center\"><tr><td align=\"center\"></td></tr>
<tr><td align=\"center\"><a href=\"#\" onclick=\"javascript:window.open('https://www.paypal.com/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside','olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=350');\">
<img  src=\"https://www.paypal.com/en_US/i/bnr/vertical_solution_PPeCheck.gif\" border=\"0\" alt=\"Solution Graphics\"></a></td></tr></table><!-- PayPal Logo -->
        </p>";
*/
		// otherwise we could use a simple submit button:
		//$html .='<input type="submit" value="PayPal">  ';
		$html .= "</form>";
		return $html;
	}

	function showPayPalSuccess($parms) {
		$pcfg=new simplecaddyconfiguration();
		$html = "";
//		$html .= sprintf("<pre>%s</pre>", print_r($_POST, 1));
		// the values from PayPal we are interested in are the [custom] and [txn_id] values.

        $orderid=JRequest::getVar("custom"); // orderid coming back from PP
        $txn_id=JRequest::getVar("txn_id"); // pp transaction ID

		$cfg=new sc_configuration();
        $statuses=explode("\r\n", $cfg->get("ostatus"));
        $status=$statuses[count($statuses)-1]; // set the status to the last one in the list

        $scorder=new order();
        $scorder->load($orderid);
        $scorder->paymentcode="PayPal: ".$txn_id;
        $scorder->status=$status;
        // obviously there are other changes you could make to an order upon successful payment...
        $scorder->store();

		// now send success email
		$em=new email();
		$em->mailorder($orderid);
        $dlpageurl= $pcfg->dlpage;
		$mainframe=JFactory::getApplication();
		$dlpageurl .= "&dlkey=$scorder->ordercode";
		$mainframe->redirect($dlpageurl);
		return $html;
	}

	function paypalIPN($parms) {
		$pcfg=new simplecaddyconfiguration();
		$html = "";
//		$html .= sprintf("<pre>%s</pre>", print_r($_POST, 1));
		// the values from PayPal we are interested in are the [custom] and [txn_id] values.


        $ipn=$this->verifyIPN();

        $status=$ipn['status'];
        $orderid=JRequest::getVar("custom"); // orderid coming back from PP
        $txn_id=$ipn['txn_id']; // pp transaction ID
		if ($ipn['response']=="VERIFIED")
		{
//
//          I'm going to use the Paypal statuses for my orders but you could transcribe them
//			if ($status == "completed"){
//				$new_status = "PAID";
//			} else if ($status == "refunded") {
//				$new_status = "REFUND";
//			} else {
//				$new_status = "UNKNOWN";
//			}

	        $scorder=new order();
	        $scorder->load($orderid);
	        $scorder->paymentcode="PayPal: ".$txn_id;
	        $scorder->status=$status;
	        // obviously there are other changes you could make to an order upon successful payment...
	        $scorder->store();

			// now send success email but only if successful - A refund is another type of notification that the customer might want to know about.
			if ($status == "completed" or $status == "refunded")
			{

				if (@isset($parms['usecontent'])) {
					$usecontent=$parms['usecontent'];
				}

				$em=new email();
				$em->mailorder($orderid, $usecontent);
			}


		}


//      -> removed the forward to the download directory as this is an offline call - Use a paypal success plugin to get to the download pages
//      $dlpageurl= $pcfg->dlpage;
//		$mainframe=JFactory::getApplication();
//		$dlpageurl .= "&dlkey=$scorder->ordercode";
//		$mainframe->redirect($dlpageurl);
		return $html;
	}

	function showPayPalFail() {
		$html = "Payment failed";
//		$html .= sprintf("<pre>%s</pre>", print_r($_POST, 1));
		return $html;
	}


 	function verifyIPN(){

		error_log(print_r($_POST,true),0);
		$ipn=array();
	 	//Take everything in POST, post back with CURL.
 		//Add cmd=_notify-validate as a field on the end
 		$fields = "";

 		foreach ($_POST as $key=>$value){
 			$fields .= $key."=".urlencode($value)."&";
 		}

 		$fields .= "cmd=_notify-validate";
		$cfg=new scpaypalconfiguration();


 		$ch = curl_init();
        if ($cfg->ppenvironment==1) { // live
 			$ppurl = "https://www.paypal.com/cgi-bin/webscr";
 		} else {
 			$ppurl = "https://www.sandbox.paypal.com/cgi-bin/webscr ";
 		}

 		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_URL, $ppurl);
 		curl_setopt($ch, CURLOPT_POST, 1);
 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

 		$response = curl_exec($ch);
 		curl_close($ch);

 		$ipn['response']=$response;
 		$ipn['status']=strtolower($_POST['payment_status']);
 		$ipn['receiver_email']=$_POST['receiver_email'];
 		$ipn['id']=$_POST['invoice'];
 		$ipn['txn_id']=$_POST['txn_id'];

		error_log(print_r($ipn,true),0);

		//Assume server will have sent a 200 OK response.

 		return $ipn;

 	}

}
