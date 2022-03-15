<?php
class Application_Model_Paypalrecurring extends Zend_Db_Table_Abstract
{
		
	public function init()
	{
		$this->site_config=Zend_Registry::get('site_config');
		$this->modelStatic = new Application_Model_Static();
		$this->modelEmail = new Application_Model_Email();
		$this->API_USERNAME = $this->site_config['paypal_username'];
		$this->API_PASSWORD = $this->site_config['paypal_password'];
		$this->API_VENDOR   = $this->site_config['paypal_vendor'];
		$this->API_PARTNER  =  $this->site_config['paypal_partner'];
		$this->ACCOUNT_MODE  = strtolower($this->site_config['paypal_account_status']);
		
		
	}
	public function CreateRecurringPaymentsProfile($request_data)
	{ 
					
					global $objSession;  
					$PayFlow = new PayFlow($this->API_VENDOR, $this->API_PARTNER, $this->API_USERNAME, $this->API_PASSWORD, 'recurring');
					$PayFlow->setEnvironment($this->ACCOUNT_MODE);                            
					$PayFlow->setTransactionType('R');                           
					$PayFlow->setPaymentMethod('C');
					$PayFlow->setPaymentCurrency($this->site_config['site_currency']);                       
				 	$PayFlow->setProfileAction('A');
					//$PayFlow->setProfieID('RP0000000005');
					$PayFlow->setProfileName($request_data['user_first_name'].$request_data['user_last_name']);
					$PayFlow->setProfileStartDate(date('mdY', strtotime("+1 day")));
					$PayFlow->setProfilePayPeriod('MONT');
					//$PayFlow->setProfilePayPeriod('YEAR');
					$PayFlow->setProfileTerm(0);
					$PayFlow->setAmount($request_data['amount'], FALSE);
					$PayFlow->setCCNumber($request_data['user_cardnumber']);
					$PayFlow->setCVV($request_data['user_cvv']);
					$request_data['user_expirationMonth'] =   sprintf("%02s", $request_data['user_expirationMonth']);
					$request_data['user_expirationYear'] = substr($request_data['user_expirationYear'], -2);
					$PayFlow->setExpiration($request_data['user_expirationMonth'].$request_data['user_expirationYear']);
					//$PayFlow->setCreditCardName('HELLO');
					//$PayFlow->setCustomerAddress('HELLO');
					//$PayFlow->setCustomerCity('HELLO');
					//$PayFlow->setCustomerState('HELLO');
					//$PayFlow->setCustomerZip('HELLO');
					$InProcess = $PayFlow->processTransaction();
					$debugNvp = $PayFlow->debugNVP('array');
					$Response = $PayFlow->getResponse();
					return $Response;
	}
	
		public function UpdateRecurringPaymentsProfile($request_data)
		{ 
					 
					global $objSession;  
					$PayFlow = new PayFlow($this->API_VENDOR, $this->API_PARTNER, $this->API_USERNAME, $this->API_PASSWORD, 'recurring');
					$PayFlow->setEnvironment($this->ACCOUNT_MODE);                            
					$PayFlow->setTransactionType('R');                           
					$PayFlow->setPaymentMethod('C');
					$PayFlow->setPaymentCurrency($this->site_config['currenc2_code']);                       
				 	$PayFlow->setProfileAction('M');
					$PayFlow->setProfileID($request_data['sd_profile_id']);
					$PayFlow->setProfileName($request_data['user_first_name'].$request_data['user_last_name']);
					$PayFlow->setProfileStartDate(date('mdY', strtotime("+1 day")));
					$PayFlow->setProfilePayPeriod('MONT');
					//$PayFlow->setProfilePayPeriod('YEAR');
					$PayFlow->setProfileTerm(0);
					$PayFlow->setAmount($request_data['amount'], FALSE);
					$PayFlow->setCCNumber($request_data['user_cardnumber']);
					$PayFlow->setCVV($request_data['user_cvv']);
					$request_data['user_expirationMonth'] =   sprintf("%02s", $request_data['user_expirationMonth']);
					$request_data['user_expirationYear'] = substr($request_data['user_expirationYear'], -2);
					$PayFlow->setExpiration($request_data['user_expirationMonth'].$request_data['user_expirationYear']);
					//$PayFlow->setCreditCardName('HELLO');
					//$PayFlow->setCustomerAddress('HELLO');
					//$PayFlow->setCustomerCity('HELLO');
					//$PayFlow->setCustomerState('HELLO');
					//$PayFlow->setCustomerZip('HELLO');
					$InProcess = $PayFlow->processTransaction();
					$debugNvp = $PayFlow->debugNVP('array');
					$Response = $PayFlow->getResponse();
					return $Response;
	}
	
			public function ClearRecurringPaymentsProfile($profile_id)
	{ 
					 
					global $objSession;  
					$PayFlow = new PayFlow($this->API_VENDOR, $this->API_PARTNER, $this->API_USERNAME, $this->API_PASSWORD, 'recurring');
					$PayFlow->setEnvironment($this->ACCOUNT_MODE);                            
					$PayFlow->setTransactionType('R');                           
					$PayFlow->setPaymentMethod('C');
					$PayFlow->setPaymentCurrency($this->site_config['currenc2_code']);                       
				 	$PayFlow->setProfileAction('C');
					$PayFlow->setProfileID($profile_id);
					$PayFlow->setProfileName($request_data['user_first_name'].$request_data['user_last_name']);
					$PayFlow->setProfileStartDate(date('mdY', strtotime("+1 day")));
					$PayFlow->setProfilePayPeriod('MONT');
					//$PayFlow->setProfilePayPeriod('YEAR');
					$PayFlow->setProfileTerm(0);
					$PayFlow->setAmount($request_data['amount'], FALSE);
					$PayFlow->setCCNumber($request_data['user_cardnumber']);
					$PayFlow->setCVV($request_data['user_cvv']);
					$request_data['user_expirationMonth'] =   sprintf("%02s", $request_data['user_expirationMonth']);
					$request_data['user_expirationYear'] = substr($request_data['user_expirationYear'], -2);
					$PayFlow->setExpiration($request_data['user_expirationMonth'].$request_data['user_expirationYear']);
					//$PayFlow->setCreditCardName('HELLO');
					//$PayFlow->setCustomerAddress('HELLO');
					//$PayFlow->setCustomerCity('HELLO');
					//$PayFlow->setCustomerState('HELLO');
					//$PayFlow->setCustomerZip('HELLO');
					$InProcess = $PayFlow->processTransaction();
					$debugNvp = $PayFlow->debugNVP('array');
					$Response = $PayFlow->getResponse();
					return $Response;
	}

}