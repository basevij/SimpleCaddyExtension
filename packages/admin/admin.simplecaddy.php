<?php
/**
* @package SimpleCaddy 2.0 for Joomla 2.5
* @copyright Copyright (C) 2006-2012 Henk von Pickartz. All rights reserved.
* Main admin file
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$debug=0;
$mainframe=JFactory::getApplication();

require_once( JPATH_COMPONENT_ADMINISTRATOR.DS. 'admin.simplecaddy.html.php' );
require_once( JPATH_COMPONENT_SITE.DS. 'simplecaddy.class.php' );
require_once( JPATH_COMPONENT.DS.'admin.simplecaddy.pg.html.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS. 'admin.simplecaddy.content.html.php' );
require_once( JPATH_COMPONENT_SITE.DS.'simplecaddy.pg.class.php' );
require_once( JPATH_COMPONENT_SITE.DS.'simplecaddy.content.class.php' );

if (version_compare( JVERSION, '2.5', '>=' ) == 0 ) {
	$mainframe->redirect("index.php", JText::_("SC_VERSION_TOO_LOW"));
}

$input=new JInput();

$action=JRequest::getCmd( 'action' );
$task=JRequest::getCmd( 'task', 'show' );
$cid=JRequest::getVar( 'cid', array(0), '', 'array' );
JArrayHelper::toInteger($cid, array(0));

if (($task=="control")) {
	
	display::MainMenu();
	if ($debug) echo "<p>Development debug info</p><strong>task= '$task'<br>action='$action'</strong>";
	if (!JRequest::getvar('no_html')) {
		display::afFooter();
	}
	return;
}

switch ($action) {
case "orders":
	$search=JRequest::getVar("search");
	$field=JRequest::getVar("field");
	$order=JRequest::getVar("order");
	switch ($task) {
	case "apply":
	case "save":
		$cid[0]=JRequest::getVar("id");
		JRequest::setvar('task', "orders");
		$tmp=new Orders();
		$tmp->saveorder();
		if ($task=="save")
			$mainframe->redirect("index.php?option=com_simplecaddy&action=orders&task=show&field=$field&order=$order&search=$search", JText::_('SC_ORDERSAVED'));
	case "edit":
	case "view":
		$tmp= new orders();
		$order=$tmp->getorder($cid[0]);
		$details=$tmp->getODetails($cid[0]);
		display::editOrder($order, $details['lst'], $details['nav']);
		break;
	case "remove":
		JRequest::setvar('task', "orders");
		$tmp=new Orders();
		$tmp->removeOrders($cid);
		$mainframe->redirect("index.php?option=com_simplecaddy&action=orders&task=show&field=$field&order=$order&search=$search", JText::_('SC_ORDERDELETED'));
		break;
	case "export":
		$order=new order();
		$csvline=$order->ordertostring($cid);
		JRequest::setvar('task', "about");
		display::showExport();
		break;
	case "email":
	printf("<pre>%s</pre>", print_r($_REQUEST, 1));
		$oid=JRequest::getVar("oid");
		$email=new email();
		$email->mailorder($oid);
		$mainframe->redirect("index.php?option=com_simplecaddy&action=orders&task=edit&id=$oid&field=$field&order=$order&search=$search", JText::_('SC_EMAIL_SENT'));
		break;
	case "viewarchive":
		JRequest::setvar('task', "orders");
		$a=new Orders();
		$alist=$a->getAllOrders($field, $order, null, $search, 1);
		display::ShowOrders($alist, $field, $order);
		break;
	case "show":
	default:
//		JRequest::setvar('task', "orders");
		$a=new Orders();
		$alist=$a->getAllOrders($field, $order, null, $search);
        display::header();
		display::ShowOrders($alist, $field, $order);
		break;
	}
	break;
case "products":
	$search=JRequest::getVar("search");
	$field=JRequest::getVar("field");
	$order=JRequest::getVar("order");
	switch ($task) {
		case "apply":
			$tmp=new products();
			$tmp->saveproduct();
			$cid[0]=$tmp->id;
		case "add":
		case "edit":
			$p= new products();
			$p->getproduct($cid[0]);
			display::editProduct($p);
			break;
		case "duplicate":
			$p= new products();
			$p->duplicate($cid[0]);
			
            //todo: copy the options as well
			JRequest::setvar('task', "edit");
			display::editProduct($p);
			break;
		case "save":
			$tmp=new products();
			$res=$tmp->saveproduct();
			$msg= JText::_('SC_PRODUCTSAVED');
			$mainframe->redirect("index.php?option=com_simplecaddy&action=products&task=show&field=$field&order=$order&search=$search", $msg);
			break;
		case "decstore":
			$pid=JRequest::getCmd( 'pid' );
			$qty=JRequest::getCmd( 'qty' );
			$oid=JRequest::getCmd( 'order' );
			$tmp=new products();
			$tmp->decfromstore($pid, $qty);
	
			//now get back to this order in edit/view mode
			JRequest::setvar('task', "edit");
			$tmp= new orders();
			$order=$tmp->getorder($oid);
			$details=$tmp->getODetails($oid);
			display::editOrder($order, $details['lst'], $details['nav']);
			break;
		case "remove":
			$tmp=new products();
			$tmp->removeProducts($cid);
			$mainframe->redirect("index.php?option=com_simplecaddy&action=products&task=show&field=$field&order=$order&search=$search", JText::_('SC_PRODUCTDELETED'));
			break;
		case "publish":
		case "unpublish":
			$a=new products();
			$a->publishproduct($cid, ($task == 'publish'));
			$mainframe->redirect("index.php?option=com_simplecaddy&action=products&task=show&field=$field&order=$order&search=$search");
			break;
        case "addoptgroup":
            $prodid=JRequest::getVar("id");
            $prodcode=JRequest::getVar("prodcode");
            $og=new optiongroups();
            $og->addgroup($prodcode);
			$mainframe->redirect("index.php?option=com_simplecaddy&action=products&task=edit&cid[0]=$prodid");
            break;
		case "show":
		default:
			JRequest::setvar('task', "products");
			$a=new products();
			$alist=$a->getAllProducts($search, $field, $order);
			display::ShowProducts($alist, $field, $order);
			break;
		}
	break;
case "fields":
	$search=JRequest::getVar("search");
	$field=JRequest::getVar("field");
	$order=JRequest::getVar("order");
	switch ($task) {
		case "apply":
			$fields=new fields();
			$fields->savefield();
			$cid[0]=$fields->id;
		case "add":
		case "edit":
			$fields= new fields();
			$fields->getfield($cid[0]);
			display::editField($fields);
			break;
		case "save":
			$fields=new fields();
			$fields->saveField();
			$mainframe->redirect("index.php?option=com_simplecaddy&action=fields&task=show&field=$field&order=$order&search=$search", JText::_('SC_FIELDSAVED'));
			break;
		case "remove":
			$fields=new fields();
			$fields->removeFields($cid);
			$mainframe->redirect("index.php?option=com_simplecaddy&action=fields&task=show&field=$field&order=$order&search=$search", JText::_('SC_FIELDDELETED'));
			break;
		case "publish":
		case "unpublish":
			$fields=new fields();
			$fields->publishfield($cid, ($task == 'publish'));
			$mainframe->redirect("index.php?option=com_simplecaddy&action=fields&task=show&field=$field&order=$order&search=$search");
			break;
		case "show":
		default:
			JRequest::setvar('task', "fields");
			$fields=new fields();
			$alist=$fields->getAllFields();
			display::showFields($alist);
			break;
		}
	break;
case "options":
case "optiongroups":
	if (!$task) $task="show"; // make sure we have a task value here
	$c=new $action(); 
	$c->$task(); // execute the task 
    // no redirect!
	break;
case "sccontents":
case "scphocag":
	if (!$task) $task="show"; // make sure we have a task value here
	$c=new $action(); 
	$c->$task(); // execute the task 
	$c->redirect(); // redirect if set in the class
	break;
case "configuration":
	switch ($task) {
	case "saveconfig":
		$cfgset=JRequest::getvar('cfgset');
		JRequest::setvar('task', "configuration");
		$cfg=new sc_configuration($cfgset);
		$cfg->setAll();
		$mainframe->redirect("index.php?option=com_simplecaddy&action=configuration&task=show", JText::_("Configuration saved"));
	case "cancel":
		$mainframe->redirect("index.php?option=com_simplecaddy&action=configuration&task=show", JText::_("Reverting to previous Configuration"));
	default:
		$cfgset=JRequest::getvar('cfgset');
		JRequest::setvar('task', "configuration");
		$cfg=new sc_configuration($cfgset);
		display::header();
		$cfg->show();
		break;
	}
	break;
case "view_prod":
$name=JRequest::getVar("name");
	$a=new products();
	$alist=$a->getPublishedProducts();
	$cfg=new sc_configuration();
	$scats=$cfg->get("prodcats");
	$catlist=explode("\r\n", $scats);
	$content=new sccontent();
	$clist=$content->getlist();
	display::view_prod($alist, $name, $catlist, $clist);
	break;
case "insertplugincode":
	break;
case "about":
	switch ($task) {
	default:
		JRequest::setvar('task', "about");
		display::ShowAbout();
		break;
	}
	break;
case "plugins":
	$pl=new scplugins();
	$lst=$pl->getlist();
	display::showPluginlist($lst);
	break;
case "pluginconfig":
	$pl=new scplugins();
	if (method_exists($pl, $task)) {
		$pl->$task();
	}
	else
	{
		$pluginname=$input->get("pluginname");
		$pl->pluginfunction($pluginname, $task);
	} 
	break;
default:
	JRequest::setvar('task', "control");
	display::MainMenu();
}


if ($debug) echo "<p>Development debug info</p><strong>task= '$task'<br>action='$action'</strong>";
if (!JRequest::getvar('no_html')) {
	display::afFooter();
}

?>
