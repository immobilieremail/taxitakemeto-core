<?php

function detectBrowserLanguage()
{
	$all 		= explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$prefered  	= substr($all[0], 0, 2);

	return $prefered;
}
