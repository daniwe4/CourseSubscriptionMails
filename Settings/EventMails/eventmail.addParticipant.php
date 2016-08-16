<?php
$genMailText  = function (\ilObjUser $user, \ilObjCourse $crs) {
	
	//customer specific:
	require(dirname(__FILE__) .'/axa.lookupJILLDataForCourse.php'); //array $COURSEDESC
	require(dirname(__FILE__) .'/axa.footer.php'); //string $MAIL_FOOTER
	


$MAIL_TEMPLATE =  <<<TXT
Sehr geehrte/r [VORNAME] [NACHNAME],
Sie haben sich zum Online-Seminar [TITEL] 
der Schulungsreihe Maklerzertifizierung Öffentlicher Dienst 2016 angemeldet. 
Sie werden unter der Rufnummer [RUFNUMMER-TEILNEHMER] zu dem von Ihnen gewünschten Termin angerufen.

Termin: 
[DATUM], [STARTZEIT] Uhr – [ENDZEIT] Uhr

Dieses Online-Seminar ist für Makler, Mehrfachagenten und deren Mitarbeiter. 
Die Dauer beträgt ca. 50 Minuten.  Sie bekommen für Ihre Teilnahme pro Modul 
einen Weiterbildungspunkt im Rahmen der Brancheninitiative "gut beraten".
Die Referenten sind Spezialisten aus der Makler-Direktbetreuung von AXA.

Es erwarten Sie die folgenden Lerninhalte:
[INHALTE]

Die Online-Seminare werden durch Live-Präsentationen via Internet unterstützt - 
bitte schalten Sie zu den o.g. Terminen daher auch Ihren Rechner ein 
und klicken Sie auf den Link www.dbvinfo.de.

Tragen Sie dort bitte Ihren vollständigen Namen und folgende PIN ein: [PIN]

Für den Fall, dass Sie nicht pünktlich den Telefonanruf des Konferenzsystems 
entgegennehmen können, können Sie sich auch selbst über folgende Rufnummer 
in die laufende Konferenz einwählen: 0049 211 54079964. 
Geben Sie nach Aufforderung über die Telefontastatur folgende PIN ein: [PIN]

Für eine nachträgliche Änderung Ihrer Registrierung klicken Sie bitte auf folgenden Link: 


Bei Rückfragen schreiben Sie uns an zertifizierung@dbv.de.

Wir freuen uns auf Ihre Teilnahme und wünschen Ihnen bis dahin eine erfolgreiche Zeit.

Ihr Team der Maklerzertifizierung ÖD

TXT;

	$txt = $MAIL_TEMPLATE;
	$txt = str_replace('[VORNAME] [NACHNAME]', $user->getFullName(), $txt);
	$txt = str_replace('[RUFNUMMER-TEILNEHMER]', $user->getPhoneOffice(), $txt);
	//$txt = str_replace('[DATUM]', $crsdate, $txt);
	$txt = str_replace('[INHALTE]', strip_tags($COURSEDESC['INHALTE']), $txt);
	//$txt = str_replace('[PIN]', '', $txt);


	$txt = $txt .$MAIL_FOOTER;

	return $txt;
}
?>