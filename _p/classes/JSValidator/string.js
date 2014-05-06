// Determines if a string is empty
function _JSValidator_string_isEmpty(x) {
	x = new String(x);
	for(i=0; i<x.length; i++) {
		var c = x.charAt(i);
		if (c != ' ' && c != '\t' && c != '\n' && c != '\r')
			return false;
	}
	return true;
}