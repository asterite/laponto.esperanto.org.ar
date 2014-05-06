// Determins if a date is valid
function _JSValidator_date_isValidDate (day, month, year) {
  month = month - 1;
  var d = new Date(year,month,day);
  var y = d.getYear() < 1000 ? d.getYear() + 1900 : d.getYear();
  return (y == year && month == d.getMonth() && day == d.getDate());
}