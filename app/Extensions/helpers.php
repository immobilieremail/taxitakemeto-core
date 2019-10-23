<?php

use App\Extensions\SwissNumber;

function detectBrowserLanguage()
{
	$all 		= explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$prefered  	= substr($all[0], 0, 2);

	return $prefered;
}

function getSwissNumberFromUrl(String $url)
{
	if (!preg_match('#[^/]+$#', str_replace("/edit", "", $url), $matches)) {
		return null;
	} else {
		return $matches[0];
	}
}

/**
 * Generate swiss number from class
 * 
 * @return String generated Swiss Number
 */
function swiss_number(): String
{
    $swiss_number = new SwissNumber;
    return $swiss_number();
}
