<?php
function SendEmail($to,$subject,$message,$from,$fromName = "",$toName = "")
{
	try {
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: ' .$from. "\r\n" .
				'Reply-To: ' .$to. "\r\n" .
				'X-Mailer: PHP/' . phpversion();
				mail($to, $subject, $message, $headers);
			//return $mail->send();
			//$headers .= 'BCC: '.ADMIN_EMAIL.'' . "\r\n";
		
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
}

function isAlnum($string,$options = array())
{	
	$validator = new Zend_Validate_Alnum($options);
	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isAlpha($string,$options = array())
{	
	$validator = new Zend_Validate_Alpha($options);
 	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isBarcode($string,$options = array()){	
	$validator = new Zend_Validate_Barcode($options);
	if ($validator->isValid($string)) 
		return true ;
		return false; 
}

function isBetween($string,$options = array()){
	$validator = new Zend_Validate_Between($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isCallback($string,$options = array()){	
	$validator = new Zend_Validate_Callback($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isCreditCard($string,$options = array()){	
	$validator = new Zend_Validate_CreditCard($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isCcnum($string,$options = array()){	
	$validator = new Zend_Validate_Ccnum($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isDate($string,$options = array()){	
	$validator = new Zend_Validate_Date($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isDb_RecordExists($string,$options = array()){	
	$validator = new Zend_Validate_Db_RecordExists($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isDb_NoRecordExists($string,$options = array()){	
	$validator = new Zend_Validate_Db_NoRecordExists($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isDigits($string,$options = array()){	
	$validator = new Zend_Validate_Digits($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isEmailAddress($string,$options = array()){	
	$validator = new Zend_Validate_EmailAddress($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isFloat($string,$options = array()){	
	$validator = new Zend_Validate_Float($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isGreaterThan($string,$options = array()){	
	$validator = new Zend_Validate_GreaterThan($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isHex($string,$options = array()){	
	$validator = new Zend_Validate_Hex($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isHostname($string,$options = array()){	
	$validator = new Zend_Validate_Hostname($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isIban($string,$options = array()){	
	$validator = new Zend_Validate_Iban($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isIdentical($string,$options = array()){	
	$validator = new Zend_Validate_Identical($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isInArray($string,$options = array()){	
	$validator = new Zend_Validate_InArray($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isInt($string,$options = array()){	
	$validator = new Zend_Validate_Int($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isIp($string,$options = array()){	
	$validator = new Zend_Validate_Ip($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isIsbn($string,$options = array()){	
	$validator = new Zend_Validate_Isbn($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isLessThan($string,$options = array()){	
	$validator = new Zend_Validate_LessThan($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isNotEmpty($string,$options = array()){	
	$validator = new Zend_Validate_NotEmpty($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isPostCode($string,$options = array()){	
	$validator = new Zend_Validate_PostCode($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isRegex($string,$options = array()){	
	$validator = new Zend_Validate_Regex($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isSitemap($string,$options = array()){	
	$validator = new Zend_Validate_Sitemap($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}
function isStringLength($string,$options = array()){	
	$validator = new Zend_Validate_StringLength($options);

	if ($validator->isValid($string)) 
		return true ;
		return false; 
}


function _filters($string , $options = array()){
	

 	if(isset($options['ignore'])){
		
		if(!is_array($options['ignore'])){
			$options['ignore'] = (array)$options['ignore'];
		}
		
		if(is_array($string)){
			$copy = $string ;
			foreach($options['ignore'] as  $value_ignore){
				recursive_unset($copy ,$value_ignore);
			}
		}else{
			$copy = $string ;
		}
  	}else{
		$copy = $string ;
		$options['ignore'] = array();
	}
  	
	 
	if(is_array($options)){
 		foreach($options as $key=>$filter){
			$copy = apply_filter($copy,$filter);
		}
		if(!is_array($string)){
			return $copy;
			
		}
 		return array_replace_recursive($string,$copy) ;	
 	}
	
	
	
	return  apply_filter($string,$options);
 }


function recursive_unset(&$array, $unwanted_key) {
    unset($array[$unwanted_key]);
    foreach ($array as &$value) {
        if (is_array($value)) {
            recursive_unset($value, $unwanted_key);
        }
    }
}


function _key_ignore($string , $options){
	
	if(is_array($string)){
	}
	
}


function apply_filter($string , $type){
	if(is_array($string)){
		foreach($string as $key=>$value){
			if(is_array($value)){
				$value = apply_filter($value,$type);
				$string[$key]= ($value);
			}else{
				$string[$key]= _apply_filter($value,$type);	
			}
			
		}
		return $string;
	}elseif(is_object($string)){
		$string = (array)$string ;
		return (object)apply_filter($string,$type);
	}else{
		return _apply_filter($string,$type);
	}
}

function _apply_filter($string,$type){
	switch($type){
		case "Alnum": $filter = new Zend_Filter_Alnum(); break;
		case "Alpha": $filter = new Zend_Filter_Alpha(); break;
		case "BaseName": $filter = new Zend_Filter_BaseName(); break;
		case "Boolean": $filter = new Zend_Filter_Boolean(); break;
		case "Callback": $filter = new Zend_Filter_Callback(); break;
		case "CompressandDecompress": $filter = new Zend_Filter_CompressandDecompress(); break;
		case "Digits": $filter = new Zend_Filter_Digits(); break;
		case "Dir": $filter = new Zend_Filter_Dir(); break;
		case "EncryptandDecrypt": $filter = new Zend_Filter_EncryptandDecrypt(); break;
		case "HtmlEntities": $filter = new Zend_Filter_HtmlEntities(); break;
		case "Int": $filter = new Zend_Filter_Int(); break;
		case "Null": $filter = new Zend_Filter_Null(); break;
		case "PregReplace": $filter = new Zend_Filter_PregReplace(); break;
		case "RealPath": $filter = new Zend_Filter_RealPath(); break;
		case "StringToLower": $filter = new Zend_Filter_StringToLower(); break;
		case "StringToUpper": $filter = new Zend_Filter_StringToUpper(); break;
		case "StringTrim": $filter = new Zend_Filter_StringTrim(); break;
		case "StripNewLines": $filter = new Zend_Filter_StripNewLines(); break;
		case "StripTags": $filter = new Zend_Filter_StripTags(); break;
		default : return $string;
	}
	
 	return $filter->filter($string);
}

/*
function pr($string){
	echo "<pre>";
	print_r($string);
	echo "</pre>";
}


function prd($string){
	pr($string);
	exit();
}


function gcm($string){
	pr(get_class_methods($string));
}
*/

function gcmd($string){
	pr(get_class_methods($string));
	exit();
}



function encryptPassowrd($string){
	return md5($string);
}



function is_assoc($array) {
  return (bool)count(array_filter(array_keys($array), 'is_string'));
}



function send_mail($configs = array() ){
 	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";	
	$headers .= 'To: '.$configs['receiver_name']. "\r\n";
	$headers .= "From: Domination \r\n";
  	return mail($configs['receiver_email'], $configs['subject'], $configs['message'], $headers);
}


 
function getAge($date){
	$from = new DateTime($date);
	$to   = new DateTime('today');
	return $from->diff($to)->y;

}
























