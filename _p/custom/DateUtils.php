<?
PHP::requireCustom('Utils');

global $_months;
$_months = array(
	1 => 'Januaro', 2 => 'Februaro', 3 => 'Marto', 4 => 'Aprilo',
	5 => 'Majo', 6 => 'Junio', 7 => 'Julio', 8 => 'Auxgusto',
	9 => 'Septembro', 10 => 'Oktobro', 11 => 'Novembro', 12 => 'Decembro'
);

global $__days;
$__days = array(
	0 => 'Dimancxo', 1 => 'Lundo', 2 => 'Mardo',
	3 => 'Merkredo', 4 => 'Jxaudo',
	5 => 'Vendredo', 6 => 'Sabato'
);

function monthName($month_num) {
	global $_months;
	return $_months[$month_num];
}

function daysInMonth($month, $year) {
	return date('t', mktime(0, 0, 0, $month, 1, $year));
}

function dayName($day) {
	global $__days;
	return $__days[$day];
}

function dayNameOfDate($date) {
	$day = date('w', $date->getSeconds());
	return dayName($day);
}

function firstDayInMonth($month, $year) {
	return date('w', mktime(0, 0, 0, $month, 1, $year));
}

function dateNormalFormat($date) {
	$lalas = $date->hour == 1 ? 'la' : 'las';
	return 
	__(dayNameOfDate($date)) . ', ' .
	$date->day . ' de ' .
	__(monthName($date->month)) . ' de ' .
	$date->year . " a $lalas " . twoDigits($date->hour) . ':' . twoDigits($date->minute);
}
?>