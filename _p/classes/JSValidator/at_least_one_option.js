// Determines if at least one option is checked in the array of inputs supplied
function _JSValidator_at_least_one_option_is_checked(options) {
	var undefined;
	if (options.length == undefined) return options.checked;
	for (i=0; i < options.length; i++) {
		if (options[i].checked) return true;
	}
	return false;
}