<?php
$genMailText  = function (\ilObjUser $user, \ilObjCourse $crs) {
	
	$txt = "Hallo "
		.$user->getFullName()
		.", Sie wurden aus dem Kurs "
		.$crs->getTitle()
		." ausgetragen.";
	return $txt;
}
?>