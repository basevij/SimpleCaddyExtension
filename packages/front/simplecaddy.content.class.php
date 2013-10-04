<?php
/**
* @package SimpleCaddy 2.0.4 for Joomla 2.5
* @copyright Copyright (C) 2006-2013 Henk von Pickartz. All rights reserved.
* General class file
*/
// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class sccontent extends JTable {
	var $id;
	var $introtext;
	var $fulltext;
	var $alias;
	var $catid;

	function sccontent() {
		$db	= JFactory::getDBO();
	   	$this->__construct( '#__content', 'id', $db );
	}


	function getlist() {
		$query="select *
				from `$this->_tbl`
				where
				(`introtext` like '%{simplecaddy%' or `fulltext` like '%{simplecaddy%'
				or
				`introtext` like '%{sctax%' or `fulltext` like '%{sctax%'
				or
				`introtext` like '%{scpaypal%' or `fulltext` like '%{scpaypal%'
				or
				`introtext` like '%{scshipping%' or `fulltext` like '%{scshipping%'
				or
				`introtext` like '%{sccoupons%' or `fulltext` like '%{sccoupons%'
				or
				`introtext` like '%{scorders%' or `fulltext` like '%{scorders%'
				)
				and `state` >=0 ";
		$this->_db->setQuery($query);
		$lst=$this->_db->loadObjectList();
		return $lst;
	}
}

class sccontents {
	var $redirect;
	var $message;

	function redirect() {
        $mainframe=JFactory::getApplication();
        if ($this->redirect!="") $mainframe->redirect($this->redirect, $this->message);
	}

	function show() {
		$scc=new sccontent();
		$lst=$scc->getlist();
		contentdisplay::showlist($lst);
	}
}