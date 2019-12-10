<?php

use App\Extensions\SwissNumber;

function detectBrowserLanguage()
{
	$all 		= explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$prefered  	= substr($all[0], 0, 2);

	return $prefered;
}

function getSwissNumberFromUrl(String $url): String
{
	if (!preg_match('#[^/]+$#', $url, $matches)) {
		return "";
	} else {
		return $matches[0];
	}
}

/**
 * Generate swiss number from class
 *
 * @return String generated Swiss Number
 */
function swissNumber(): String
{
    $swiss_number = new SwissNumber;
    return $swiss_number();
}

/**
 * Create intersection of two arrays
 *
 * @param array $allowed_fields
 * @param array $fields
 *
 * @return array intersection
 */
function intersectFields(array $allowed_fields, array $fields): array
{
    $new_data = array_intersect_key($fields, array_flip($allowed_fields));
	return $new_data;
}
