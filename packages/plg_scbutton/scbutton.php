<?php
/**
 * @version   	1.1
 * @package     Joomla
 * @subpackage  System
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU GPL v2.0
 */
 
defined('_JEXEC') or die;

class plgButtonScbutton extends JPlugin
{
    function onDisplay($name) {
        $this->loadLanguage();

//		$txt=$this->definecode();
		$label = JText::_("SCEX_SIMPLECADDY");
//		$jsCode = "
//                function insertCustText(editor) {
//                    jInsertEditorText('".$txt."', editor);
//                }
//				";
				
		JHtml::_('behavior.modal');
		$doc = JFactory::getDocument();
//		$doc->addScriptDeclaration($jsCode);

$link = 'index.php?option=com_simplecaddy&amp;action=view_prod&amp;layout=modal&amp;tmpl=component&amp;'.JSession::getFormToken().'=1&amp;name='.$name;
		
        $button = new JObject();
        $button->set('text',$label);
        $button->set('name','blank');
//		$button->set('onclick', 'insertCustText(\''.$name.'\')');
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('options', "{handler: 'iframe', size: {x: 970, y: 600}}");
        return $button;
    }
    
    function definecode() {
    	return "{simplecaddy type=buynow code=sox001 nextcid=51}";
    }
}
?>