<?php 
class DbAdapter { /* Begin Class DB */
 
	public function __construct(){
		
		/* Code To Create Connection  */
		
		 /*  Connect File For Cron Job */
		$dbConfig = parse_ini_file(APPLICATION_PATH . '/configs/db.ini',false);
		
		
		/*  Connect File For Cron Job */
		$connection  = mysql_connect($dbConfig['resources.db.params.hostname'] , $dbConfig['resources.db.params.username'] , $dbConfig['resources.db.params.password']);
		
		if(!$connection){
			die('Could not connect: ' . mysql_error());
		}
		$dbselect = mysql_select_db($dbConfig['resources.db.params.dbname']);
 
		if(!$dbselect){
			die('Could not connect: ' . mysql_error());
		}
	}
	
	
	
	public function insert($table_name , $data = array()){
		if(empty($table_name) or empty($data) ){
			die(" Invalid Parameters to call insert .  ");
		}
		return $this->Super_Insert($table_name ,  $data);
	}
 
 
	public function update($table_name , $data = array() , $where ){
		if(empty($table_name) or empty($data) or empty($where)){
			die(" Invalid Parameters to call update .  ");
		}
		return $this->Super_Insert($table_name ,  $data , $where );
	}
 
 	
	public function exec($query){
		$result = mysql_query($query) or die(mysql_error());
		return $result; 
	}
	
	public function lastInsertId(){
		return (int) mysql_insert_id() or die(mysql_error());
	}
	
	public function beginTransaction(){
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		return true;		
	}
	
	public function commit(){
		mysql_query("COMMIT");
		return true;		
	}
	
	public function rollBack(){
		 mysql_query("ROLLBACK");
		 return true;		
	}
	
 
 
	public function runQuery($query){
		
		$result = mysql_query($query) or die(mysql_error());
		
		$temp = array();
		
		while($row = mysql_fetch_assoc($result)){
			array_push($temp,$row);
		}
		
		return $temp;
	}
	
	
	
	public function fetchAll($query){
		$result = mysql_query($query) or die(mysql_error());
		
		$temp = array();
		while($row = mysql_fetch_assoc($result)){
			array_push($temp,$row);
		}
		return $temp;
	}
	
	
	public function fetch($query){
		$result = mysql_query($query) or die(mysql_error());
		return mysql_fetch_assoc($result) ;
	}
	
	
	public function Super_Insert( $table_name , $data, $where = false){
		
		$query = mysql_query(" describe `$table_name`") or die( mysql_error() );
		
		$data_types = array();
		
		while($row = mysql_fetch_assoc($query)){
			$orignal[] = $row['Type'];
			if(preg_match('[int|float|double]',$row['Type'])){
				$data_types[$row['Field']] = "int";
			}else{
				$data_types[$row['Field']] = "string";
			}
		}
		
		foreach($data as $key=>$value){
			if(isset($data_types[$key]) and $data_types[$key]!="int"){
				$data[$key] = "'".$data[$key]."'";
			}
		}
		
		
		
		
		if($where){
			$temp =array();
	
			foreach($data as $key=>$value){
				$temp[] ="`$key`= $value";
			}
	
			$query = "update   `$table_name` set ".implode(",",$temp)." where $where ";
			
		}else{
			$query = "insert into `$table_name` (".implode(",",array_keys($data)).") values(".implode(",",$data).") ";
		}
		
		$exec_query  = mysql_query($query) ;
		
		if(!$exec_query){
			return (object)array("success"=>false,"error"=>true,"message"=>mysql_error(),"exception"=>true,"exception_code"=>mysql_errno()) ;
		}
	
		if($where){
			return  (object)array("success"=>true,"error"=>false,"message"=>'successfully ' ,'rows_affected'=>$exec_query);
		}else{
			return  (object)array("success"=>true,"error"=>false,"message"=>'successfully ' ,'inserted_id'=>mysql_insert_id());
		}
	}
	
 
	
  
  
	public function Super_Get($table_name , $where = 1, $fetchMode = 'fetch', $extra = array()){
		
		$query = mysql_query(" describe `$table_name` ");
		
		if(!$query){
			echo "<h1 align='center'> ".mysql_error()." </h1>";
			exit();
		}
		
		$fields = '*';
		
		if(isset($extra['fields']) and  $extra['fields']){
			if(is_array($extra['fields'])){
				$fields = '`'.implode("`,`",$extra['fields']).'`';
			}else{
				$fields = '`'.implode("`,`",array_map('trim',explode(",",$extra['fields']))).'`';
			}
		}
		
		$query  =  " Select  $fields  from `$table_name` where $where " ;  //$this->select()->from($this->_name,$fields)->where($where);
		
	 
		if(isset($extra['group']) and  $extra['group']){
			$query.= " group by  ".$extra['group'] ; 
		}
		
		if(isset($extra['having']) and  $extra['having']){
			$query.= " having  ".$extra['having'] ; 
		}
		
		if(isset($extra['order']) and  $extra['order']){
			$query.= " order  ".$extra['order'] ; 
		}
		
		if(isset($extra['limit']) and  $extra['limit']){
			$query.= " limit  ".$extra['limit'] ; 
		}
	 
		return $fetchMode=='fetch'? $this->fetch($query):$this->fetchAll($query);
		
	}
	
	
	
	/* Get Retail Price For Specific Product */
	public function  getRetailPrice($metal_type_id){
		
		switch($metal_type_id){
			case 1   : $pc_id = 1;break; 		
			case 2   : $pc_id = 3;break; 
			case 3 	 : $pc_id = 2;break; 
			case 4   : $pc_id = 4;break; 
			default  : return false  ;
		}
			
		$price_config = $this->Super_Get("price_config"," pc_id = $pc_id ");
		
		if($price_config['pc_status']==1){
			return $price_config['pc_price'];
		}else{
			return $price_config['pc_manual_price'];
		}
	}
	
	
	/* getStandingSellOrderSerials */
	public function getStandingSellOrderSerials($sell_order_id , $qty ){
		$serials = $this->Super_Get("order_serials","order_serial_order_id = ".$sell_order_id." and order_serial_status ='processing' ","all",array("limit"=>$qty));
		return $serials;  
	}





	




	
	/* 
		Process Order By Cron  
		
		Algo - 
				1) Get All Processing Buy Orders [ORDER BY ID]
				
	
	
	*/
	public function processOrderByCron(){
		
		$orders = $this->getAllEffectiveOrders();
		
		/* Process All Buy Orders One By One */
		foreach($orders as $key=>$id){
			
			$order = $this->getEffectiveOrder($id['order_id']);

			if(!$order){
				continue; 
			}
			
			if($order['order_type']=="buy"){
				
				
				/* Buy Order */
				$is_processed = $this->_processBuyOrderByCron($order);
				
				
			}else{
				/* Sell Order */
				$is_processed = $this->_processSellOrderByCron($order);/* Complete */
			}
			
			
			
		}/* END BUY ORDER LOOP */
	}
	
	
	
	
	
	/* Sell Order With Dynamic Pirce 
		STEPS : 
			1	=>	Calculate Selling Price for current Order
			2	=>	Get Buy Order Greater then Seller's Selling price Order by Price DESC
			
		
		
	
	
	 */
	private function _processSellOrderByCron($sell_order){
		
		$retail_price = $this->getRetailPrice($sell_order['product_type_metal_id']);
		
		
		if($sell_order['order_pricing_type']=="fixed"){
			$selling_price = (float)($sell_order['order_avg_cost_ea']); 
		}else{
			$selling_price = (float)($retail_price - $sell_order['order_reference_price_modifier']); 	
		}
		
		$selling_price = (float)($retail_price - $sell_order['order_reference_price_modifier']); 

		$all_buy_orders = $this->getAvailableBuyOrders($sell_order  , $selling_price , $retail_price);
		
		$remaining_qty = $sell_order['order_quantity'];
		
		$this->beginTransaction() ;			

		foreach($all_buy_orders as $buy_order){ /* Begin Buy Orders For Each Loop */
		
			
			if($remaining_qty!=0){
				
				if($buy_order['order_pricing_type']=="fixed"){
					$buying_qty_for_current_order = (int) $buy_order['order_quantity_remaining'] ;  
				}else{
					$buying_qty_for_current_order = (int) ($buy_order['order_estimated_cost_remaining'] / $buy_order['buying_price'] ) ;  
				}
				
				
				$qty_to_sell_in_current_loop = 0;
				
				if($buying_qty_for_current_order > $remaining_qty ){
					$qty_to_sell_in_current_loop = $remaining_qty ;
				}else{
					$qty_to_sell_in_current_loop  = $buying_qty_for_current_order;
				}

	
				$all_serials  = $this->getStandingSellOrderSerials($sell_order['order_id'], $qty_to_sell_in_current_loop);
				
				$serial_ids = array(
					'order_serial_id'=>array(),
					'order_serial_serial_id'=>array()
				);
		
				foreach($all_serials  as  $serial){
					$serial_ids['order_serial_id'][] = $serial['order_serial_id'];
					$serial_ids['order_serial_serial_id'][] = $serial['order_serial_serial_id'];
				}
				
				
				
				/* Update order_serials status with completed */
				$is_serial_status_changed = $this->Super_Insert("order_serials",array("order_serial_status"=>"completed","order_serial_updated"=>date('Y-m-d H:i:s')),"order_serial_id IN (".implode(",",$serial_ids['order_serial_id']).")");
				
				if($is_serial_status_changed->success){
					
					/* Update Serial Owner with the Buyer Id */
					$is_owner_updated = $this->Super_Insert("inventory_serial",array(
															"serial_owner"=>$buy_order['order_user_id'],
															"serial_status"=>"active"
														),
														"serial_id IN (".implode(",",$serial_ids['order_serial_serial_id']).")"
													);
					
					 
					if($is_owner_updated->success){
						
						/*  Make Entry in Order execute  table for the current sold qty and buy oty  */
						
						$data_for_order_execute_table = array(
							"execute_sell_order_id"=>$sell_order['order_id'],
							"execute_buy_order_id"=>$buy_order["order_id"],
							"execute_quantity"=>$qty_to_sell_in_current_loop,
							"execute_total_price"=>(float)($buy_order['buying_price']*$qty_to_sell_in_current_loop),
							"execute_status"=>"completed",
							"execute_created"=>date("Y-m-d H:i:s"),
						);
						
						$is_inserted_order_execute_data = $this->Super_Insert("order_execute",$data_for_order_execute_table); 
						
						if($is_inserted_order_execute_data->success){
							
							foreach($all_serials as $serial){
								
								$order_execute_serial_data = array(
									"execute_serial_execute_id"=>$is_inserted_order_execute_data->inserted_id,
									"execute_serial_serial_id"=>$serial["order_serial_serial_id"],
								);
								
								$is_inserted_order_execute_serial = $this->Super_Insert("order_execute_serials" , $order_execute_serial_data);
								
								if($is_inserted_order_execute_serial->error){
									$this->rollBack();
									return $is_inserted_order_execute_serial;
								}
							}
							
							
							/* Update Buy and Sell Order table entries  
								1 Update Buy  Order 
								
									 
							*/
							
							
							$data_update_to_buy_order = array(
								'order_quantity_remaining'=>"`order_quantity_remaining` - ".count($all_serials)."",
								'order_updated'=>date('Y-m-d H:i:s'),
								
							);
							
							if($buy_order['order_pricing_type']=="dynamic"){
								$data_update_to_buy_order["order_estimated_cost_remaining"] = "`order_estimated_cost` - ".($qty_to_sell_in_current_loop*$buy_order['buying_price'])."";
							}else{
								$data_update_to_buy_order["order_quantity_remaining"] = "`order_quantity_remaining` - ".($qty_to_sell_in_current_loop)."";
							}
							
							
							if($buying_qty_for_current_order ==$qty_to_sell_in_current_loop){
								$data_update_to_buy_order['order_status'] = "completed";
							}
							
							
							$is_buy_order_updated = $this->Super_Insert("order",$data_update_to_buy_order,"order_id = ".$buy_order['order_id']);
							
							
							if($is_buy_order_updated->success){
								$remaining_qty -=$qty_to_sell_in_current_loop ;
								continue;	
							}
							$this->rollBack();
							return $is_buy_order_updated;
							
						}
						$this->rollBack();
						return $is_inserted_order_execute_data;
					}
					$this->rollBack();
					return $is_owner_updated;
				}
				return $is_serial_status_changed ;
				
			}
			break;
			
		}/* End Buy Orders For EachLoop */
		
		if($remaining_qty or true){					
			/* UPDATE SELL ORDER AND MAKE ALL TRANSACTIONS COMMIT */
			
			$data_update_for_sell_order = array(
				"order_quantity_remaining"=>$remaining_qty,
				"order_status"=>$remaining_qty==0?'completed':'processing',
				"order_updated"=>date("Y-m-d H:i:s")
			);
			
			$is_sell_order_updated = $this->Super_Insert("order",$data_update_for_sell_order,"order_id = ".$sell_order['order_id']);
			
			
			if($is_sell_order_updated->success){
				/* Commit and return  */
				$this->commit();
				return $is_sell_order_updated ;						
			}
			$this->rollBack();
			return $is_sell_order_updated ;
		}
		 
		$this->rollBack();
		return ;	
	}
	
	
	
	
	public function getAvailableBuyOrders($sell_order , $selling_price ,  $retail_price){
		
		$query = "
		
		SELECT `order`.*,  
			 ( CASE  
				WHEN order_pricing_type = 'dynamic' 
					THEN  $retail_price  - order_reference_price_modifier   
				ELSE  order_avg_cost_ea   
			   END ) AS `buying_price` FROM `order` 
			   
			   WHERE (order_product_id = ".$sell_order['order_product_id']." ) 
			   AND 
				(order_type = 'buy') AND 
				(order_status = 'processing') AND 
				(order_effective_till >= '".date('Y-m-d H:i:d')."') AND 
				(order_category = 'own_price') AND 
				(order_user_id != ".$sell_order['order_user_id'].") 
				having buying_price >= $selling_price 
				ORDER BY `buying_price` DESC
		";
		
		
		$data = $this->fetchAll($query );
		
		
		return $data ;
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/* Sell Order With Dynamic Pirce 
		STEPS : 
			1	=>	Calculate Selling Price for current Order
			2	=>	Get Buy Order Greater then Seller's Selling price Order by Price DESC
			
	
	 */
	 
	 
	private  function _processBuyOrderByCron($buy_order){
		
		 if($buy_order['order_id']!=1630){
			//return ;
			 
		 }
		 
		 
		$retail_price = $this->getRetailPrice($buy_order['product_type_metal_id']);
		
		
		
		if($buy_order['order_pricing_type']=="fixed"){
			$buying_price = (float)($buy_order['order_avg_cost_ea']); 
			$buying_qty_for_current_order = (int) $buy_order['order_quantity_remaining'] ;  
		}else{
			$buying_price = (float)($retail_price - $buy_order['order_reference_price_modifier']); 	
			$buying_qty_for_current_order = (int) ($buy_order['order_estimated_cost_remaining'] /$buying_price  ) ;  
		}
					
		
		$orders = $this->getAvailableSellOrders($buy_order['order_product_id'] , $retail_price , $buying_price ,$buy_order['order_user_id']);
		
		$remaining_qty = $buying_qty_for_current_order;//$buy_order['order_quantity'];
		
		$available_data = array(
			'qty'=>0,
			'average_cost' =>0
		);
		
		$flag_break = false ;
		//$data = $buy_order; 
		
		
		$data['order_quantity'] = $buying_qty_for_current_order;//$buy_order["order_quantity_remaining"];
		$data['order_user_id'] =  $buy_order['order_user_id'];
		$data['order_id'] =  $buy_order['order_id'];
		 
		
		if(empty($orders)){
			return ;
			
		}
		$this->beginTransaction() ;			
		
		
		
		
		
		/* Price Estimation  */
		foreach($orders as $key=>$sellOrder){
			
			$qty = (int) $sellOrder['order_quantity_remaining'] ;  
			
	
			
			
			if( isset($data['order_quantity']) and  (($data['order_quantity']-$available_data['qty'])-$qty) <=0){
				$qty = $data['order_quantity']-$available_data['qty'] ;
				$flag_break = true;
			}
			
		
			$available_data['average_cost'] +=  (float) ($qty*$sellOrder['selling_price']);
			$available_data['qty'] += $qty ;
			
			
			/* Sold and Process Sell Order buy Current Buy Order  */
			
			switch($sellOrder['order_type']){
				
				case 'sell':
				
					$buy_orders = $data;
					
					$is_process = $this->processBuyOrderWithStandingSellOrder($buy_orders ,$sellOrder , $qty );
					
					if($is_process->error){
						return $is_process ;
					}
					
					
					
				break;
				
				case 'retail':
					
					$buy_orders = $data;
					
					$is_process = $this->processBuyOrderWithRetailOrder($buy_orders, $qty);
					
					if($is_process->error){
						return $is_process ;
					}

					
					

				break;
				
				default : exit("Invalid Operation");
				
			}

			if( isset($data['qty']) and ($available_data['qty']) == $data['order_quantity'] or $flag_break){
				break;
			}
		}
		
		
		 
		if(!$available_data['qty']){
			return ; 
		}
		$available_data['average_cost'] = round($available_data['average_cost'] / $available_data['qty'] ,2);
		
		
		
			
		/* Update Buy Order Data */
		$data_to_update_for_buy_order = array(
			"order_quantity_remaining"=>"`order_quantity_remaining` - ".$available_data['qty'],
			"order_avg_cost_ea"=>$available_data['average_cost'],
			"order_estimated_cost"=>$available_data['average_cost']*$available_data['qty'],
			"order_updated"=>date('Y-m-d H:i:s')
		);
		
		if($available_data['qty']==$data['order_quantity']){
			
			$data_to_update_for_buy_order['order_status'] = "completed";
		}
		
		
		$is_update_buy_order = $this->Super_Insert("order",$data_to_update_for_buy_order ,"order_id=".$buy_orders['order_id']);
		
		
		if($is_update_buy_order->success){
			$this->commit();
			return $is_update_buy_order ;
		}
		$this->rollBack();
		return $is_update_buy_order ;
		
	}
	


	public function getAvailableSellOrders( $product_type ,$retail_price , $buying_price ,$user_id ){
		
		$query = "
			SELECT `order`.*, 
			( CASE 
				WHEN order_type = 'retail' 
					THEN $retail_price 
				WHEN order_pricing_type = 'dynamic' 
					THEN $retail_price - order_reference_price_modifier 
				ELSE order_avg_cost_ea 
			  END
			 ) AS `selling_price` 
			 FROM `order` WHERE 
			 (order_product_id = $product_type ) AND 
			 (order_type IN ('sell','retail') ) AND 
			 (order_status = 'processing') AND 
			 (( (order_effective_till >= '".date('Y-m-d H:i:s')."' and order_type= 'sell') or (order_type='retail') ) ) AND 
			 (order_user_id != $user_id ) 
			 having  $buying_price >= selling_price
			 ORDER BY `selling_price` ASC, `order_type` DESC 
		";
		
		
		return $this->fetchAll($query);
		
		
		
	}

	
		
	/* 
		STEPS : 
			
			1 ) Get All Retail order 
			2 ) Assign Serials to the Buyers 
			3 ) Subtract $qty from the remaining qty of seller remaining_qty
			4 ) Create entry in order_execution Table for seller order [sell Order] 
	*/ 
	private function processBuyOrderWithRetailOrder($buy_order , $qty ){
		
		$where = "product_type_id = ".$buy_order['order_product_id']." and order_user_id !=".$buy_order['order_user_id']." ";
		
		$all_orders = "
		SELECT `order_queue`.*,
		`order`.*, 
		`users`.`user_salutation`, `users`.`user_first_name`, `users`.`user_last_name`,
		`users`.`user_email`, `users`.`user_image`, 
		`product_type`.`product_type_metal_id`, `product_type`.`product_type_code`, 
		`product_type`.`product_type_title`, `product_type`.`product_type_weight`, `product_type`.`product_type_picture` 
		FROM `order_queue`
		INNER JOIN `order` ON order_id = queue_order_id
		INNER JOIN `users` ON user_id = order_user_id
		INNER JOIN `product_type` ON order_product_id = product_type_id 
		WHERE 
			(order_type = 'retail' ) AND 
			($where) AND 
			(order_status='processing') AND 
			(queue_status NOT IN ('canceled','completed')) 
			ORDER BY `product_type_id` ASC, `queue_cycle` ASC, `queue_order_priority` DESC, `order_created` ASC
		";
		
		$all_orders = $this->fetchAll($all_orders );
		$remaining_qty =  $qty; 
		
		$retail_price = 0;
		
		if(count($all_orders)>0){
			$retail_price = $this->getRetailPrice($all_orders[0]['product_type_metal_id']);
			
		}
		
		foreach($all_orders  as $queueElement){
			
			
			$available_qty_for_current_queue_element = $queueElement['queue_order_max_qty'] - $queueElement['queue_order_sold_qty'];
			
			
			if($remaining_qty!=0){
				
				if($available_qty_for_current_queue_element > $remaining_qty){
					/* Process with remaining qty */
					$qty_to_process_in_current_queue_element = $remaining_qty;
					
				}else{
					/* Process Current Queue element completly and subtract current queue qty from the remaining qty */
					$qty_to_process_in_current_queue_element = $available_qty_for_current_queue_element;
				}
				
				
				/*  Step 
				
					1 - Process qty_to_process_in_current_queue_element 
					2 - Update retail order table remaining qty
					3 - Create entry in order execution table 
					4 - Create entry in order execution serial table 
					5 - Assign involve serials to the buyer and also make serial active
					6 - 
				 */		
				 
				
				$all_serials  = $this->getStandingSellOrderSerials($queueElement['order_id'], $qty_to_process_in_current_queue_element);
				
				$serial_ids = array(
					'order_serial_id'=>array(),
					'order_serial_serial_id'=>array()
				);

				foreach($all_serials  as  $serial){
					$serial_ids['order_serial_id'][] = $serial['order_serial_id'];
					$serial_ids['order_serial_serial_id'][] = $serial['order_serial_serial_id'];
				}
				
	
	
				/* Update order_serials status with completed */
				$is_serial_status_changed = $this->Super_Insert("order_serials",
														array(
															"order_serial_status"=>"completed",
															"order_serial_updated"=>date('Y-m-d H:i:s')
														),"order_serial_id IN (".implode(",",$serial_ids['order_serial_id']).")");
														
				
				if($is_serial_status_changed->success){
					
					/* Update Serial Owner with the Buyer Id */
					$is_owner_updated = $this->Super_Insert("inventory_serial",array(
															"serial_owner"=>$buy_order['order_user_id'],
															"serial_status"=>"active"
														),
														"serial_id IN (".implode(",",$serial_ids['order_serial_serial_id']).")"
													);
					
					if($is_owner_updated->success){
						
						/*  Make Entry in Order execute  table for the current sold qty and buy oty  */
						
						$data_for_order_execute_table = array(
							"execute_sell_order_id"=>$queueElement['order_id'],
							"execute_buy_order_id"=>$buy_order["order_id"],
							"execute_quantity"=>$qty_to_process_in_current_queue_element,
							"execute_total_price"=>(float)($retail_price*$qty_to_process_in_current_queue_element),
							"execute_status"=>"completed",
							"execute_created"=>date("Y-m-d H:i:s"),
							
						);
						
						$is_inserted_order_execute_data = $this->Super_Insert("order_execute",$data_for_order_execute_table); 
						
						if($is_inserted_order_execute_data->success){
							
							foreach($all_serials as $serial){
								
								$order_execute_serial_data = array(
									"execute_serial_execute_id"=>$is_inserted_order_execute_data->inserted_id,
									"execute_serial_serial_id"=>$serial["order_serial_serial_id"],
								);
								
								$is_inserted_order_execute_serial = $this->Super_Insert("order_execute_serials" , $order_execute_serial_data);
								
								if($is_inserted_order_execute_serial->error){
									return $is_inserted_order_execute_serial;
								}
							}
							
							
							/* Update Buy and Sell Order table entries  
								1 Update Sell Order 
							*/
							
							$data_update_to_sell_order = array(
								'order_quantity_remaining'=>"(order_quantity_remaining - ".count($all_serials).")",
								'order_updated'=>date('Y-m-d H:i:s')
							);
							
							if($queueElement['order_quantity_remaining']<=$qty_to_process_in_current_queue_element){
								$data_update_to_sell_order['order_status'] = "completed";
								
							}
							
							$is_sell_order_updated = $this->Super_Insert("order",$data_update_to_sell_order,"order_id = ".$queueElement['order_id']);
							
							if($is_sell_order_updated->success){
								
								/* Update Retail Queue Element  */
								$is_updated_queue_element = array(
									"queue_order_sold_qty"=>"(queue_order_sold_qty + ".$qty_to_process_in_current_queue_element.")",
								);
								
								if($queueElement['queue_order_max_qty'] == ($qty_to_process_in_current_queue_element + $queueElement['queue_order_sold_qty'])){
									/* Current Queue Element is completly executed  */
									$is_updated_queue_element['queue_status'] = "completed";
								}
								
								$is_updated_queue_element = $this->Super_Insert("order_queue",$is_updated_queue_element,"queue_id = ".$queueElement['queue_id']);
								
								if($is_updated_queue_element->success){
									$remaining_qty-=$qty_to_process_in_current_queue_element;
									continue;
								}
								return $is_updated_queue_element ;
							}
							return $is_sell_order_updated ;
						}
						return $is_inserted_order_execute_data;
					}
					return $is_owner_updated;
				}
				return $is_serial_status_changed ;		
			}
			break ;
		}
		
		return (object) array('success'=>true,"error"=>false,"message"=>"Order Successfully processed via retail order");
}
	
	
	
	
	
	

	/* 
	STEPS : 
		
		1 ) Get Sell Order Serials Equal to the $qty
			2 ) Assign Serials to the Buyers 
			3 ) Subtract $qty from the remaining qty of seller remaining_qty
			4 ) Create entry in order_execution Table for seller order [sell Order] 
	
	
	
	*/ 
	
	private function processBuyOrderWithStandingSellOrder($buy_order , $sell_order , $qty ){
		
		$all_serials  = $this->getStandingSellOrderSerials($sell_order['order_id'], $qty);
		
		if(!count($all_serials)){
			return false; 
		}
		
		
		$this->beginTransaction();
		
		
		$serial_ids = array(
			'order_serial_id'=>array(),
			'order_serial_serial_id'=>array()
		);

		foreach($all_serials  as  $serial){
			$serial_ids['order_serial_id'][] = $serial['order_serial_id'];
			$serial_ids['order_serial_serial_id'][] = $serial['order_serial_serial_id'];
		}
		
		
		
		
		
		/* Update order_serials status with completed */
		$is_serial_status_changed = $this->Super_Insert("order_serials",array("order_serial_status"=>"completed","order_serial_updated"=>date('Y-m-d H:i:s')),"order_serial_id IN (".implode(",",$serial_ids['order_serial_id']).")");
		
		if($is_serial_status_changed->success){
			
			/* Update Serial Owner with the Buyer Id */
			$is_owner_updated = $this->Super_Insert("inventory_serial",array(
													"serial_owner"=>$buy_order['order_user_id'],
													"serial_status"=>"active"
												),
												"serial_id IN (".implode(",",$serial_ids['order_serial_serial_id']).")"
											);
			
			if($is_owner_updated->success){
				
				
				/*  Make Entry in Order execute  table for the current sold qty and buy oty  */
				
				
				$data_for_order_execute_table = array(
					"execute_sell_order_id"=>$sell_order['order_id'],
					"execute_buy_order_id"=>$buy_order["order_id"],
					"execute_quantity"=>$qty,
					"execute_total_price"=>(float)($sell_order['selling_price']*$qty),
					"execute_status"=>"completed",
					"execute_created"=>date("Y-m-d H:i:s"),
				);
				
				$is_inserted_order_execute_data = $this->Super_Insert("order_execute",$data_for_order_execute_table); 
				
				
				if($is_inserted_order_execute_data->success){
					
					foreach($all_serials as $serial){
						
						$order_execute_serial_data = array(
							"execute_serial_execute_id"=>$is_inserted_order_execute_data->inserted_id,
							"execute_serial_serial_id"=>$serial["order_serial_serial_id"],
						);
						
						$is_inserted_order_execute_serial = $this->Super_Insert("order_execute_serials" , $order_execute_serial_data);
						
						if($is_inserted_order_execute_serial->error){
							$this->rollBack();
							return $is_inserted_order_execute_serial;
						}
					}
					
					
					/* Update Buy and Sell Order table entries  
						1 Update Sell Order 
					*/
					
					
					
					
					$data_update_to_sell_order = array(
						'order_quantity_remaining'=>"(`order_quantity_remaining` - ".count($all_serials).")",
						'order_updated'=>date('Y-m-d H:i:s')
					);
					
					if($sell_order['order_quantity_remaining']<=$qty){
						$data_update_to_sell_order['order_status'] = "completed";
						
					}
					
					$is_sell_order_updated = $this->Super_Insert("order",$data_update_to_sell_order,"order_id = ".$sell_order['order_id']);
					
					if($is_sell_order_updated->success){
						$this->commit();
						
					}else{
						$this->rollBack();
					}
					
					
					return $is_sell_order_updated ;
				}
				$this->rollBack();
				return $is_inserted_order_execute_data;
			}
			$this->rollBack();
			return $is_owner_updated;
		}
			$this->rollBack();
		return $is_serial_status_changed ;
		
		
		
	}
	
	
	
	
	
	
		
	 
	
	
	public function getAllEffectiveOrders(){

		$fields_buy_order = $this->_getFields('buy');
		
		$query = " select order_id  from  `order`";
		$query.= " inner join users on user_id = order_user_id";
		$query.= " inner join product_type on product_type_id = order_product_id";
		$query.= " inner join metal_type on product_type_metal_id = metal_type_id";
		$query.= " where user_status = '1' and user_verification_status='1' "; /* User Verification Condition */
		$query.= " and order_type IN ('buy','sell')  and order_status = 'processing'   "; /* Buy Order Conditions */
		$query.= " and order_effective_till >= '".date("Y-m-d H:i:s")."' "; /* Buy Order Conditions */
		$query.= " order by order_created"; /* Buy Order Conditions */
		
		
		$processing_orders = $this->fetchAll($query);
		
		return $processing_orders ;
		
	}
	
	
	public function getEffectiveOrder($order_id ){
		
		$fields_buy_order = $this->_getFields('buy');
		
		$query = " select $fields_buy_order   from  `order`";
		$query.= " inner join users on user_id = order_user_id";
		$query.= " inner join product_type on product_type_id = order_product_id";
		$query.= " inner join metal_type on product_type_metal_id = metal_type_id";
		$query.= " where user_status = '1' and user_verification_status='1' "; /* User Verification Condition */
		$query.= " and order_type IN ('buy','sell')  and order_status = 'processing'   "; /* Buy Order Conditions */
		$query.= " and order_effective_till >= '".date("Y-m-d H:i:s")."' "; /* Buy Order Conditions */
		$query.= " and order_id = $order_id "; /* Buy Order Conditions */
		$query.= " order by order_created"; /* Buy Order Conditions */
		
		
		$processing_orders = $this->fetch($query);
		
		return $processing_orders ;
		
		
	}
	
	
	
	/* Return all Proce */
	private function _getProcessingBuyOrder(){
 
		$fields_buy_order = $this->_getFields('buy');
		
		$query = " select $fields_buy_order  from  `order`";
		$query.= " inner join users on user_id = order_user_id";
		$query.= " inner join product_type on product_type_id = order_product_id";
		$query.= " inner join metal_type on product_type_metal_id = metal_type_id";
		$query.= " where user_status = '1' and user_verification_status='1' "; /* User Verification Condition */
		$query.= " and order_type = 'buy' and order_category='own_price' and order_status = 'processing'   "; /* Buy Order Conditions */
		
		$processing_buy_orders = $this->fetchAll($query);
		
		return $processing_buy_orders ;
		
	}
	
	

	
	
	
	
	

	
	
	
	private function _getFields($type){
		
		$fields_required = array(
			'buy'=>array(
				'order'=>array(
								"order_id",
								"order_user_id",
								"order_product_id",
								"order_type",
								"order_category",
								"order_effective_till",
								"order_quantity",
								"order_quantity_remaining",
								"order_pricing_type",
								"order_avg_cost_ea",
								"order_reference_price_modifier",
								"order_estimated_cost",
								"order_estimated_cost_remaining",
								"order_delivery_address",
								"order_priority_fee",
								"order_status",
								"order_created",
								"order_updated"),
				'users'=>array(
								"user_id",
								"user_code",
								"user_salutation",
								"user_email",
								"user_first_name",
								"user_last_name",
								"user_delivery_address",
								"user_referrer_code",
								"user_status",
								"user_verification_status",
							),
				'product_type'=>array(
								"product_type_id",
								"product_type_metal_id",
								"product_type_code",
								"product_type_title",
								"product_type_weight",
								"product_type_commission_fee",
								"product_type_referral_fee",
								"product_type_retail_price_factor",
								"product_type_restocking_factor",
								"product_type_buy_back",
								"product_type_picture"
				),
				'metal_type'=>array(
								"metal_type_id",
								"metal_type_name",	
					
				)
			)
		);
		
		$buy_order_fields = array();
		
		foreach($fields_required['buy'] as $table => $fields){
			$buy_order_fields[]= "`$table`.`".implode("`,`$table`.`",$fields)."`";
		}
		return implode(",",$buy_order_fields); 
	}
	
	
	
			
	
	
	
	
	
	
	
	
	
 
}/* End Class DB */










































function updatePriceConfig(){
	
	$db = new DbAdapter();
	/* Get Last Config Dates for the price configs */
	$config_data = $db->Super_Get("price_config" , "pc_id  <= 4","all");
	
	$time = date("Y-m-d H:i:s", strtotime($config_data[0]['pc_last_update_time'])+ $config_data[0]['pc_update_time']);
	
	
	foreach($config_data as $configKey => $configValue){
		
		/* Check Last Update time and time Setting for update intervel */
		
		if(time()>= strtotime($configValue['pc_last_update_time'])+ $configValue['pc_update_time']){
			
			/* Need to update the API prices */	
			_updatepricechange($configValue['pc_id'] , $configValue);
			
		}
	}

	return true ;
	
}

 



function _updatepricechange($config_id,$price_settings = false){
	
	$db = new DbAdapter();
	
	switch($config_id){
		case 1:$api_link = 'http://gold-feed.com/iframe/paid/55669856231478956501/55548795685212587801gold.php';break;
		case 2:$api_link = 'http://gold-feed.com/iframe/paid/55669856231478956501/55548795685212587801goldask.php';break;
		case 3:$api_link = 'http://gold-feed.com/iframe/paid/55669856231478956501/55548795685212587801silver.php';break;
		case 4:$api_link = 'http://gold-feed.com/iframe/paid/55669856231478956501/55548795685212587801silverask.php';break;
	}
	
	$new_price = file_get_contents($api_link);
	$new_price = round ($new_price , 2);
	 
	if($price_settings['pc_status']=='1'){

		/* Log the Current Record */
		$log_to_insert = array(
			"pc_id"=>$price_settings["pc_id"],
			"pc_updated"=>$price_settings["pc_last_update_time"],
			"pc_price"=>$price_settings["pc_price"],
			"pc_change"=>$price_settings["pc_change"],
			"pc_change_percent"=>$price_settings["pc_change_percent"],
		);
		
		$is_insert_log = $db->Super_Insert("price_log",$log_to_insert);
		
		if(is_object($is_insert_log) and $is_insert_log->success){
			/* Update Gold Price in database */
			$is_update_price = $db->Super_Insert( "price_config",
				array(
					"pc_last_update_time"=>date('Y-m-d H:i:s'),
					"pc_price"=>$new_price,
					"pc_change"=>getChangeInPrice($price_settings['pc_price'],$new_price),
					"pc_change_percent"=>getChangeInPrice($price_settings['pc_price'],$new_price,"percent"),
				),"pc_id = ".$config_id);
				
			if(is_object($is_update_price) and $is_update_price->success){
				return (object)array("success"=>true,"error"=>false,"message"=>"Price Configs Successfully Uploaded");
			}else{
				 $is_update_price ;
			}
		}
	}
	return true ;
}


	

function addLog($price_data){
		
$log_to_insert = array(
	"pc_id"=>$price_data["pc_id"],
	"pc_updated"=>$price_data["pc_last_update_time"],
	"pc_price"=>$price_data["pc_price"],
	"pc_change"=>$price_data["pc_change"],
	"pc_change_percent"=>$price_data["pc_change_percent"],
);
		array_walk($log_to_insert,"to_string");
			
		$query = "insert into price_log (".implode(",",array_keys($log_to_insert)).") values(".implode(",",$log_to_insert).") ";
		
		mysql_query($query) or  die(mysql_error()) ;
		
		return  (object)array("success"=>true,"error"=>false,"message"=>'successfully ');
		
	}
	
function to_string(&$value){
	$value = (string)'"'.$value.'"';
}

