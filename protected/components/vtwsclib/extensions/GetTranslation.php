<?php
/************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  JPL TSolucio, S.L. Open Source
 * The Initial Developer of the Original Code is  JPL TSolucio, S.L.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_gettranslation($totranslate, $language, $module, $user){
	
	global $log,$adb,$default_language;
	$log->debug("Entering function vtws_gettranslation");

	if (!is_array($totranslate)) $totranslate=array($current_language);
	$mod_strings=array();
	$app_strings=array();
	// $app_strings
	$applanguage_used = $language;
	if (file_exists("include/language/$language.lang.php"))
		@include("include/language/$language.lang.php");
	else {
		$log->warn("Unable to find the application language file for language: ".$language);
		$applanguage_used = $default_language;
		if (file_exists("include/language/$default_language.lang.php"))
			@include("include/language/$default_language.lang.php");
		else
			$applanguage_used=false;
	}

	// $mod_strings
	$modlanguage_used = $language;
	if (file_exists("include/$module/language/$language.lang.php"))
		@include("include/$module/language/$language.lang.php");
	else {
		$log->warn("Unable to find the module language file for language/module: $language/$module");
		$modlanguage_used = $default_language;
		if (file_exists("include/$module/language/$default_language.lang.php"))
			@include("include/$module/language/$default_language.lang.php");
		else
			$modlanguage_used = false;
	}
	
	if (!$applanguage_used and !$modlanguage_used)
	  return $totranslate;  // We can't find language file so we return what we are given
	
	$translated=array();
	foreach ($totranslate as $key=>$str) {
		$translated[$key] = ($mod_strings[$str] != '')?$mod_strings[$str]:(($app_strings[$str] != '')?$app_strings[$str]:$str);
	}

	$log->debug("Leaving function vtws_gettranslation");
	return $translated;
}

?>