<?php
class Zend_View_Helper_Messages extends Zend_View_Helper_Partial
{
	public function getMessages(){
  		return $this->partial('static/messages.phtml');
	}

}

?>