<?php
/**
 * @category   Apptrian
 * @package    Apptrian_Subcategories
 * @author     Apptrian
 * @copyright  Copyright (c) 2014 Apptrian (http://www.apptrian.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Apptrian_Subcategories_Block_About
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		
        $logopath =	'http://www.apptrian.com/media/apptrian.gif';
        $html = <<<HTML
<div style="background:url('$logopath') no-repeat scroll 15px 15px #e7efef; border:1px solid #ccc; min-height:100px; margin:5px 0; padding:15px 15px 15px 140px;">
	<p>
		<strong>Apptrian Subcategories Grid / List Extension</strong><br />
		Shows subcategories in the form of a grid or list, on category pages and optionally on the home page.
	</p>
    <p>
        Website: <a href="http://www.apptrian.com" target="_blank">www.apptrian.com</a><br />
        Twitter: <a href="http://twitter.com/apptrian" target="_blank">@apptrian</a><br />
        If you have any questions send email at <a href="mailto:service@apptrian.com">service@apptrian.com</a>
    </p>
</div>
HTML;
        return $html;
    }
}