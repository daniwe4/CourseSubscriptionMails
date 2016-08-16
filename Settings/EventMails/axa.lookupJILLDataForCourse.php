<?php
	
	global $j_customer;
	global $tree, $ilDB;


	$cfg_file = './noUI/include/config.php';
	require($cfg_file); //$JILL_CUSTOMER_PATH
	$inc_cfg_path = dirname(realpath($cfg_file)) 
		.'/../..' 
		.$JILL_CUSTOMER_PATH;

	if(!$j_customer) {
		require($inc_cfg_path .'/config.customer.php'); //$j_customer
	}
	
	
	//get container-reference to this object
    $query_referencing = 'SELECT obj_id FROM container_reference'
    	.' WHERE target_obj_id = '.$crs->getId();

    $query = 'SELECT ref_id FROM object_reference'
    	.' WHERE obj_id IN (' .$query_referencing .')';

    $res = $ilDB->query($query);
    $row = $ilDB->fetchAssoc($res);
	
	$ref_id = $row['ref_id'];

	//get parentObj of reference (==BiPro)	
	require_once("./Modules/StudyProgramme/classes/class.ilObjStudyProgramme.php");
	$parent_data = $tree->getParentNodeData($ref_id);
	$prg = ilObjStudyProgramme::getInstanceByRefId($parent_data["ref_id"]);

	//get type of bipro
	require_once("./Services/AXA/Utils/classes/class.axaSettings.php");
	$axaSettings = axaSettings::getInstance();
	$subtype_id = $prg->getSubTypeId();
    $subtype = $axaSettings->getConstById($subtype_id);
 	$jill_crs_key = strtoupper(str_replace('prg_amd_type_', '', $subtype));

	//include content/booking.desc_mapping.php to get $DESC_MAPPING
	require($inc_cfg_path .'/content/booking.descmapping.php');
	//include contents according to type
	$f = $DESC_MAPPING[$jill_crs_key];
	require($inc_cfg_path .'/content/coursedescription/'.$f);


	//get CSN Data, if available

	//print_r($COURSEDESC);
	//die();

?>
