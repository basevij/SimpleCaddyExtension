<?php
/**
* @package SimpleCaddy 2.0 for Joomla 2.5
* @copyright Copyright (C) 2006-2012 Henk von Pickartz. All rights reserved.
* Content display file
*/
// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class contentdisplay {
	static function showlist($rows) {
		display::header();
		JToolBarHelper::title( JText::_( 'SC_SIMPLECADDY_CONTENT' )); 
		JToolBarHelper::custom( 'control', 'back.png', 'back.png', 'Main', false,  false );
        $regex = '/{(simplecaddy)\s*(.*?)}/i';
		$prodcode=array();
		foreach ($rows as $row) { // first get all the plugins from the content
	        $matches = array();
	        preg_match_all( $regex, $row->introtext.$row->fulltext, $matches, PREG_SET_ORDER );
	        $i=0;
	        foreach ($matches as $elm) { // then clean em up and reorder to something useful
				$line=str_replace("&nbsp;", " ", $elm[2]);
	            $line=str_replace(" ", "&", $line);
	            $line=strtolower($line);
	            $parms=array();
	            $elm["cid"]=$row->id;
	            parse_str( $line, $parms );

	            if (!isset($parms['type'])) {
	            	$res[$row->id]["type"][$i]="buynow"; // just make sure we have something to say
	            }
	            else
	            {
	            	$res[$row->id]["type"][$i]=$parms["type"];
	            }
	            $res[$row->id]["prodcode"][$i]=@$parms["code"];
	            $res[$row->id]["cid"][$i]=$row->title;
	            $i++;
	        }
		}
		// then present them on the page
        echo "<table class='adminlist'>";
        echo "<tr>";
        echo "<th style='width: 40px;'>".JText::_("SC_ID")."</th>";
        echo "<th style='width: 300px;'>".JText::_("SC_TITLE")."</th>";
        echo "<th style='width: 250px;'>".JText::_("SC_TYPES")."</th>";
        echo "<th>".JText::_("SC_PRODCODES")."</th>";
        echo "</tr>";
		foreach ($rows as $row) { 
			echo "<tr>";
			echo "<td>$row->id</td>";
			echo "<td><a href='index.php?option=com_content&task=article.edit&id=$row->id' target='_blank'>$row->title</a></td>";
			$sctypes= @ implode(",", $res[$row->id]["type"]);
			echo @ "<td>$sctypes</td>";
			echo "<td>";
			if (is_array(@$prodcode)) {
				if (isset($res[$row->id]["prodcode"])) {
					foreach(@$res[$row->id]["prodcode"] as $key=>$value) {
						if (@$value) {
							$product=new products();
							$p=$product->getproductByProdCode($value);
							echo @ "<a href='index.php?option=com_simplecaddy&action=products&task=edit&cid[0]=$p->id' target='_blank'>$value</a>&nbsp;";
						}
					}
				}  
			}
			echo "</td></tr>";
	 	}
		echo "</table>"; 
		?>
		<form name="adminForm">
		<input type="hidden" name="task" />
		<input type="hidden" name="option" value="com_simplecaddy" />
		</form>
		<?php
	}
}
