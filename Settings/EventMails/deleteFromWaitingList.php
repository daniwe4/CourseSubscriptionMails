<?php
$genMailText  = function (\ilObjUser $user, \ilObjCourse $crs) {
	
	$txt = "Hallo "
		.$user->getFullName()
		.", Sie wurden von der Warteliste des Kurses "
		.$crs->getTitle()
		." ausgetragen.";
	return $txt;
}
?>