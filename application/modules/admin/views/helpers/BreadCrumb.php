<?php
class Zend_View_Helper_BreadCrumb extends Zend_View_Helper_Partial
{
	
	public function getBreadcrumb($links =array()){
		
 		return $this->partial('BreadCrumb/breadcrumb.phtml' ,array('links' => $links));
	}
}

?>