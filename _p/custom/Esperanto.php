<?
function sombrerosAx($s) {
	$s = str_replace('&#264;', 'Cx', $s);
	$s = str_replace('&#284;', 'Gx', $s);
	$s = str_replace('&#292;', 'Hx', $s);
	$s = str_replace('&#308;', 'Jx', $s);
	$s = str_replace('&#348;', 'Sx', $s);
	$s = str_replace('&#364;', 'Ux', $s);
	$s = str_replace('&#265;', 'cx', $s);
	$s = str_replace('&#285;', 'gx', $s);
	$s = str_replace('&#293;', 'hx', $s);
	$s = str_replace('&#309;', 'jx', $s);
	$s = str_replace('&#349;', 'sx', $s);
	$s = str_replace('&#365;', 'ux', $s);
	return $s;
}

function xASombreros($s) {
	$s = str_replace('Cx', '&#264;', $s);
	$s = str_replace('Gx', '&#284;', $s);
	$s = str_replace('Hx', '&#292;', $s);
	$s = str_replace('Jx', '&#308;', $s);
	$s = str_replace('Sx', '&#348;', $s);
	$s = str_replace('Ux', '&#364;', $s);
	$s = str_replace('cx', '&#265;', $s);
	$s = str_replace('gx', '&#285;', $s);
	$s = str_replace('hx', '&#293;', $s);
	$s = str_replace('jx', '&#309;', $s);
	$s = str_replace('sx', '&#349;', $s);
	$s = str_replace('ux', '&#365;', $s);
	return $s;
}
?>