<?php

require 'dbconfig.php';

class User {

    function checkUser($uid, $oauth_provider, $username) 
	{ 

		
        $query = mysql_query("SELECT * FROM users WHERE oauth_uid =".$uid." and oauth_provider ='".$oauth_provider."'") or mysql_error();
        $result = mysql_fetch_array($query);
		
        if (!empty($result)) {
			  //return $result;
	/*				echo "data";
		echo $uid.$oauth_provider.$username;
		print_r($result);
		print_r("SELECT * FROM users WHERE oauth_uid =".$uid." and oauth_provider ='".$oauth_provider."'");
		die;
		*/
            # User is already present
        } else {
            #user not present. Insert a new Record
			
			$name=explode(" ",$username);
			
            $query = mysql_query("INSERT INTO `users` (oauth_provider, oauth_uid,username,first_name,last_name) VALUES ('$oauth_provider', $uid, '$username','$name[0]','$name[1]')") or die(mysql_error());
            $query = mysql_query("SELECT * FROM `users` WHERE oauth_uid = '$uid' and oauth_provider = '$oauth_provider'");
            $result = mysql_fetch_array($query);
			  return $result;
            
        }
		
        return $result;
    }

    

}

?>
