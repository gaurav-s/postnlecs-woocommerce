<?php 
class ecsOrderSettings {
   
    public static $instance;
	
	
    
	public static function init()
    {
        if ( is_null( self::$instance ) )
            self::$instance = new EcsOrderSettings();
        return self::$instance;
    }
    
    private function __construct()
    {
     
		
    }
    
    public function loadOrderSettings($settingID)
    {
    
			// find list of states in DB
			global $wpdb;
			$table_name = $wpdb->prefix . 'ecsmeta';	
			$qrymeta    = "SELECT * FROM $table_name " . "WHERE settingid = $settingID  ";
			$statesmeta = $wpdb->get_results($qrymeta);
			
		return 	 $statesmeta; 
	  
    }
    
	  public function getSettingId()
    {
      
		global $wpdb;
		$table_name_ecs = $wpdb->prefix . 'ecs';
		$qry            = "SELECT * FROM  	$table_name_ecs " . "WHERE keytext ='OrderExport' ORDER BY id DESC  LIMIT 1 ";
        $states         = $wpdb->get_results($qry);
        $settingID      = '';
        foreach ($states as $k) {
            $settingID = $k->id;
        }
		return 	  $settingID;
    }
	
	 public function saveSettings()
    {
			global $wpdb;
			$table_name_ecs = $wpdb->prefix . 'ecs';
			$wpdb->insert($table_name_ecs, array(
							'type' => '4',
							'enable' => 'true',
							'keytext' => 'OrderExport' // ... and so on
				));
			$id         = $wpdb->insert_id;
			return $id;
    }
	
	 public function saveSettingsValues($id,$keytext,$value)
    {
			global $wpdb;
			$table_name = $wpdb->prefix . 'ecsmeta';	
			$wpdb->insert($table_name, array(
							'settingid' => $id,
							'keytext' => $keytext,
							'value' => $value 
						));
    }
	
	public function updateSettingsValues($id,$value)
    {
			global $wpdb;
			$table_name = $wpdb->prefix . 'ecsmeta';	
			$wpdb->query($wpdb->prepare("UPDATE $table_name  SET value = '$value'
	WHERE id= %d", $id));
    }
    
	public function getSettingValues($settingID)
    {
			global $wpdb;
			$table_name = $wpdb->prefix . 'ecsmeta';	
			$qrymeta    = "SELECT * FROM $table_name " . "WHERE settingid = $settingID  ";
						$statesmeta = $wpdb->get_results($qrymeta);
						return $statesmeta;
    }
	
	public function getShippingTypesList()
    {
      $ShippingTypes = array("flat_rate","free_shipping","international_delivery","local_delivery","local_pickup","disabled");
	  return $ShippingTypes;
    }
	
	 public function getOrderStatusList()
    {
      $OrderStatus = array("on-hold","pending","failed","processing","completed","refunded","cancelled");
	  return $OrderStatus;
    }
    
	public function displayOrderExpSettings($Cron,$Path, $Shipping, $Status, $no   ) {
		
		 echo '<div class="form-group">
<label class="col-md-4 control-label" for="textinput">Path</label>  
<div class="col-md-4">
<input id="textinput" name="Path" type="text" placeholder="Path" required="true" class="form-control input-md" value=' . $Path . '>
<span class="help-block">For example /orders</span>  
</div>
</div>
';
        echo '<div class="form-group">
<label class="col-md-4 control-label" for="textinput">Number of Orders per file</label>  
<div class="col-md-4">
<input id="textinput" name="no" type="number" placeholder="number" required="true" class="form-control input-md" value=' . $no . '>
<span class="help-block">For example 3,5,10</span>  
</div>
</div>
';
	
       if ($Cron == '') {
            echo '
<!-- Select Basic -->
<div class="form-group">
<label class="col-md-4 control-label" for="selectbasic">Cron schedule</label>
<div class="col-md-4">
<select id="selectbasic" name="Cron" class="form-control">
<option value="15min">15 min</option>
<option value="30min">30 min</option>
<option value="1hour"  >1 hour</option>
<option value="2hour">2 hour</option>
<option value="4hour">4 hour</option>
<option value="1day">1 daily</option>
<option value="0" selected="selected" >Stop</option>
</select>
<span class="help-block">Pick a schedule</span>  
</div>
</div>';
        } else {
            echo '
<!-- Select Basic -->
<div class="form-group">
<label class="col-md-4 control-label" for="selectbasic">Cron schedule</label>
<div class="col-md-4">
<select id="selectbasic" name="Cron" class="form-control">';
            if ($Cron == "15min") {
                echo '     <option value="15min" selected="selected" >15 min</option>';
            } else {
                echo '     <option value="15min">15 min</option>';
            }
            if ($Cron == "30min") {
                echo '  <option value="30min" selected="selected">30 min</option>';
            } else {
                echo '  <option value="30min">30 min</option>';
            }
            if ($Cron == "1hour") {
                echo '    <option value="1hour" selected="selected"  >1 hour</option>';
            } else {
                echo ' <option value="1hour">1 hour</option>';
            }
            if ($Cron == "2hour") {
                echo '     <option value="2hour" selected="selected" >2 hour</option>';
            } else {
                echo '  <option value="2hour">2 hour</option>';
            }
            if ($Cron == "4hour") {
                echo '  <option value="4hour" selected="selected"  >4 hour</option>';
            } else {
                echo '<option value="4hour">4 hour</option>';
            }
            if ($Cron == "1day") {
                echo '    <option value="1day" selected="selected"  >1 daily</option>';
            } else {
                echo '  <option value="1day">1 daily</option>';
            }
            if ($Cron == "0") {
                echo '    <option value="0" selected="selected"  >Stop</option>';
            } else {
                echo ' <option value="0">Stop</option>';
            }
            echo '
</select>
<span class="help-block">Pick a schedule</span>  
</div>
</div>';
        }
        $OrderStatus = $this->getOrderStatusList();
		
		if ($Status == '') {
            echo '<!-- Select Basic -->
				<div class="form-group">
				<label class="col-md-4 control-label" for="selectbasic">Order Status</label>
				<div class="col-md-4">
				<select id="selectbasic" name="Status[]" class="form-control"  required="true"  multiple="multiple" >
				';
				foreach($OrderStatus as $OrderStatusValue) {
								
									echo '            <option value="'.$OrderStatusValue.'">'.$OrderStatusValue.'</option>';
							
							
							}
				
				echo '
				</select>
				<span class="help-block">Only orders with selected status will be exported</span>  
				 
				</div>
				</div>
				';
        } else {
            echo '<!-- Select Basic -->
<div class="form-group">
<label class="col-md-4 control-label" for="selectbasic">Order Status</label>
<div class="col-md-4">
<select id="selectbasic" name="Status[]" class="form-control"  required="true"   multiple="multiple">';
            echo '';
			
			$selectedOrderStatus = explode(":",$Status);
			
			foreach($OrderStatus as $OrderStatusValue) {
				if(in_array($OrderStatusValue,$selectedOrderStatus)) {
					echo '            <option value="'.$OrderStatusValue.'" selected="selected" >'.$OrderStatusValue.'</option>
';
					}
				else {
					echo '            <option value="'.$OrderStatusValue.'">'.$OrderStatusValue.'</option>';
				}
					
			
			}
			
            echo ' </select>
<span class="help-block">Only orders with selected status will be exported </span>  
 
</div>
</div>';
        }
        
        
        $ShippingTypes = $this->getShippingTypesList();
        if ($Shipping == '') {
            echo '<!-- Select Basic -->
			<div class="form-group">
			<label class="col-md-4 control-label" for="selectbasic"> Shipping Method </label>
			<div class="col-md-4">
			<select id="selectbasic" name="Shipping[]" class="form-control" required="true" multiple="multiple">';
			
			foreach($ShippingTypes as $ShippingTypesValue) {
											
												echo '            <option value="'.$ShippingTypesValue.'">'.$ShippingTypesValue.'</option>';
										
										
										}
				echo '
					</select>
					<span class="help-block">Only orders with selected status will be exported</span>  
					</div>
					</div>
				';
						
            
        } else {
            
          
            echo '<!-- Select Basic -->
			<div class="form-group">
			<label class="col-md-4 control-label" for="selectbasic"> Shipping Method </label>
			<div class="col-md-4">
			<select id="selectbasic" name="Shipping[]" class="form-control" required="true" multiple="multiple" > ';
			$selectedShippingType = explode(":",$Shipping);
				foreach($ShippingTypes as $ShippingTypesValue) {
				if(in_array($ShippingTypesValue,$selectedShippingType)) {
					echo '            <option value="'.$ShippingTypesValue.'" selected="selected" >'.$ShippingTypesValue.'</option>
';
					}
				else {
					echo '            <option value="'.$ShippingTypesValue.'">'.$ShippingTypesValue.'</option>';
				}
					
			
			}
            echo '
</select>
<span class="help-block"> Only orders with selected status will be exported</span>  


</div>
</div>';
        }
        global $wpdb;
        $table_name_ecs = $wpdb->prefix . 'ecs';
        $querylast      = "SELECT * FROM $table_name_ecs " . "WHERE keytext = 'lastOrdername'  ";
        $statesmeta     = $wpdb->get_results($querylast);
        $lastname       = '';
        if (count($statesmeta) > 0) {
            foreach ($statesmeta as $k) {
                echo '<!-- Text input-->
<div class="form-group">
<label class="col-md-4 control-label  " for="textinput"  style="cursor:default"> Last Processed File</label>  
<div class="col-md-4">
<label class="col-md-4 control-label  " for="textinput" style="cursor:default ; padding-left:0px ; margin-left:0px ;">' . $k->type . ' </label>  
<span class="help-block"></span>  
</div>
</div>';
            }
        } else {
            echo '<!-- Text input-->
<div class="form-group">
<label class="col-md-4 control-label " for="textinput" style="cursor:default"> Last Processed File</label>  
<div class="col-md-4">
<label class="col-md-4 control-label " for="textinput"  style="cursor:default"> </label>  
<span class="help-block"></span>  
</div>
</div>';
        }
	
	
	}
	
   
    
    




}

?>