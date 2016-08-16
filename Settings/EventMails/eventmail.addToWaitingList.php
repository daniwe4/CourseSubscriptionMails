<?php
$genMailText  = function (\ilObjUser $user, \ilObjCourse $crs) {
	
	$txt = "Hallo "
		.$user->getFullName()
		.", Sie befinden sich jetzt aufder Warteliste des Kurses "
		.$crs->getTitle();
		
	return $txt;
}
?>