<?
PHP::requireCustom('Esperanto');

function magicConversion($string, $encoding) {
	if ($encoding == 'ESPERANTO') {
		$encoding = 'ISO-8859-1';
		$string = xASombreros($string);
	}
	$string = iconv($encoding, 'UTF-8', $string);
	$string = utf8ToUnicodeEntities($string);
	return $string;
}

function magicUnconversion($string, $encoding) {
	if ($encoding == 'ESPERANTO') {
		return sombrerosAx($string);
	} else {
  	/*
  	TODO: ver...
		$string = preg_replace_callback('/&#([0-9a-fx]+);/mi', 'replace_num_entity', $string);
		$string = iconv('UTF-8', $encoding, $string);
		*/
		return $string;
	}
}

/**
* Takes a string of utf-8 encoded characters and converts it to a string of unicode entities
* each unicode entitiy has the form &#nnnnn; n={0..9} and can be displayed by utf-8 supporting
* browsers.
* @param $source string encoded using utf-8 [STRING]
* @return string of unicode entities [STRING]
* @access public
*/
function utf8ToUnicodeEntities($source) {
	// array used to figure what number to decrement from character order value
	// according to number of characters used to map unicode to ascii by utf-8
	$decrement[4] = 240;
	$decrement[3] = 224;
	$decrement[2] = 192;
	$decrement[1] = 0;
	
	// the number of bits to shift each charNum by
	$shift[1][0] = 0;
	$shift[2][0] = 6;
	$shift[2][1] = 0;
	$shift[3][0] = 12;
	$shift[3][1] = 6;
	$shift[3][2] = 0;
	$shift[4][0] = 18;
	$shift[4][1] = 12;
	$shift[4][2] = 6;
	$shift[4][3] = 0;
	
	$pos = 0;
	$len = strlen ($source);
	$encodedString = '';
	while ($pos < $len) {
		$asciiPos = ord (substr ($source, $pos, 1));
		
		if (($asciiPos >= 240) && ($asciiPos <= 255)) {
			// 4 chars representing one unicode character
			$thisLetter = substr ($source, $pos, 4);
			$pos += 4;
		} else if (($asciiPos >= 224) && ($asciiPos <= 239)) {
			// 3 chars representing one unicode character
			$thisLetter = substr ($source, $pos, 3);
			$pos += 3;
		} else if (($asciiPos >= 192) && ($asciiPos <= 223)) {
			// 2 chars representing one unicode character
			$thisLetter = substr ($source, $pos, 2);
			$pos += 2;
		} else {
			// 1 char (lower ascii)
			$thisLetter = substr ($source, $pos, 1);
			$pos += 1;
		}
		
		// process the string representing the letter to a unicode entity
		$thisLen = strlen ($thisLetter);
		$thisPos = 0;
		$decimalCode = 0;
		while ($thisPos < $thisLen) {
			$thisCharOrd = ord (substr ($thisLetter, $thisPos, 1));
			if ($thisPos == 0) {
				$charNum = intval ($thisCharOrd - $decrement[$thisLen]);
				$decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
			}
			else {
				$charNum = intval ($thisCharOrd - 128);
				$decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
			}
			
			$thisPos++;
		}
		
		if ($thisLen == 1) {
			$num = str_pad($decimalCode, 3, "0", STR_PAD_LEFT);
		} else {
			$num = str_pad($decimalCode, 5, "0", STR_PAD_LEFT);
		}
		if ($num < 256) {
			$encodedLetter = chr($num);
		} else {
			$encodedLetter = "&#". $num . ';';
		}

		$encodedString .= $encodedLetter;
	}
	
	return $encodedString;
}

function replace_num_entity($ord)
   {
       $ord = $ord[1];
       $match = 0;
       if (preg_match('/^x([0-9a-f]+)$/i', $ord, $match))
       {
           $ord = hexdec($match[1]);
       }
       else
       {
           $ord = intval($ord);
       }
      
       $no_bytes = 0;
       $byte = array();

       if ($ord < 128)
       {
           return chr($ord);
       }
       elseif ($ord < 2048)
       {
           $no_bytes = 2;
       }
       elseif ($ord < 65536)
       {
           $no_bytes = 3;
       }
       elseif ($ord < 1114112)
       {
           $no_bytes = 4;
       }
       else
       {
           return;
       }

       switch($no_bytes)
       {
           case 2:
           {
               $prefix = array(31, 192);
               break;
           }
           case 3:
           {
               $prefix = array(15, 224);
               break;
           }
           case 4:
           {
               $prefix = array(7, 240);
           }
       }

       for ($i = 0; $i < $no_bytes; $i++)
       {
           $byte[$no_bytes - $i - 1] = (($ord & (63 * pow(2, 6 * $i))) / pow(2, 6 * $i)) & 63 | 128;
       }

       $byte[0] = ($byte[0] & $prefix[0]) | $prefix[1];

       $ret = '';
       for ($i = 0; $i < $no_bytes; $i++)
       {
           $ret .= chr($byte[$i]);
       }

       return $ret;
   }
?>
