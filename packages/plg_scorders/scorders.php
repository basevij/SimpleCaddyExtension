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
require_once (JPATH_ROOT.DS.'components'.DS.'com_simplecaddy'.DS.'simplecaddy.cart.class.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_simplecaddy'.DS.'simplecaddy.content.class.php');

class scordersconfiguration extends JTable { // this is the standard configuration class for the plugin
// naming is {pluginname}configuration
	var $id;
	var $orderdisplay;
	var $usecontent;
	var $orderlistclass;

	function scordersconfiguration(){
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
		$lang->load('plg_content_scorders', JPATH_ADMINISTRATOR);
		$db	= JFactory::getDBO();
		// next 2 lines: suppress any legitimate errors on checking if the table exists
	   	@$this->__construct( '#__sc_orders_conf', 'id', $db );
		if (@count($db->getTableColumns($this->_tbl))==0 ) {
			// no columns found, so we need to create the table
			$query= "CREATE TABLE `#__sc_orders_conf` (
			`id`  int NULL AUTO_INCREMENT ,
			`orderdisplay`  TEXT NULL ,
			`usecontent` INT(2) NULL,
			`orderlistclass` TEXT NULL,
			PRIMARY KEY (`id`)
			)
			;";
			$this->_db->setQuery($query);
			$this->_db->query();
		};


		// get the first and only useful record in the db here
		$query="select `id` from `$this->_tbl` ";
		$this->_db->setQuery($query);
		$id=$this->_db->loadResult(); // this sets the first ID as the one to use.
		$this->load($id); // load the first id
	}

	function _showconfig() { // mandatory function name
		$lang = JFactory::getLanguage(); // joomla's backend does not load frontend languages by itself
		$extension = 'plg_content_scorders'; // so we need to do this manually here
		$lang->load($extension);


		require_once(JPATH_COMPONENT_SITE."/simplecaddy.class.php");
		$fields=new fields();
		$fieldlist=$fields->getFieldNames(); // get all the fields, standard and custom
		$this->load();

		$db = JFactory::getDbo();
		$query="SELECT sef, title_native, lang_code FROM #__languages ORDER BY sef ASC";
		$db->setQuery($query);
		$languages = $db->loadObjectList();

		display::header(); // while this would be better inside the display function, a plugin does not have access to this
		JToolBarHelper::title( JText::_( "SCORD_CONFIGURATION" ), 'generic.png');
		JToolbarHelper::custom("save", "save", "", JText::_("SCORD_SAVE"), false);
		JToolbarHelper::cancel();
		?>
		<form name="adminForm">
			<table class="adminform">
				<tr><td><?php echo JText::_("SCORD_DISPLAY"); ?></td><td>
				<textarea type="textarea" name="orderdisplay" cols="100" rows="6" /><?php echo $this->orderdisplay; ?></textarea>
				</td></tr>

				<tr><td><?php echo JText::_("SCORD_ORDERLISTCLASS"); ?></td><td>
				<input type="text" name="orderlistclass"  value="<?php echo $this->orderlistclass; ?>"</input>
				</td></tr>


				<tr><td><?php echo JText::_("SCORD_USECONTENT"); ?></td><td>

                <select name="usecontent">
                            <option value="0"<?php echo ($this->usecontent==0?' selected':''); ?> ><?php echo JText::_('SCORD_NO'); ?></option>
                           <option value="1" <?php echo ($this->usecontent==1?' selected':''); ?> ><?php echo JText::_('SCORD_YES'); ?></option>
                            </select>
							</td></tr>


				<input type="hidden" name="option" value="com_simplecaddy" />
				<input type="hidden" name="action" value="pluginconfig"/>
				<input type="hidden" name="pluginname" value="scorders"/>
				<input type="hidden" name="task" />
				<input type="hidden" name="id" value="<?php echo $this->id;?>"/>
			</table>
		</form>

		<?php
	}

	function save($stay=false, $orderingFilter = '', $ignore = '') { // mandatory function to save any variables
		$this->bind($_REQUEST); // get all the fields from the admin form
		$b=$this->store(); // store the relevant fields only
		if ($b) {
			$msg=JText::_("SCORD_CONFIG_SAVED");
		}
		else
		{
			$dbmsg=$this->_db->getErrorMsg();
			$msg=JText::_("SCORD_CONFIG_NOT_SAVED") . $dbmsg;
		}
		$mainframe=JFactory::getApplication();
		$mainframe->redirect("index.php?option=com_simplecaddy&action=plugins&task=show", $msg);
	}

	function cancel() {
		$mainframe=JFactory::getApplication();
		$mainframe->redirect("index.php?option=com_simplecaddy&action=plugins&task=show");
	}
}




//The Content plugin Loadmodule
class plgContentScorders extends JPlugin {
	var $debugshow="hidden"; // just for testing purposes
	var $_plugin_number	= 0;

	function plgContentScorders( &$subject, $config ) {
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

        $regex = '/{(scorders)\s*(.*?)}/i'; // the plugin code to get from content

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
			$line=str_replace("&nbsp;", " ", $elm[2]);
            $line=str_replace(" ", "&", $line);
            $line=strtolower($line);
            parse_str( $line, $parms );

            if (!isset($parms['filter'])) { // no type provided, or just forgot...
            	$parms["filter"]="all";
            }
        	// get all different types of display here, define manually to avoid hacking of the code
        	switch (strtolower($parms['type']) ) {
        		case "skip":
        			$html.=$this->skip($parms);
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
       			case "displayorder":
                    $html.=$this->displayorder($parms);
           			break;
       			case "listorders":
                    $html.=$this->listorders($parms);
           			break;
         		default: // anything else provides an error message
                	$html.=JText::_("SC_THIS_PLUGIN_TYPE_NOT_SUPPORTED"). "({$parms['type']})";
        	}

            $article->text = preg_replace($regex, $html, $article->text, 1);
        }
        return true;
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
		$cfg2=new scordersconfiguration();

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


		if ($usecontentasitem==1) {  // Content override on plugin call
			$db	= JFactory::getDBO();
			$query="select introtext from #__content where id = '$usecontent'";
			$db->setQuery($query);
			$content=$db->loadResult();
		}
		else
		{
			$content=$hhtml;
			if($cfg2->usecontent) {  // Content override from config
				$hhtml = $cfg2->orderdisplay;
			}
			$html = ""; // header html
			$html  ="<div class='$cfg2->orderlistclass'>";
			$html  .="<table>";

		}

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
			if ($thefields['website']== ""){

				$url2="";

			} else {

				$url2="<a href='".$thefields['website']."'>".$thefields['website']."</a>";

			}


			$thefields=unserialize($order->customfields); // the fields filled by customers
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

		if(!$usecontentasitem) {
			$html  .="</table>";
			$html  .="</div>";
		}

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
			$html .= $field->required ? "<span class='reqfield'>".JText::_('SCORD_REQUIRED')."</span>" : "";
			$html .= "";
			$html .= "</td>";
//			if ($first) {
//				$html .= "<td rowspan='$n'><div class='checkoutright'>&nbsp;</div></td>";
//				$first=false;
//			}
			$html .= "</tr>";
		}
		$html.="<tr>	<td>".JText::_('SCORD_ORDER_STATUS')."</td>";
		$html.="\n<td>";
		$html.="<select name='edtostatus'>";
				foreach ($statuses as $status) {
					$selected=(strtolower($single->status)==strtolower(trim($status))?" selected":"");
					$html.="<option value='".trim($status)."' $selected>$status</option>\n";

				}
		$html.="</select>";
		$html.="</td></tr>";


		$html .= "<tr><td>&nbsp;</td><td><input class='sc_detailsbutton' type='submit' name='submit' value='". JText::_('SCORD_CONFIRM') ."'/></td>";
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
		$btnname=JText::_('SCORD_GET_ORDER');
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
		$btnname=JText::_('SCORD_SKIP');
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


}
