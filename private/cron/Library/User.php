<?php 
class User extends DbAdapter { /* Begin Class DB */
	public $user_config , $dbAdapter ;
 	public function updatelesson()
	{
		$lesson_arr=array();
		//require_once('./ziggeo/Ziggeo.php');
		$configuration =$this->Super_Get("config","1","fetchAll");
		$ziggeo = new Ziggeo('80022bf8c53e76bfb6c1bebccefc6113', 'e07460938f50cd611b59a5f764676e48', '384609c33a387b25c597cd8bb1790c96'); 
 		foreach($configuration as $key=>$config){
			$config_data[$config['config_key']]= $config['config_value'] ;
			$config_groups[$config['config_group']][$config['config_key']]=$config['config_value'];	
		}
		
	
		$lesson_arr=$this->Super_Get("lesson","1","fetchAll");
	
//mail('userjdr1@gmail.com','cronrunnig1',$lesson_arr);
$id='';
		foreach($lesson_arr as $k=>$v)
		{
			
			$lesson_student=array();
			$lesson_student=$this->Super_Get("lesson_student","l_s_autodelete='1' and l_s_lessid='".$v['lesson_id']."'","fetch");
			/*if(empty($lesson_student))
			{*/
				$aftersixmonth=date("Y-m-d H:i:s", strtotime("+6 months", strtotime($v['lesson_date'])));	
				$today=date("Y-m-d H:i:s");
				if(strtotime($aftersixmonth)<strtotime($today))
				{ $id.=$v['lesson_id'].',';
				$lesson_attach=array();
				$lesson_attach=$this->Super_Get("lession_attach","la_lesson_id='".$v['lesson_id']."'","fetchAll");
			
				
				foreach($lesson_attach as $ks=>$vs)
				{
					if($vs['la_type']==1)
					{
							// video uploaded
						
							if(file_exists(TEMP_PATH."/".$vs['la_name']))
							{
							unlink(TEMP_PATH.'/'.$vs['la_name']);	
							}
							if(file_exists(TEMP_PATH."/voice_".$v['lesson_teacherid'].'/'.$vs['la_name']))
							{
							unlink(TEMP_PATH."/voice_".$v['lesson_teacherid'].'/'.$vs['la_name']);	
							}
							if(file_exists(TEMP_PATH."/upimage_".$v['lesson_teacherid'].'/'.$vs['la_name']))
							{
							unlink(TEMP_PATH."/upimage_".$v['lesson_teacherid'].'/'.$vs['la_name']);	
							}
							
							
					}	
					else
					{
							// recorded by ziggeo
							if($vs['la_token']!=''){
								
							$ziggeo->videos()->delete($vs['la_token']) ;
							}
							
					}
					
				}
				
				$this->Super_Delete("lession_attach","la_lesson_id='".$v['lesson_id']."'");
				$this->Super_Delete("lesson_student","l_s_lessid='".$v['lesson_id']."'");
				$this->Super_Delete("lesson","lesson_id='".$v['lesson_id']."'");
				//pr($v);				
			}	
			/*}*/
			/*else{
				
				pr($v);	
			}*/
		}
		//mail('userjdr1@gmail.com','cronrunnig1',$id);die;

	}

}/* End Class DB */






