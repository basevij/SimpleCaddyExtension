<?php
/**
* @package SimpleCaddy 2.0.4 for Joomla 2.5
* @copyright Copyright (C) 2006-2013 Henk von Pickartz. All rights reserved.
* General class file
*/
// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once("simplecaddy.class.php"); // just to make sure we have the order functions available

class product_instance extends JTable {
    var $id;
    var $orderid;
    var $prodcode;
    var $odetailid;
    var $instancename;
    var $status;


	function product_instance() {
		$db	= JFactory::getDBO();
	   	$this->__construct( '#__sc_product_instance', 'id', $db );
	}

    function getInstanceByDetailId($detailid){
        $query="select * from {$this->_tbl} where `odetailid` = '$detailid'";
        $this->_db->setQuery($query);
        $Inst=$this->_db->loadResult();
        return $Inst;
    }


    function getInstanceByOrderId($orderid){
        $query="select * from {$this->_tbl} where `orderid` = '$orderid'";
        $this->_db->setQuery($query);
        $lst=$this->_db->loadObjectList();
        return $lst;
    }

	function getInstanceListByStatus($filter=null) {
		$db	= JFactory::getDBO();
		$query="select * from #__sc_product_instance ";

		if ($filter) {
				$query .= " and status = '$filter' ";
		}

		$query.= "order by `instancename`";

		$db->setQuery($query);
		$lst=$db->loadObjectList();

		if ($db->getErrorNum()) {
			echo $db->getErrorMsg();
			echo $db->getQuery();
		}

		return $lst;
	}

	function getFreeInstanceListByProdcode($filter=null) {
		$db	= JFactory::getDBO();
		$query="select * from #__sc_product_instance ";

		if ($filter) {
				$query .= " and prodcode = '$filter' and orderid=null order by instancename";
		}

		$db->setQuery($query);
		$lst=$db->loadObjectList();

		if ($db->getErrorNum()) {
			echo $db->getErrorMsg();
			echo $db->getQuery();
		}

		return $lst;
	}


}

class instances {
    var $id;
    var $instancename;
    var $orderid;
    var $proddesc;
    var $ordername;
    var $orderbusiness;



	function getOrderInstanceDetails {



	}

	function getFreeInstances {


	}

	function getAssignedInstances {


	}

	getAllInstances {

	}


?>
