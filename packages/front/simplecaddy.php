<?php
/**
* @package SimpleCaddy 2.0.4 for Joomla 2.5
* @copyright Copyright (C) 2006-2013 Henk von Pickartz. All rights reserved.
* SimpleCaddy frontent processing plant
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.router');

// Load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load('com_simplecaddy', JPATH_SITE);

$mainframe=JFactory::getApplication();

require_once( JPATH_COMPONENT_SITE . '/simplecaddy.html.php' );
require_once( JPATH_COMPONENT_SITE . '/simplecaddy.class.php' );
require_once( JPATH_SITE . '/components/com_content/helpers/route.php');
require_once( JPATH_COMPONENT_SITE.'/simplecaddy.pg.class.php' );
require_once( JPATH_COMPONENT_SITE.'/simplecaddy.content.class.php' );

$user = JFactory::getUser();

$Itemid = intval( JRequest::getVar( 'Itemid' ) );
$action=JRequest::getCmd( 'action', '');
$task=JRequest::getCmd( 'task', '');
$thiscid=JRequest::getVar("thiscid");
$nextcid=JRequest::getVar("nextcid", null);
$data = JRequest::getVar( 'data');
$id = intval( JRequest::getVar( 'id' ) );

//printf("<pre>%s</pre>", print_r($_REQUEST, 1));

switch ($action) {
	case "skip":
		$sp=new sccontent();
		$sp->load($nextcid);
		$url =JUri::root(). ContentHelperRoute::getArticleRoute($sp->id. ":". $sp->alias, $sp->catid) ;
		if (strpos($url, "?")>0) {
			$url .= "&data=".$data;
		}
		else
		{
			$url .= "?data=".$data;
		}
		$mainframe->redirect($url);
		break;
	case "addtocart":
		$cfg=new sc_configuration();
		$cartProd=new CartProduct();
        $cartProd->option="";
        $cartProd->formula="";
        $cartProd->caption="";

		$acpoption=JRequest::getVar( 'edtoption' );
		$acpoption2=JRequest::getVar( 'edtoption2' );
		$picname=JRequest::getVar("picname");
		$minqty=JRequest::getVar("minqty");
		$cartProd->prodcode=JRequest::getCmd( 'edtprodcode', 'cp error');
        if (is_array($acpoption)) {
			$prodoptions=new productoption();
			foreach ($acpoption as $key=>$value) {
				if (strpos($value, ":")>0) { // standard option
					$cpoption=explode(":", $value);
					$prodoptions->load($cpoption[1]);
				}
				else
				{ // text type option, in fact has no options, just fill the object
					$cartProd->option=$value;
				}

				$cartProd->option .= $prodoptions->description . "-";
				$cartProd->formula .= $prodoptions->formula;
				$cartProd->caption .= $prodoptions->caption;
				$cartProd->md5id .= md5($cartProd->prodcode.$prodoptions->description.$picname);
			}
        }
        else
        {
            $cartProd->md5id=$cartProd->prodcode.$acpoption;
        }

        @$cartProd->option .= " - ". $picname;
		$cartProd->prodname=JRequest::getVar( 'edtshorttext', 'txt error');
		// added security => retrieve original product from DB and not from session vars
		$product=new products();
		$p=$product->getproductByProdCode($cartProd->prodcode);
		$cartProd->unitprice=$p->unitprice; // retrieved from DB, not from session
		$cartProd->quantity=abs(JRequest::getInt( 'edtqty' ) ); // restrict to positive integer values, fractions not allowed
        if ($cartProd->quantity < $minqty ) $cartProd->quantity = $minqty;
		$cartProd->finalprice= matheval("$cartProd->unitprice $cartProd->formula");

		// check for minimum order quantities
		if ($cfg->get("checkoos")==1) { // check if we need to check
			$product=new products();
			$p=$product->getproductByProdCode($cartProd->prodcode);
			$cart=new cart2();
			$cp=$cart->getCartProduct($p);
			if ( ($p->av_qty + $cp->quantity) < $cartProd->quantity) { // if less than minimum quantity ordered
				$lasturl="index.php?option=com_simplecaddy&action=showcart&Itemid=$Itemid";
				$mainframe->redirect($lasturl, JText::_("SC_MINQTY_WARNING")); // stay on the same page and issue a warning message
			}
		}

		$cart2=new cart2();
		$cart2->addCartProduct($cartProd); // add the product to the cart
		$usestdprod=$cfg->get("usestdproduct");
		if ($usestdprod==1) {
			AddStandard();
		}
		if ($nextcid) {
			$sp=new sccontent();
			$sp->load($nextcid);
			$url =JUri::root(). ContentHelperRoute::getArticleRoute($sp->id. ":". $sp->alias, $sp->catid) ;
			$mainframe->redirect($url);
		}
		else
		{ // go back to the "previous" page. php does not know how to do this so rely on javascript :-(
			?>
			<script>window.history.go(-1)</script>
			<?php
		}
		break;
	case "changeqty":
		$prodcode=JRequest::getVar( 'edtprodcode');
		$product=new products();
		$p=$product->getproductByProdCode($prodcode);

		$cartProd=new CartProduct();
		$cartProd->md5id=JRequest::getVar( 'edtid');
		$cartProd->prodcode=$prodcode;
		$cartProd->quantity=abs(JRequest::getInt( 'edtqty' )); // restrict to positive integer values
		$cartProd->unitprice=$p->unitprice; // retrieve price from DB not from session vars
		$cartProd->finalprice= matheval("$cartProd->unitprice $cartProd->formula"); // recalculate price

		$cfg=new sc_configuration();
		if ($cfg->get("checkoos")==1) {
			// check for available quantity before making the change in the cart
			$product=new products();
			$p=$product->getproductByProdCode($cartProd->prodcode);
			if ($p->av_qty < $cartProd->quantity) {
				$lasturl="index.php?option=com_simplecaddy&action=showcart&Itemid=$Itemid";
				$mainframe->redirect($lasturl,JText::_("SC_MINQTY_WARNING"));
			}
		}
		$cart2=new cart2();
		$cart2->setCartProductQty($cartProd);

		$c=$cart2->getCartNumbers();

		if ($c==1 and $cart2->isInCart("coupon")) {
			$cart2->destroyCart();
		}

		$stdprod=$cfg->get("cart_fee_product");
		if ($c==2 and ($cart2->isInCart("coupon") and ($cart2->isInCart("$stdprod"))) ) {
			$cart2->destroyCart();
		}

		$cfg = new sc_configuration();
		$usestdprod=$cfg->get("usestdproduct");
		if ($usestdprod==1) {
			AddStandard();
		}
		$sp=new sccontent();
		$sp->load($thiscid);
		$url =JUri::root(). ContentHelperRoute::getArticleRoute($sp->id. ":". $sp->alias, $sp->catid) ;
		$mainframe->redirect($url);
		break;
	case "emptycart":
		$cart2=new cart2();
		$cart2->destroyCart();
		$sp=new sccontent();
		$sp->load($thiscid);
		$url =JUri::root(). ContentHelperRoute::getArticleRoute($sp->id. ":". $sp->alias, $sp->catid) ;
		$mainframe->redirect($url);
		break;
	case "showcartxml":
		$cart2=new cart2();
		$cart=$cart2->dumpCartXML();
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
	case "email_order":
		$usecid=JRequest::getVar("usecid");
		$tmp= new orders();
		$oid=$tmp->getOrderIdFromCart($data);
		$email=new email();
		$email->mailorder($oid, $usecid);
		$sp=new sccontent();
		$sp->load($nextcid);
		$url =JUri::root(). ContentHelperRoute::getArticleRoute($sp->id. ":". $sp->alias, $sp->catid) ;
		$input=new JInput();
		if (strpos($url, "?")>0) {
			$url .= "&data=".$data;
		}
		else
		{
			$url .= "?data=".$data;
		}
		$mainframe->redirect($url, JText::_('SC_EMAIL_SENT'));
		break;
	case "saveorder":
		$oid=JRequest::getVar("oid");
		JRequest::setvar('task', "orders");
		$tmp=new Orders();
		$tmp->saveorder($oid);
		$sp=new sccontent();
		$sp->load($nextcid);
		$url =JUri::root(). ContentHelperRoute::getArticleRoute($sp->id. ":". $sp->alias, $sp->catid) ;
		$input=new JInput();
		if (strpos($url, "?")>0) {
			$url .= "&data=".$data;
		}
		else
		{
			$url .= "?data=".$data;
		}

		$mainframe->redirect($url);
		break;

	case "view_order":
		$oid=JRequest::getVar("oid");
		$tmp= new orders();
		$order=$tmp->getorder($oid);
		$sp=new sccontent();
		$sp->load($nextcid);
		$url =JUri::root(). ContentHelperRoute::getArticleRoute($sp->id. ":". $sp->alias, $sp->catid) ;
		$input=new JInput();
		$data=$order->ordercode;
		if (strpos($url, "?")>0) {
			$url .= "&data=".$data;
		}
		else
		{
			$url .= "?data=".$data;
		}


		$mainframe->redirect($url);
		break;
	case "checkout":
		$cart=new cart2();
		$cfg=new sc_configuration();
		$mintocheckout=$cfg->get("mincheckout");
		// check for minimum amount before checkout, default = 0 => any amount is enough
		if (!$mintocheckout) $mintocheckout=0;
		$carttotal= $cart->getCartTotal();
		if ( $carttotal < $mintocheckout ) {
			$txt=JText::_('SC_LESS_THAN_MIN_AMOUNT', $mintocheckout );
			$sp=new sccontent();
			$sp->load($thiscid);
			$url =JUri::root(). ContentHelperRoute::getArticleRoute($sp->id. ":". $sp->alias, $sp->catid) ;
			$mainframe->redirect($url, $txt);
		}
		$sp=new sccontent();
		$sp->load($nextcid);
		$url =JUri::root(). ContentHelperRoute::getArticleRoute($sp->id. ":". $sp->alias, $sp->catid) ;
		$input=new JInput();
		$data=$input->get("data");
		if (strpos($url, "?")>0) {
			$url .= "&data=".$data;
		}
		else
		{
			$url .= "?data=".$data;
		}

		$mainframe->redirect($url);
		break;
	case "allconfirm":
		$errors=checkerrors();
		$cfg=new sc_configuration();
		if ($errors==0) {
			$cart=new cart2();
			$mycart=$cart->readCart();
			// store the order in db
			$order=new orders();
			$orderid = $order->store_new_order($mycart);
			$scdl=new scdl();
			$dlkey=$scdl->createdlkey($orderid); // inscribes the downloadables in the *sc_downloads table and returns the download key
//			$cart=new cart2();
	//		$cart->destroyCart(); // empty all session vars of the cart, no visual return
			$sp=new sccontent();
			$sp->load($nextcid);
			$url =JUri::root(). ContentHelperRoute::getArticleRoute($sp->id. ":". $sp->alias, $sp->catid) ;
			// now go to shipping, taxes or checkout... anyway deliver the order code as well
			if (strpos($url, "?")>0) {
				$url .= "&data=".$dlkey;
			}
			else
			{
				$url .= "?data=".$dlkey;
			}
			$mainframe->redirect($url);
		}
		else // some required info is missing or incorrect
		{
			$fields=new fields();
			$fieldlist=$fields->getPublishedFields();
			$fielddata=$_POST; // get posted fields back
			$encfielddata=base64_encode(serialize($fielddata));
			$sp=new sccontent();
			$sp->load($thiscid);
			$url =JUri::root(). ContentHelperRoute::getArticleRoute($sp->id. ":". $sp->alias, $sp->catid) ;
			if (strpos($url, "?")>0) {
				$url .= "&data=".urlencode($encfielddata);
			}
			else
			{
				$url .= "?data=".urlencode($encfielddata);
			}
			$mainframe->redirect($url, JText::_('SC_REQUIRED_MISSING'));
		}
		break;
	default:
	if (class_exists($action)) {
		$c=new $action();
		if (!$task) $task="view";
		if (method_exists($action, $task)) {
			$c->$task();
			if ($c->redirect != "") $c->redirect();
		}
		else
		{
			$input=new JInput();
			$pl=new scplugins();
			$pluginname=$input->get("pluginname");
			$pl->pluginfunction($pluginname, $task);
		}
	}
}

function AddStandard_example () {
	$cfg=new sc_configuration();
	$stdprod=$cfg->get("cart_fee_product");
	if ($stdprod != "") {
		$tmp=new products();
		$sp=$tmp->getproductByProdCode($stdprod);

		$cartProd=new CartProduct();
		$cartProd->option="";
		$cartProd->prodcode=$stdprod;
		$cartProd->prodname=$sp->shorttext . " 0%";
		$cartProd->unitprice=$sp->unitprice;
		$cartProd->quantity=1;
		$cartProd->finalprice=$sp->unitprice; // left on 0 for no discount for starters
		$cartProd->id=uniqid("S");
		$cart2=new cart2();
		$cart2->removecartProduct($cartProd);
		$c=$cart2->readcart();
		$qties=$cart2->getCartQuantities();
		$total=$cart2->getCartTotal();

		if ( ($qties>=2) and ($qties<5) ) {
			$cartProd->finalprice = -1 * ( $total * 0.05 );
			$cartProd->prodname=$sp->shorttext . " 5%";
		}
		if ( ($qties>=5) and ($qties<14) ) {
			$cartProd->finalprice = -1 * ( $total * 0.10 );
			$cartProd->prodname=$sp->shorttext . " 10%";
		}
		if ( ($qties>=14) ) {
			$cartProd->finalprice = -1 * ( $total * 0.20 );
			$cartProd->prodname=$sp->shorttext . " 20%";
		}

		if (count($c)>0) { // makes ure we have something in the cart
			$cart2->addCartProduct($cartProd);
		}
	}
}

function AddStandard() {
	$cfg=new sc_configuration();
	$stdprod=$cfg->get("cart_fee_product");
	if ($stdprod != "") {
		$tmp=new products();
		$sp=$tmp->getproductByProdCode($stdprod);
		$cartProd=new CartProduct();
		$cartProd->option="";
		$cartProd->prodcode=$stdprod;
		$cartProd->prodname=$sp->shorttext;
		$cartProd->unitprice=$sp->unitprice;
		$cartProd->quantity=1;
		$cartProd->finalprice=$sp->unitprice;
		$cartProd->id=uniqid("S");
		$cart2=new cart2();
		$cart2->removecartProduct($cartProd);
		$c=$cart2->readcart();
		if (count($c)>0) {
			$cart2->addCartProduct($cartProd);
		}
	}
}

function checkerrors () {
	$errors=0;
	// this is a very simple check, you can add any kind of checking method to refine and enhance your security
	// first start by getting the published fields
	$fields=new fields();
	$fieldlist=$fields->getPublishedFields();
	// now check if they are required, and if so, check if they are filled
	// default function is "checkfilled" see below!
	foreach ($fieldlist as $field) {
		if ($field->required == 1 ){ // required field
			// now get the required function, this is set in the DB for each field
			if (function_exists($field->checkfunction)) { //check if you defined this function
				$errors = $errors + call_user_func($field->checkfunction, $field);
			}
		}
	}
	return $errors;
}

function matheval($equation){
    $equation = preg_replace("/[^0-9+\-.*\/()%]/","",$equation);
    // fix percentage calcul when percentage value < 10
    $equation = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$equation);
    // calc percentage
    $equation = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$equation);
    // you could use str_replace on this next line
    // if you really, really want to fine-tune this equation
    $equation = preg_replace("/([0-9]+)(%)/",".\$1",$equation);
    if ( $equation == "" )
    {
      $return = 0;
    }
    else
    {
      eval("\$return=" . $equation . ";" );
    }
    return $return;
}


// the basic function for checking if a field has been filled
function checkfilled($field) {
	if (trim(JRequest::getVar($field->name))=="") { // trim the field and compare
		echo "<div class='errormsg'>".JText::_('SC_REQUIRED_FIELD')." <b>$field->caption</b> ".JText::_('SC_IS_EMPTY')."</div>";
		return 1; // add one to the errors total
	}
	else
	{
		return 0; // adds nothing to the errors total
	}
}


?>