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

	if(! $ref_id) { //crs is not referenced; permanent course? no mail, anyhow!
		return '';
	}

	//get parentObj of reference (==BiPro)	
	require_once("./Modules/StudyProgramme/classes/class.ilObjStudyProgramme.php");
	$parent_data = $tree->getParentNodeData($ref_id);
	$prg = ilObjStudyProgramme::getInstanceByRefId($parent_data["ref_id"]);

	//get type of bipro
	require_once("./Services/AXA/Utils/classes/class.axaSettings.php");
	$axaSettings = axaSettings::getInstance();
	$subtype_id = $prg->getSubTypeId();
    $subtype = $axaSettings->getConstById($subtype_id);
    
    if($subtype_id == 0 || $subtype == '') {
    	return '';
    }
 	
 	$jill_crs_key = strtoupper(str_replace('prg_amd_type_', '', $subtype));

	//include content/booking.desc_mapping.php to get $DESC_MAPPING
	require($inc_cfg_path .'/content/booking.descmapping.php');
	//include contents according to type
	$f = $DESC_MAPPING[$jill_crs_key];
	require($inc_cfg_path .'/content/coursedescription/'.$f); //provides $COURSEDESC


	//to german date
	//DateTime::createFromFormat('Y-m-d', $dat)->format('d.m.Y');
	$COURSEDESC['startdate'] = $crs->getCourseStart()->get(IL_CAL_DATE);
	$COURSEDESC['startdate'] = DateTime::createFromFormat('Y-m-d', $COURSEDESC['startdate'])->format('d.m.Y');
	
	require_once("./Services/AXA/Utils/classes/class.axaCourseUtils.php");
	$cutils = axaCourseUtils::getInstance($crs->getId(), axaCourseUtils);
	$COURSEDESC['courseStartTime'] = $cutils->getCourseStartTime();
    $COURSEDESC['courseEndTime'] = $cutils->getCourseEndTime();




	//get texts

	require($inc_cfg_path .'/content/mail/mail.footer.php'); //$MAIL_FOOTER
	
	//course-specific mail-template:
	switch ($jill_crs_key) {
		case 'OD01': 
		case 'OD02': 
		case 'OD03': 
		case 'OD04': 
			$course_type = 'webinar';
			$section = 'od';
			//$mail_template = 'invite' | 'storno' | 'waiting' | 'waiting_cancel'
			break;

		case 'ODFINAL': 
			$course_type = 'f2f';
			$section = 'od';
			//$mail_template = 'invite' | 'storno' | 'waiting' | 'waiting_cancel'
			break;

		case 'FK01': 
		case 'FK02': 
		case 'FK03': 
		case 'FK04': 
		case 'FK05': 
			$course_type = 'webinar';
			$section = 'fk';
			//$mail_template = 'invite' | 'storno' | 'waiting' | 'waiting_cancel'
			break;
		case 'FKFINAL': 
			$course_type = 'f2f';
			$section = 'fk';
	}


	if($course_type == 'webinar') {
		
		//get CSN Data
		$res = $ilDB->query("SELECT ref_id FROM object_reference WHERE obj_id = ".$ilDB->quote($crs->getId()));
        $ret = $ilDB->fetchAssoc($res);
		$crs_refId = $ret["ref_id"];
        
		$children = $tree->getChilds($crs_refId, 'title');
		$csn_ref = null;
		foreach ($children as $cnt => $entry) {
			if($entry['type'] === 'xcsn') {
				$csn_ref_id = $entry['ref_id'];
				$csn_obj_id = $entry['obj_id'];
			}
		}

		include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/CSN/classes/class.ilObjCSN.php");
		$csn = new ilObjCSN($csn_ref_id);
		//$csn->doRead();
		$csn_data = $csn->csnSettings();
		
		$COURSEDESC['CSN_PIN'] = $csn_data->pin();
		$COURSEDESC['CSN_FON'] = $csn_data->phoneNumber();
		$COURSEDESC['CSN_LINK'] = $csn_data->link();


	}



	$tpl_path = '/content/mail/'.$course_type.'.'.$mail_template.'.'.$section.'.php';
	require($inc_cfg_path .$tpl_path); //$MAIL_TEMPLATE

	$MAIL_TEMPLATE .= $MAIL_FOOTER;

	//print_r($COURSEDESC);
	//die();

?>
