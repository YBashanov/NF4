<?php
$in = "";

function menuPersonal ($g_page, $name, $href = "", $url = ""){
	global $base_url;

	if ($href !== false) {
		$ret = "<a href='{$base_url}personal/?p={$href}{$url}'><div class='element";
		if ($g_page == $href) $ret .= " active";
		$ret .= "'>{$name}</div></a>";
	}
	else {
		$ret = "<a target='_blank' href='{$base_url}'><div class='element'>{$name}</div></a>";
	}
	
	return $ret;
}
