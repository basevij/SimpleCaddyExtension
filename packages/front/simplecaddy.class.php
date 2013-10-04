<?php
/**
* @package SimpleCaddy 2.0.4 for Joomla 2.5
* @copyright Copyright (C) 2006-2013 Henk von Pickartz. All rights reserved.
* General class file
*/
// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once("simplecaddy.cart.class.php"); // just to make sure we have the cart functions available

class email {
	function mailorder($orderid=null, $usecontent=null) {

        if (!$orderid) return;


        $cfg=new sc_configuration();
		$tsep=$cfg->get('thousand_sep');
		$dsep=$cfg->get('decimal_sep');
		$decs=$cfg->get('decimals');
		$mode=1; // always html

		$order = new order();
		$order->load($orderid);
		$emailbody=$order->getOrderPresentationHTML($usecontent);

		$emailsubject=$cfg->get("email_subject");
		//todo: define some variables to replace for the subject line
		$emailsubject=str_replace("#name#", $order->name, $emailsubject);
		$emailsubject=str_replace("#ordertotal#", number_format($order->gtotal, $decs, $dsep, $tsep), $emailsubject);

// substitute custom fields in header

		$fields=new fields();
		$fieldslist=$fields->getPublishedFields() ;// the custom fields defined for this system
		$thefields=unserialize($order->customfields); // the fields filled by customers
		foreach ($fieldslist as $key=>$customfield) {
			$emailsubject=str_replace("#".$customfield->name."#", $thefields[$customfield->name], $emailsubject); // replace custom tags with the field names
		}



		$mailengine=$cfg->get("mailengine");
		if ($mailengine=="alternative") {
			// some servers do NOT like to send to an array of addresses
			// so as an alternative way we send the emails one by one
			$from = $cfg->get('email_from');
			$fromname = $cfg->get('email_fromname');
			$recipient=trim($order->email); // customer email
			$subject = stripslashes($emailsubject);
			$body = $emailbody;
			$mode = $mode;
			// send to customer
			$rs = JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
			// now send the eventual copies
			$emailcopies=$cfg->get('email_copies'); // the complete address list is already trimmed
			$aemailcopies=explode("\r\n", $emailcopies);
			foreach ($aemailcopies as $key=>$emailaddress) {
				$copyrecipient=trim($emailaddress); // trim each address from any \n ...
				$rs = JUtility::sendMail($from, $fromname, $copyrecipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
			}
		}
		else
		{ // standard Joomla mail engine
			$mailer = JFactory::getMailer();
			// Build e-mail message format
			$mailer->setSender(array($cfg->get('email_from'), $cfg->get('email_fromname')));
			$mailer->setSubject(stripslashes($emailsubject));
			$mailer->setBody($emailbody );
			$mailer->IsHTML($mode);

			$emailcopies=$cfg->get('email_copies'); // the complete address list is already trimmed
			$aemailcopies=explode("\n", $emailcopies);
			// Add recipients
			$mailer->addRecipient(trim($order->email));
			// add the copies
			foreach ($aemailcopies as $key=>$emailaddress) {
				$mailer->addRecipient(trim($emailaddress)); // trim each address from any \n ...
			}
			// Send the Mail
			$rs	= $mailer->Send();
		}
		return $rs;
	}
}

class optionsshowas {
    var $type;

    function optionsshowas() {
        $this->type[1]=JText::_('SC_HORIZ_RADIO');
        $this->type[2]=JText::_('SC_DROPDOWN');
        $this->type[3]=JText::_('SC_STANDARDLIST');
        $this->type[4]=JText::_('SC_VERT_RADIO');
        $this->type[5]=JText::_('SC_SINGLELINE');
        $this->type[6]=JText::_('SC_CALENDAR');
    }

}

class productoption extends JTable { // individual options

var $id;
	var $optgroupid="";
	var $formula="";
	var $caption="";
    var $description="";
    var $defselect;
    var $disporder;

	function productoption() {
		$db	= JFactory::getDBO();
	   	$this->__construct( '#__sc_productoptions', 'id', $db );
	}


    function getbygroup($optgroupid) {
        $query="select * from ".$this->_tbl." where `optgroupid` = '$optgroupid' order by `disporder` asc ";
        $this->_db->setQuery($query);
        $lst=$this->_db->loadObjectList();
        return $lst;
    }
}

class optiongroups extends JTable { // option groups
    var $id;
    var $productid;
    var $prodcode;
    var $title;
    var $showas;
    var $disporder;

    var $redirect;
    var $message;

    function optiongroups() {
		$db	= JFactory::getDBO();
	   	$this->__construct( '#__sc_prodoptiongroups', 'id', $db );
	}

    function redirect() {
    }

    function redirect2() {
        $mainframe=JFactory::getApplication();
        $mainframe->redirect($this->redirect, $this->message);
    }

    function addgroup($prodcode) {
    	$product=new products();
    	$product->getproductByProdCode($prodcode);
        $this->id=null;
        $this->title=stripslashes( JText::_('SC_UNTITLED_OPTION'));
        $this->showas=1;
        $this->prodcode=$prodcode;
        $this->productid=$product->id;
        $this->disporder=0;
        $this->store();
    }

    function remove() {
        $id=JRequest::getVar("optgrid");
        $prodid=JRequest::getVar("productid");
        $this->delete($id);
        $this->message="Option group deleted";
        $this->redirect="index.php?option=com_simplecaddy&action=products&task=edit&cid[0]=$prodid";
        $this->redirect2();
    }

    function getgroups($prodcode) {
        $query="select * from ".$this->_tbl." where `prodcode` = '$prodcode' order by `disporder` asc ";
        $this->_db->setQuery($query);
        $lst=$this->_db->loadObjectList();
        return $lst;
    }

    function getgroupsbyid($pid) {
        $query="select * from ".$this->_tbl." where `productid` = '$pid' ";

       $this->_db->setQuery($query);
        $lst=$this->_db->loadObjectList();
       return $lst;
    }

    function getgroupids($aprodcode=array()) {

   	$pcs=implode("','", $aprodcode);
        $query="select `id` from `$this->_tbl` where `prodcode` IN ( '$pcs' ) ";
        $this->_db->setQuery($query);
        $lst=$this->_db->loadResultArray();
        return $lst;
    }

    function deletegroup($id) {
    	$query="DELETE FROM `$this->_tbl` where `id`='$id' ";
    	$this->_db->setQuery($query);
	   	$this->_db->query();
    }


    function show() {
        $id=JRequest::getVar("optgrid");
        $productid=JRequest::getVar("productid");
        $this->load($id);
        display::showoptgroup($this, $productid);
    }

    function saveoptiongroup() {
        $prodid=JRequest::getVar("productid");
        $this->id=JRequest::getVar("id");
        $this->title=JRequest::getVar("title");
        $this->showas=JRequest::getVar("showas");
        $this->prodcode=JRequest::getVar("prodcode");
        $this->disporder=JRequest::getVar("disporder");
        $this->productid=$prodid;
        $this->store();
        $this->message="Option group saved";
        $this->redirect="index.php?option=com_simplecaddy&action=products&task=edit&cid[0]=$prodid";
        $this->redirect2();
    }
}

class options extends JTable {
    var $id;
    var $optgroupid;
    var $description;
    var $formula;
    var $caption;
    var $defselect;
    var $disporder;
    var $redirect;
    var $message;

    function options() {
		$db	= JFactory::getDBO();
	   	$this->__construct( '#__sc_productoptions', 'id', $db );
	}

    function redirect() {
    }

    function redirect2() {
        $mainframe=JFactory::getApplication();
        $mainframe->redirect($this->redirect, $this->message);
    }

    function getindoption($optgrid) {
        $query="select * from `". $this->_tbl."` where `optgrid` = '$optgrid'";
        $this->_db->setQuery($query);
        $lst=$this->_db->loadObjectList();
        return $lst;
    }

    function getindoptionids($optgrid=array()) {
    	$optgrids=implode(",", $optgrid);
        $query="select `id` from `$this->_tbl` where `optgroupid` IN ( $optgrids )";
       $this->_db->setQuery($query);
        $lst=$this->_db->loadResultArray();
        return $lst;
    }

    function deleteindoption($optionid) {
    	$query="DELETE FROM `$this->_tbl` where `id`='$optionid' ";
    	$this->_db->setQuery($query);
    	$this->_db->query();
    	echo $this->_db->getQuery();
    }

    function showindoptions() {
        $id=JRequest::getVar("optgrid");
        $productid=JRequest::getVar("productid");
        $po=new productoption();
        $lst=$po->getbygroup($id);
        display::showindoptions($lst, $id, $productid);

   }


    function saveoptions() {
        $optgrid=JRequest::getVar("optgrid");
        $productid=JRequest::getVar("productid");
        $optionid=JRequest::getVar("optionid");
        $optionshorttext=JRequest::getVar("optionshorttext");
        $optionformula=JRequest::getVar("optionformula");
        $optioncaption=JRequest::getVar("optioncaption");
        $optiondefselect=JRequest::getVar("optiondefselect");
        $optiondisporder=JRequest::getVar("optiondisporder");

       // first, get rid of the existing options, if any
        $query="delete from `".$this->_tbl."` where `optgroupid` = '$optgrid' ";
        $this->_db->setQuery($query);
        $this->_db->query();

        // now recreate the new options
        foreach($optionid as $key=>$value) {
            $this->id=null;
            $this->optgroupid=$optgrid;
            $this->description=$optionshorttext[$key];
            $this->formula=$optionformula[$key];
            $this->caption=$optioncaption[$key];
            $this->defselect=($key==$optiondefselect?"1":"0");
            $this->disporder=$optiondisporder[$key];
            $this->store();
        }
		$this->redirect="index.php?option=com_simplecaddy&action=options&task=showindoptions&optgrid=$optgrid&tmpl=component&productid=$productid";
        $this->message=JText::_("SC_INDIVIDUAL_OPTIONS_SAVED");
        $this->redirect2();
    }

    function saveoptiongroup() {
        $prodid=JRequest::getVar("prodid");
        $this->id=JRequest::getVar("id");
        $this->title=JRequest::getVar("title");
        $this->showas=JRequest::getVar("showas");
        $this->prodcode=JRequest::getVar("prodcode");
        $this->disporder=JRequest::getVar("disporder");
        $this->store();

        $this->message="Option group saved";
        $this->redirect="index.php?option=com_simplecaddy&action=products&task=edit&cid[0]=$prodid";
        $this->redirect2();
    }

}

class products extends JTable {
	var $id=null;
	var $prodcode="";
	var $shorttext="";
	var $av_qty=0;
	var $unitprice=0;
	var $published=0;
	var $showas=1;
	var $options="";
	var $optionstitle="";
	var $category="";
	var $shippoints=0;
	var $downloadable=0;
	var $filename;
	var $shiplength;
	var $shipwidth;
	var $shipheight;
	var $shipweight;
	var $userid;


function products() {
		$db	= JFactory::getDBO();
	   	$this->__construct( '#__sc_products', 'id', $db );
	}

	function getProduct($id) {
		$this->load($id);
	}

    function getProductCodeList() {
        $query="select `prodcode`, `shorttext` from {$this->_tbl} ";
        $this->_db->setQuery($query);
        $lst=$this->_db->loadObjectList();
        return $lst;
    }

	function getAllProducts($filter=null, $field=null, $orderby=null) {
		global $mosConfig_list_limit;
        $mainframe=JFactory::getApplication();
		$query="select count(*) as total from ".$this->_tbl;
		if($filter) $query .= " where `category` = '$filter'";
		$this->_db->setQuery($query);
		$total=$this->_db->loadResult();

		$limit = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
		$limitstart = intval( $mainframe->getUserStateFromRequest( "view{scp}limitstart", 'limitstart', 0 ) );
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit );
		$query="select * from ".$this->_tbl;
		if ($filter) $query .= " where `category` = '$filter'";
		if ($field) $query .= " order by `$field` $orderby ";
		$this->_db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$lst=$this->_db->loadObjectList();

		$query="select distinct `category` from ".$this->_tbl;
		$this->_db->setQuery($query);
		$lstcategories=$this->_db->loadObjectList();

		$categories[]=array('value'=>"", 'text'=>"", "");
		if ($lstcategories) {
			foreach ($lstcategories as $cat) {
				$categories[]=array('value'=>$cat->category, 'text'=>$cat->category, $filter );
			}
		}
		else
		{
			$categories[]=array('value'=>'None defined', 'text'=>"No categories selectable");
		}
		$lists['category'] = JHTML::_('select.genericlist',  $categories, 'search', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter);
		$res['lst']=$lst;
		$res['nav']=$pageNav;
		$res['lists']=$lists;
		return $res;
	}

	function getPublishedProducts() {
		$query="select * from ".$this->_tbl." where published=1";
		$query .= " order by `category`, `shorttext` asc ";
		$this->_db->setQuery($query);
		$lst=$this->_db->loadObjectList();
		return $lst;
	}

	function saveproduct() {
		$forbiddenchars=array(" ", ".", ",", "/", "$", "\\");
		$id=JRequest::getVar("id");
		$this->load($id);
		$this->bind($_REQUEST);
		$this->prodcode=strtolower($this->prodcode);
		$this->prodcode=str_replace($forbiddenchars, "", $this->prodcode);
		$user=JFactory::getUser();

	if ($this->userid==0) $this->userid=$user->id;
		$this->store();
		if ($this->id) { //previously saved product
			// save the optiongroups as well to reflect possible changes in prodcode
			$optgroups=new optiongroups();
			$lst=$optgroups->getgroupsbyid($this->id);
			foreach ($lst as $optgroup) {
				$og=new optiongroups();
				$og->load($optgroup->id);
				$og->prodcode=$this->prodcode;
				$og->store();
			}
		}
		return $this->id;
	}


	function duplicate($pid) {
		$this->load($pid);
		$this->id=null;
		$this->shorttext = JText::_("SC_COPY_OF") .$this->prodcode ." ". $this->shorttext;
		$this->store();
		$newid=$this->_db->insertid();
		$this->load($newid);
		$optgroups=new optiongroups();
		$lst=$optgroups->getgroupsbyid($pid);

		foreach ($lst as $optgroup) {
			$og=new optiongroups();
			$og->load($optgroup->id);
			$og->id=null;
			$og->productid=$newid;
			$og->store();
			$newogid=$og->_db->insertid();
			$opt=new options();
			$lst=$opt->getindoptionids(array($optgroup->id) );
			foreach ($lst as $key=>$value) {
				$o=new options();
				$o->load($value);
				$o->id=null;
				$o->optgroupid=$newogid;
				$o->store();
			}
		}
		$this->prodcode=null; // prep for duplication
	}

	function getproductByProdCode($prodcode) {
		$query="select * from ".$this->_tbl." where prodcode='$prodcode'";
		$this->_db->setQuery($query);
		$p=$this->_db->loadObject();
		$this->load(@$p->id);
		return $p;
	}

	function publishProduct( $cid=null, $publish=1) {
		$cids = implode( ',', $cid );
		$query = "UPDATE ".$this->_tbl
		. "\n SET published = " . intval( $publish )
		. "\n WHERE id IN ( $cids )"
		;
		$this->_db->setQuery( $query );
		$this->_db->query();
	}

	function RemoveProducts($cid=null) {
		$cids = implode( ',', $cid );
		$query = "SELECT `prodcode` FROM `$this->_tbl` WHERE `id` in ( $cids ) ";
		$this->_db->setQuery( $query );
		$prodcodes = $this->_db->loadResultArray();
		$og=new optiongroups();
		$gids=$og->getgroupids($prodcodes);
		$io=new options();
		$lst=$io->getindoptionids($gids);
		foreach ($lst as $key=>$value) {
			$io->deleteindoption($value);
		}
		foreach ($gids as $key=>$value) {
			$og->deletegroup($value);
		}

//		printf("Prodcodes<pre>%s</pre>", print_r($prodcodes, 1));
//		printf("Group IDS<pre>%s</pre>", print_r($gids, 1));
//		printf("Option IDS<pre>%s</pre>", print_r($lst, 1));

		$query = "DELETE FROM ".$this->_tbl." WHERE id IN ( $cids )";
		$this->_db->setQuery( $query );
		$this->_db->query();
	}

	function decfromstore($pid, $qty) {
		$query = "UPDATE ".$this->_tbl." set av_qty= av_qty - $qty WHERE prodcode = '$pid' limit 1";
		$this->_db->setQuery( $query );
		$this->_db->query();
	}
}

class fields extends JTable {
	var $id=null;
	var $name="";
	var $caption="";
	var $type="text";
	var $length=0;
	var $classname="inputbox";
	var $required=0;
	var $ordering;
	var $published=1;
	var $checkfunction="checkfilled";
	var $fieldcontents;

	function fields() {
		$db	= JFactory::getDBO();
	   	$this->__construct( '#__sc_fields', 'id', $db );
	}

	function getField($id) {
		$this->load($id);
	}

	function getFieldByName($name) {
		$query="select `id` from `$this->_tbl` where `name` = '$name' ";
		$this->_db->setQuery($query);
		$id=$this->_db->loadResult();
		$this->load($id);
	}

	function getFieldNames() { // internal use only
		$query="select `name` from ".$this->_tbl;
		$this->_db->setQuery($query);
		return $this->_db->loadColumn();
	}

	function getAllFields() {
		global $mosConfig_list_limit;
        $mainframe=JFactory::getApplication();
		$query="select count(*) as total from ".$this->_tbl;
		$this->_db->setQuery($query);
		$total=$this->_db->loadResult();

		$limit = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
		$limitstart = intval( $mainframe->getUserStateFromRequest( "view{scp}limitstart", 'limitstart', 0 ) );
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit );
		$query="select * from ".$this->_tbl. " order by `ordering` ASC ";
		$this->_db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$lst=$this->_db->loadObjectList();
		if ($this->_db->getErrorNum()==1146) {
			return null;
		}
		$res['lst']=$lst;
		$res['nav']=$pageNav;
		return $res;
	}

	function getPublishedFields() {
		$query="select * from ".$this->_tbl." where published=1 order by `ordering` asc ";
		$this->_db->setQuery($query);
		$lst=$this->_db->loadObjectList();
		return $lst;
	}

	function getPublishedFieldsArray() {
		$query="select name from ".$this->_tbl." where published=1 order by `ordering` asc ";
		$this->_db->setQuery($query);
		$lst=$this->_db->loadResultArray();
		return $lst;
	}

	function saveField() {
		$forbiddenchars=array(" ", ".", ",", "/", "$", "\\");
		$this->bind($_REQUEST);
		$this->name=strtolower($this->name);
		$this->name=str_replace($forbiddenchars,"", $this->name);
		$this->store();
		return $this->id;
	}

	function publishField( $cid=null, $publish=1) {
		$cids = implode( ',', $cid );
		$query = "UPDATE ".$this->_tbl
		. "\n SET published = " . intval( $publish )
		. "\n WHERE id IN ( $cids )"
		;
		$this->_db->setQuery( $query );
		$this->_db->query();
	}

	function RemoveFields($cid=null) {
		$cids = implode( ',', $cid );
		$query = "DELETE FROM ".$this->_tbl." WHERE id IN ( $cids )";
		$this->_db->setQuery( $query );
		$this->_db->query();
	}
}

class config_key extends JTable {
    var $id;
    var $keyword;
    var $description;
    var $setting;
    var $cfgset;
    var $type;
    var $indopts;
    var $sh;
    var $sv;
    var $pagename;

    function config_key() {
		$db	= JFactory::getDBO();
	   	$this->__construct( '#__sc_config', 'id', $db );
    }
}

class sc_configuration {
	var $cfgset=0;

	function sc_configuration($cfgset=0) {
			$this->cfgset=$cfgset;
	}

	function get($kw) {
	$db	= JFactory::getDBO();
		$query="select setting from #__sc_config where keyword='$kw' and cfgset='$this->cfgset'";
		$db->setQuery($query);
		return trim($db->loadResult());
	}

	function getAll() {
	$db	= JFactory::getDBO();
		$query="select * from #__sc_config where `cfgset`='$this->cfgset' order by pagename";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function setAll() {
	$db	= JFactory::getDBO();
		$req=array();
		$req=$_REQUEST;
		foreach ($req as $key=>$value) {
			if (substr($key, 0, 3)=="edt") // only edt* fields
			{
				$cfg=new config_key();
				$setting=substr($key,3,32);
				$query="UPDATE #__sc_config SET `setting`='$value' WHERE keyword='$setting' AND `cfgset`='$this->cfgset' LIMIT 1;";
				$db->setQuery($query);
				$r=$db->query();
			}
		}
	}

	function show() {
		jimport( 'joomla.methods' );
		jimport( 'joomla.html.html.tabs' );
		$options = array(
		    'onActive' => 'function(title, description){
	        description.setStyle("display", "block");
		        title.addClass("open").removeClass("closed");
		    }',
		    'onBackground' => 'function(title, description){
		        description.setStyle("display", "none");
		        title.addClass("closed").removeClass("open");
		    }',
		    'useCookie' => 'true', // note the quotes around true, since it must be a string. But if you put false there, you must not use qoutes otherwise JHtmlTabs will handle it as true
		);

		$cfg=$this->getAll();
		JToolBarHelper::title( JText::_( 'SIMPLECADDY_CONFIGURATION' ));
		JToolBarHelper::custom( 'saveconfig', 'save.png', 'save_f2.png', 'Save', false,  false );
		JToolBarHelper::cancel();
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'Main', false,  false );
	?>
		<form method="post" name="adminForm" action="index.php">
		<?php
		echo JHtml::_('tabs.start', 'tab_group_id', $options);
			$currentpage='';
			$i=0;
			foreach ($cfg as $conf) {
				if ($currentpage<>$conf->pagename) {
					if ($currentpage) {
						echo "</tbody></table>"; //</fieldset>\n";
					}
					$currentpage=$conf->pagename;
					echo JHtml::_('tabs.panel', JText::_($currentpage), "panel_$i");
				//	echo "<fieldset class='adminform'>";
				//	echo "<legend>".JText::_($currentpage)."</legend>";
					echo "\n<table class='admintable' cellspacing='1'><tbody>";
					$i++;
				}

				echo "\n<tr><td class='configkey'>".JText::_($conf->description)."</td>";
				switch ($conf->type) {
				case "text": 	echo "<td><input type=\"text\" name=\"edt$conf->keyword\" value=\"$conf->setting\" size=\"$conf->sh\">";
							echo "</td></tr>\n";
							break;
				case "textarea": echo "<td><textarea name=\"edt$conf->keyword\" cols=\"$conf->sh\" rows=\"$conf->sv\">$conf->setting</textarea>";
						echo "</td></tr>\n";
						break;
				case "richtext": echo "<td>";
				 			editorArea( 'editor1', $conf->setting, "edt$conf->keyword", '100%', '350', '75', '20' ) ;
				 			echo "</td></tr>\n";
							break;
				case "yesno": 	echo "<td>";
                            echo "<select name='edt$conf->keyword'>";
                            echo "\n<option value='0'".($conf->setting==0?" selected":"").">".JText::_('SC_NO')."</option>";
                            echo "\n<option value='1'".($conf->setting==1?" selected":"").">".JText::_('SC_YES')."</option>";
                            echo "\n</select>";
							echo "</td></tr>\n";
							break;
				case "list": 	echo "<td>";
							echo "<select name='edt$conf->keyword'>";
							$txtoptlist=trim($conf->indopts);
							$pairoptlist=explode("\r\n",$txtoptlist);
							foreach ($pairoptlist as $k=>$value) {
								$aline=explode(":", trim($value));
								echo "<option value='".$aline[1]."'".($conf->setting==$aline[1]?" selected":"").">".$aline[0]."</option>\n";
							}
							echo "</select>";
							echo "</td></tr>\n";
							break;
				}
				echo "\n";
			}
		?>
		</td></tr>
		</table>
		<?php echo JHtml::_('tabs.end'); ?>
		<input type="hidden" name="option" value="com_simplecaddy" />
		<input type="hidden" name="action" value="configuration" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
	<?php
	}

}

class order extends JTable {
	var $id;
	var $name;
	var $email;
	var $address;
	var $codepostal;
	var $city;
	var $telephone;
	var $ordercode="";
	var $orderdt;
	var $total;
	var $tax;
var $status;
	var $customfields;
	var $ipaddress;
	var $archive=0;
	var $shipRegion;
	var $shipCost;
    var $j_user_id;
    var $orderlink;
    var $paymentcode;

	function order() {
		$db	= JFactory::getDBO();
	   	$this->__construct( '#__sc_orders', 'id', $db );
	}

	function save($src=false, $orderingFilter = '', $ignore = ''){
		$this->store();
//		echo $this->_db->getQuery();
//		echo $this->_db->getErrorMsg();
		return $this->_db->insertid();
	}

	function orderheadcsv() {
		//$this->to
	}

	function getOrderPresentationHTML($usecontent) {
		// Load user_profile plugin language
		$lang = JFactory::getLanguage();
		$lang->load('com_simplecaddy', JPATH_ADMINISTRATOR);

        $cfg=new sc_configuration();
		$tsep=$cfg->get('thousand_sep');
		$dsep=$cfg->get('decimal_sep');
		$decs=$cfg->get('decimals');
		$currency=$cfg->get('currency');
		$curralign=$cfg->get('curralign');
		$dateformat=$cfg->get('dateformat');
		$timeformat=$cfg->get('timeformat');
		$mode=1; // always html

		if(!$usecontent) {
			$usecontentasemail=$cfg->get("usecidasemail");
			$contentemail=$cfg->get("emailcid");
		} else {
			$usecontentasemail=1;
			$contentemail=$usecontent;
		}


		$hhtml = ""; // header html
		$hhtml .= "<h2>Your Order details</h2>";
		$hhtml .= "\nOrdered On : ".date("$dateformat $timeformat", $orderdt);
		$hhtml .= "\n<br /> Ordered By :$this->name";
		$hhtml .= "\n<br />Address Details";
		$hhtml .= "\n<br />#address1#";
		$hhtml .= "\n<br />$this->city";
		$hhtml .= "\n<br />#postal_code#";
		$hhtml .= "\n<br />Phone : #phone#";
		$hhtml .= "\n<br />Email : $this->email";
		$hhtml .= "\n<br />Order No: #orderid#";
		$hhtml .= "\n<br />PayPal code: #ppref#";
		$hhtml .= "\n<br />Order Status: #status#";
		$hhtml .= "\n<br />Order Code: #ordercode#";

// create html order details block
        $odetails=new orderdetail();
		$detailslist=$odetails->getDetailsByOrderId($this->id);

		$dhtml = "<p>"; // detail html
		$dhtml .= "<table width='100%' border='1'>\n";
		$dhtml .= "<tr><th>".JText::_('SC_CODE')."</th><th>".JText::_('SC_DESCRIPTION')."</th><th>".JText::_('SC_PRICE_PER_UNIT')."</th><th>".JText::_('SC_QUANTITY')."</th><th>".JText::_('SC_TOTAL')."</th></tr>";
		foreach ($detailslist as $detail) {
			$desc = $detail->shorttext;
			if(!$detail->option == "-") {
				$desc .= " - ".$detail->option;
				}
			$dhtml .= "<tr><td>$detail->prodcode</td>\n";
			$dhtml .= "<td>$desc</td>\n";
			$dhtml .= "<td>".number_format($detail->unitprice, $decs, $dsep, $tsep)."</td>\n";
			$dhtml .= "<td>$detail->qty</td>\n";
			$dhtml .= "<td><strong>".number_format($detail->qty*$detail->unitprice, $decs, $dsep, $tsep)."</strong></td>\n";
		}

		$dhtml .= "<tr><td colspan='2'><td colspan='2'><strong>".JText::_('SC_TOTAL')."</strong></td>";
		$dhtml .= "<td><strong>".number_format($this->total, $decs, $dsep, $tsep)."</strong></td></tr>\n";
		$dhtml .= "</table>\n";
		$dhtml .= "</p>";
		if ($usecontentasemail==1) {
			$db	= JFactory::getDBO();
			$query="select introtext from #__content where id = '$contentemail'";
			$db->setQuery($query);
			$content=$db->loadResult();
		}
		else
		{
			$content=$hhtml.$dhtml;
		}
		$fields=new fields();
		$fieldslist=$fields->getPublishedFields() ;// the custom fields defined for this system
		$thefields=unserialize($this->customfields); // the fields filled by customers
		foreach ($fieldslist as $key=>$customfield) {
			$thefields[$customfield->name]=str_replace("\'","'",$thefields[$customfield->name]);
			$content=str_replace("#".$customfield->name."#", $thefields[$customfield->name], $content); // replace custom tags with the field names
		}
		$content=str_replace("#orderheading#",$hhtml, $content); // replace the headertag with header html
		$content=str_replace("#orderdetails#",$dhtml, $content); // replace detail tag with detail html
		$content=str_replace("#orderid#",$this->id, $content); // replace orderid tag with the order ID
		$content=str_replace("#status#",$this->status, $content); // replace orderid tag with the order ID
		$content=str_replace("#ppref#",$this->paymentcode, $content); // replace orderid tag with the order ID
		$content=str_replace("#ordercode#",$this->ordercode, $content); // replace orderid tag with the order ID
		$content=str_replace("#orderdt#",date("$dateformat $timeformat", $this->orderdt), $content); // replace orderid tag with the order ID
		return $content;

	}

	function ordertostring($cids) {
		$field=new fields();
		$aflds=$field->getFieldNames();
		$afields=unserialize($this->customfields);
		$this->afields=$aflds;
		foreach ($aflds as $key=>$value) {
			$this->$value=$afields["$value"];
		}
		$csv="";
		$fldsep="|";
		$recsep="\r\n";
		$csvheader = "orderid".$fldsep."orderdate".$fldsep."total".$fldsep."tax".$fldsep."Shipping Cost".$fldsep."Shipping Region".$fldsep."status";
		$csvheader .= $fldsep . "productcode".$fldsep."qty".$fldsep."unitprice".$fldsep."total".$fldsep."shorttext".$fldsep."option";
		foreach ($aflds as $key=>$value) {
			$csvheader .= $fldsep . "$value";
		}
		$csvheader .= $recsep;

		$f=fopen("components/com_simplecaddy/exports/export.txt", "w+");
		fwrite($f, $csvheader);
		foreach ($cids as $key=>$orderid) {
			$this->load($orderid);
//			printf("<pre>%s</pre>", print_r($this, 1));
			$csvline="$this->id".$fldsep."$this->orderdt".$fldsep."$this->total".$fldsep."$this->tax".$fldsep."$this->shipCost".$fldsep."$this->shipRegion".$fldsep."$this->status".$fldsep;
//			foreach ($aflds as $key=>$value) {
//				$csvline .= $this->$value . $fldsep ;
//			}

			$detlin="";
			$details=new orders();
			$lst=$details->getOrderDetails($this->id);
			$afields=unserialize($this->customfields);
			foreach ($lst as $d) {
				$detlin .= $csvline . $d->prodcode . $fldsep . $d->qty . $fldsep . $d->unitprice . $fldsep . $d->total . $fldsep . $d->shorttext . $fldsep . $d->option ;
				foreach ($aflds as $key=>$value) {
					$detlin .= $fldsep .$afields["$value"] ;
				}
				$detlin .= $recsep;
				fwrite($f, $detlin);
				$detlin="";
			}
		}

		fclose($f);
	//	$csvline .= $recsep;

		$csv=$csvheader;
		$csv .= $detlin;

		return $csv;
	}

	function setOrderTotals($orderid) {
		$this->load($orderid);
		$dets=new orderdetail();
		$lst=$dets->getDetailsByOrderId($orderid);
		$tax=0;
		$shipping=0;
		$total=0;
		foreach ($lst as $d) {
			switch($d->prodcode) {
				case "shipping":
					$shipping += $d->unitprice;
					break;
				case "tax":
					$tax += $d->unitprice;
					break;
				default:
					$total += $d->total;
			}
		}
		$this->tax=$tax;
		$this->shipCost=$shipping;
		$this->total=$total;
		$this->store();
	}
}

class orderdetail extends JTable {
    var $id;
    var $orderid;
    var $prodcode;
    var $qty;
    var $unitprice;
    var $total;
   var $shorttext;
    var $option;

	function orderdetail() {
		$db	= JFactory::getDBO();
	   	$this->__construct( '#__sc_odetails', 'id', $db );
	}

    function getDetailsByOrderId($orderid){
        $query="select * from {$this->_tbl} where `orderid` = '$orderid'";
        $this->_db->setQuery($query);
        $lst=$this->_db->loadObjectList();
        return $lst;
    }

    function isinorder($orderid, $prodcode) {
    	$query="select `id` from `$this->_tbl` where `orderid` = '$orderid' and `prodcode`='$prodcode' limit 1";
    	$this->_db->setQuery($query);
    	$id=$this->_db->loadResult();
    	$this->load($id);
    }

}

class orders {
	function store_new_order($cart) {
    	if (count($cart)==0) return;
    	//get statuses
    	$cfg=new sc_configuration();
    	$statuses=explode("\n", trim($cfg->get("ostatus")));
        // get the first status from the list
    	$status=(isset($statuses[0])?trim($statuses[0]):JText::_('SC_NO_STATUS') );

        $juser=JFactory::getUser();

    	//create order info from the details page
    	$o=new order();
    	$o->bind($_POST);
    	$o->id=null; // ensure a new order is created here
        $o->j_user_id=$juser->id; // add the user id
    	$o->orderdt=mktime();
    	$o->status=$status;
    	$o->customfields=serialize($_POST);

    	$o->ordercode=substr(md5(mktime()),0,15); // should be pretty unique for an order
    	$o->orderlink=JURI::base( true )."/index.php?option=com_simplecaddy&ordercode=$o->ordercode"; // incomplete so that other functions can redirect elsewhere with this code
    	$orderid=$o->save();

    	$gtotal=0;
    	$autodec=$cfg->get("autodecfromstore");
    	foreach ($cart as $key=>$product) {
    		unset($odet);
    		$odet=new orderdetail();
    		$odet->id=null;
    		$odet->orderid=$orderid;
    		$odet->prodcode=$product->prodcode;
    		$odet->qty=$product->quantity;
    		$odet->unitprice=$product->finalprice;
    		$odet->total=$product->quantity*$product->finalprice;
    		$odet->shorttext=$product->prodname;
    		$odet->option=$product->option;
            $odet->store();
    		$gtotal=$gtotal+$odet->total;
    		if ($autodec==1) { // auto decrement from store
	    		$pr=new products();
	    		$pr->decfromstore($product->prodcode, $product->quantity);
    		}
    	}
		$o = new order(); // reload the order
        $o->load($orderid);

    	$o->total=$gtotal; // update its grand total
        $o->store(); // store the order with new total
   		return $orderid;
	}


	function getAllOrders($field=null, $type='', $special=0, $filter=null, $archive=0) {
	global $mosConfig_list_limit, $mosConfig_absolute_path, $option;
    $mainframe=JFactory::getApplication();
	$db	= JFactory::getDBO();
		$query="select * from #__sc_orders where archive=$archive ";

		if ($filter) {
			if (is_numeric($filter)) {
				$query .= " and #__sc_orders.id = '$filter' ";
			}
			else
			{
				$query .= " and name like '%$filter%' ";
			}
		}

		if ($field) {
			$query .= " order by `$field` $type";
		}
		$db->setQuery($query);
		$lst=$db->loadObjectList();

		$limit = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
		$limitstart = intval( $mainframe->getUserStateFromRequest( "view{orders}limitstart", 'limitstart', 0 ) );
		$total=count($lst);

		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit );

		$db->setQuery($query, $limitstart, $limit);
		$lst=$db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->getErrorMsg();
			echo $db->getQuery();
		}
		$res=array();
		$search	= $mainframe->getUserStateFromRequest( 'search', 'search', '', 'string' );
		$search	= JString::strtolower($search);
		$res['search'] = $search;
		$res['lst']=$lst;
		$res['nav']=$pageNav;
		return $res;
	}

	function getfilteredOrderList($filter=null) {
		$db	= JFactory::getDBO();
		$query="select * from #__sc_orders where archive=0 ";

		if ($filter) {
				$query .= " and status = '$filter' ";
		}

		$db->setQuery($query);
		$lst=$db->loadObjectList();

		if ($db->getErrorNum()) {
			echo $db->getErrorMsg();
			echo $db->getQuery();
		}

		return $lst;
	}



	function getOrderIdFromCart($ordercode) {
		$db= JFactory::getDbo();
		$query="select `id` from `#__sc_orders` where `ordercode` = '$ordercode' limit 1; ";
		$db->setQuery($query);
		$id=$db->loadResult();
		return $id;
	}

	function getorder($id) {
		$db	= JFactory::getDBO();
		$query="select * from #__sc_orders where id='$id'";
		$db->setQuery($query);
		$p=$db->loadObject();
		return $p;
	}

	function getOrderDetails($orderid) {
		global $mainframe, $mosConfig_list_limit, $mosConfig_absolute_path, $option;
		$db	= JFactory::getDBO();
		$query="select * from #__sc_odetails where `orderid`='$orderid'";
		$db->setQuery($query);
		$lst=$db->loadObjectList();
		return $lst;
	}

	function getODetails($id) {
		global $mosConfig_list_limit, $mosConfig_absolute_path, $option;
		$mainframe=JFactory::getApplication();
		$db	= JFactory::getDBO();
		$query="select * from #__sc_odetails where orderid='$id'";
		$db->setQuery($query);
		$lst=$db->loadObjectList();

		$limit = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
		$limitstart = intval( $mainframe->getUserStateFromRequest( "view{items}limitstart", 'limitstart', 0 ) );
		$total=count($lst);

		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit );

		$db->setQuery($query, $limitstart, $limit);
		$lst=$db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->getErrorMsg();
			echo $db->getQuery();
		}
		$res=array();
		$res['lst']=$lst;
		$res['nav']=$pageNav;
		return $res;
	}

	function RemoveOrders($cid=null) {
		$db	= JFactory::getDBO();
		//remove the orders
		$cids = implode( ',', $cid );
		$query = "DELETE FROM #__sc_orders WHERE id IN ( $cids )";
		$db->setQuery( $query );
		$db->query();
		$query = "DELETE FROM #__sc_odetails WHERE orderid IN ( $cids )";
		$db->setQuery( $query );
		$db->query();
	}

	function saveOrder($id=null) {
		//save an edited order. only field changed is the status!
		$db	= JFactory::getDBO();
		if(!$id) {
			$id=JRequest::getVar( 'id', 'cp error');
			}
		$status=JRequest::getVar( "edtostatus");
		$req=$_POST;

		//remove spurious VARs
		unset($req["edtostatus"]);
		unset($req["id"]);

//		if(in_array("option", $req))
		unset($req["option"]);
//		if(in_array("action", $req))
		unset($req["action"]);
//		if(in_array("task", $req))
		unset($req["task"]);
//		if(in_array("order", $req))
		unset($req["order"]);
//		if(in_array("field", $req))
		unset($req["field"]);
//		if(in_array("boxchecked", $req))
		unset($req["boxchecked"]);
//		if(in_array("hidemainmenu", $req))
		unset($req["hidemainmenu"]);

		$archive=JRequest::getVar("archive");
		$order=$this->getorder($id);
		$order->status=$status;
		$order->customfields=serialize($req);
		$db->updateObject("#__sc_orders", $order, "id");
	}

}



class scdl_items extends JTable {
	var $id;
	var $filename;
	var $paymentkey;
	var $datetime;

	function scdl_items() {
		$db	= JFactory::getDBO();
	   	$this->__construct( '#__sc_downloads', 'id', $db );
	}

	function getlist($paymentkey) {
		$query="select `id`,`filename` from $this->_tbl where `paymentkey`='$paymentkey';";
		$this->_db->setQuery($query);
		$s=$this->_db->loadObjectList();
		return $s;
	}

}

class scdl {
	var $redirect=null;
	var $message=null;
	var $data;

	function redirect() {
		$mainframe=JFactory::getApplication();
		if($this->redirect)
		$mainframe->redirect("$this->redirect", "$this->message");
	}

	function view() {
		$key=JRequest::getVar("dlkey");
		if (empty($key)) {
			$this->display("keyform");
			return;
		}

		$cfg=new sc_configuration();
		$dlcid=$cfg->get("downloadcid");
		$this->redirect="index.php?option=com_content&view=article&id=$dlcid&dlkey=$key";
		$this->message="";

}

	function display($view='default') {
		include_once("views".DS."$view.php");
	}

	function createdlkey($orderid) {
		$order=new order();
		$order->load($orderid);
		$dlkey=$order->ordercode;
		$oi=new orderdetail();
		$lst=$oi->getDetailsByOrderId($orderid);
		$now=mktime();
		foreach ($lst as $detail) {
			// get the product
			$product=new products();
			$product->getproductByProdCode($detail->prodcode);
			if ($product->downloadable==1) {
				$dl=new scdl_items();
				$dl->id=null; // ensure new item
				$dl->datetime=$now;
				$dl->filename=$product->filename;
				$dl->paymentkey=$dlkey;
				$dl->store();
			}
		}
		return $dlkey;
	}
}

class scplugins {
	function getlist() {
		$db= JFactory::getDbo();
		$query=" select `extension_id`, `enabled`, `element`, `name` from `#__extensions` where `params` like '%scregistration%' ";
		$db->setQuery($query);
		$lst=$db->loadObjectList();
		return $lst;
	}

	function showpluginconfig() {
		$pluginname=JRequest::getVar("pluginname");
		$path=JPATH_PLUGINS."/content/$pluginname/$pluginname.php";
		if (! file_exists($path) ) {
			$msg=JText::_("SC_NO_PLGCONFIG");
			$mainframe= JFactory::getApplication();
			$mainframe->redirect("index.php?option=com_simplecaddy&action=plugins&task=show", $msg);
		}
		require_once($path);
		$pluginclass="{$pluginname}configuration";
		$plg=new $pluginclass();
		$plg->_showconfig();
	}

	function pluginfunction($pluginname, $functionname) {
		$pluginname=JRequest::getVar("pluginname");
		$path=JPATH_PLUGINS."/content/$pluginname/$pluginname.php";
		require_once($path);
		$pluginclass="{$pluginname}configuration";
		$plg=new $pluginclass();
		$plg->$functionname();
	}

	function enableplugin() {
		$enabled=JRequest::getVar("enable");
		$extension_id=JRequest::getVar("id");
		$db = JFactory::getDbo();
		$query="update `#__extensions` set `enabled` = '$enabled' where `extension_id` = '$extension_id' limit 1;";
		$db->setQuery($query);
		$db->query();
		$mainframe= JFactory::getApplication();
		$mainframe->redirect("index.php?option=com_simplecaddy&action=plugins&task=show");
	}
}


?>
