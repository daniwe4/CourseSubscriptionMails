<?php
$genMailText  = function (\ilObjUser $user, \ilObjCourse $crs) {
	
	$txt = "Hallo "
		.$user->getFullName()
		.", Sie haben sich erfolgreich in den Kurs "
		.$crs->getTitle()
		." eingeschrieben.";
	return $txt;
}
?>