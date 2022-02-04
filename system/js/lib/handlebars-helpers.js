//getInitial
Handlebars.registerHelper('getInitial', function(name) {
	return String(name).charAt(0);
});

//getStageAgeLabelClass
Handlebars.registerHelper('getStageAgeLabelClass', function(age, limit) {
	if(age < limit)
	{
		return;
	}

	var diff = Math.abs(age - limit);

	if(diff < 5)
	{
		return 'warning';
	}

	if(diff > 4)
	{
		return 'important';
	}
});

//getFormatPhone
Handlebars.registerHelper('getFormatPhone', function(phone) {
	return '(' + phone.substr(0,3) + ') ' + phone.substr(3,3) + '-' + phone.substr(6);
});

//getDateFromTimestamp
Handlebars.registerHelper('getDateFromTimestamp', function(timestamp) {
	var date_obj = new Date(timestamp);

	return date_obj.format('mediumDate');
});

//getTimeFromTimestamp
Handlebars.registerHelper('getTimeFromTimestamp', function(timestamp) {
	var date_obj = new Date(timestamp);

	return date_obj.format('shortTime');
});

//getDateTimeFromTimestamp
Handlebars.registerHelper('getDateTimeFromTimestamp', function(timestamp) {
	var date_obj = new Date(timestamp);

	return date_obj.format('mediumDate') + ' ' + date_obj.format('shortTime');
});