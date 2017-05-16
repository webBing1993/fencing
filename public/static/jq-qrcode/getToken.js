function getToken() {
	var date = new Date(),
		year = date.getFullYear(),
		month = (date.getMonth() + 1).toString(),
		day = date.getDate().toString();
	month = checkDate(month);
	day = checkDate(day);
	var _date = year + '/' + month + '/' + day;
	console.log(_date);
	var model, controller, action;
	var token = 'ct!zi&uhg9&b$*nw4m9^a51e%ps^*m7h';
	var apiToken = $.md5(_date + token);
	return apiToken;
}

function checkDate(date) {
	if(date.length == 1) {
		date = '0' + date;
	}
	return date;
}