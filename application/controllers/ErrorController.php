<?php
class ErrorController extends Zend_Controller_Action
{

   ##----------------------------##
   ## ** THEJAMSTOP **
   ## Error
   ##----------------------------##
    public function errorAction(){
		
		 	
    //	$this->initRootController() ;
	$errors = $this->_getParam('error_handler');
		
       	
        
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
            default:
			
			if(get_class($errors->exception)=="Zend_Db_Statement_Exception"){
				$trace=$errors->exception->getTrace() ;gcm($errors->exception->getTrace);
				foreach($trace as $traceobj){
					if($traceobj['class']=="Zend_Db_Adapter_Pdo_Abstract"){
						$errorArgs=$traceobj['args'];
					}
				}
	 
				  $this->view->lastQuery = prepareQuery($errorArgs);
			} 
				
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
              	$this->view->message = 'Application error' ;
				// $this->view->message. = implode(",",$errorArgs);
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }
        
		if(defined("HIDE_ZEND_ERRORS")){
		
		  $this->view->message="This part is under development";
		  
		  //$this->view->hiddenmessage="This part is under development";
		  
		  $this->view->hiddenmessage= '<!--'.($errors->exception->getMessage()).($errors->exception->getTraceAsString()).'-->';
		
			return;
		}
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

