<?php
/**
* @package SimpleCaddy 2.0 for Joomla 2.5
* @copyright Copyright (C) 2006-2012 Henk von Pickartz. All rights reserved.
* Install file
*/
// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class com_simplecaddyInstallerScript {
	function install($parent) {
            // $parent is the class calling this method
	}

    function uninstall($parent) 
    {
            // $parent is the class calling this method
     }

    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent) 
    {
            // $parent is the class calling this method
            echo "update script called";
    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent) 
    {
    	echo "preflight";
            // $parent is the class calling this method
            // $type is the type of change (install, update or discover_install)
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent) 
    {
    		echo "postflight";
            // $parent is the class calling this method
            // $type is the type of change (install, update or discover_install)
    }

}

?>