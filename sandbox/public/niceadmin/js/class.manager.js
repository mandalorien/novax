
// ************************************************************************************************************
// parseValue
// Parameters : _VALUE to replace "null" values by empty string, trim data

function parseValue(_VALUE) {
	return (_VALUE == null) ? '' : _VALUE.trim();
}

// ************************************************************************************************************
// showBoolean
// Parameters : _VALUE is the value which will be formatted

function showBoolean(_VALUE) {
	return parseFloat(_VALUE) == 0 ? 'Non' : 'Oui';
}

// ************************************************************************************************************
// showPrice
// Parameters : _VALUE is the value which will be formatted

function showPrice(_VALUE, _CURRENCY, _TAX, _QUANTITY) {


	if(typeof _CURRENCY == 'object') {
		_CURRENCY = ' €';
	}


	console.log(typeof _CURRENCY);
	if(typeof(_TAX) != 'undefined') {
		_VALUE *= (1 + _TAX / 100);
		_VALUE *= _QUANTITY;
	}
	
	return parseFloat(_VALUE).toFixed(2) + _CURRENCY;
}

// ************************************************************************************************************
// showYear
// Parameters : _VALUE is the value which will be formatted

function showYear(_VALUE) {
	if(_VALUE == null) {
		return '';
	}
	
	if(typeof(_VALUE) == 'undefined') {
		return '';
	}
	
	return showDate(_VALUE).substr(-4);
}

// ************************************************************************************************************
// showPeriod
// Parameters : _VALUE is the value which will be formatted

function showPeriod(_VALUE) {
	if(_VALUE == null) {
		return '';
	}
	
	if(typeof(_VALUE) == 'undefined') {
		return '';
	}
	
	return showDate(_VALUE).substr(-7);
}


// ************************************************************************************************************
// showDate
// Parameters : _VALUE to return a formatted date from yyyy-mm-dd to dd/mm/yyyy

function showDate(_VALUE, _SEPARATOR = '/') {
	let TEMPO;
	if(!_VALUE || (_VALUE == null)) {
		return '';
	}
    
	console.log(typeof _SEPARATOR);
	if(typeof _SEPARATOR == 'object') {
		TEMPO = '/';
	}else{
		TEMPO = _SEPARATOR;
	}

	return _VALUE.toString().substr(0, 10).split('-').reverse().join(TEMPO);
}

function showHours(_VALUE,WITH_SEC = true) {
	if(!_VALUE || (_VALUE == null)) {
		return '';
	}
    
	let _SUB = 0;
	if(!WITH_SEC) {
		_SUB = 3;
	}
	return _VALUE.toString().substr(11, _VALUE.toString().length - _SUB);
}

function showDateHours(_VALUE,WITH_SEC = true) {
	if(!_VALUE || (_VALUE == null)) {
		return '';
	}
    
	let _SUB = 0;
	if(!WITH_SEC) {
		_SUB = 3;
	}
	return showDate(_VALUE) + ' ' +_VALUE.toString().substr(11, _VALUE.toString().length - _SUB);
}


//convert time second to S:M:H....
function showTime(time = 0) {
	let _second = time;
	let _minut = 0;
	let _hour = 0;
	let _HTML = '';

	if (_second < 0) {
		_HTML = "-";
    } else {
		if (_second > 59) {
			_minut = Math.floor(_second/60);
			_second = _second - _minut * 60;
		}
		
		if (_minut > 59) {
			_hour = Math.floor(_minut / 60);
			_minut = _minut - _hour * 60;
		}
		
		if (_second < 10) {
			_second = "0" + _second;
		}

		if (_minut < 10) {
			_minut = "0" + _minut;
		}

		if (_hour < 10) {
			_hour = "0" + _hour;
		}
		_HTML = _hour + ":" + _minut + ":" + _second + "";
    }
	return _HTML;
}

// ************************************************************************************************************
// showSeparatedString
// Parameters : _VALUE to return a formatted string to a separated value for lisibility

function showSeparatedString(_VALUE, _LENGTH = 4, _SEPARATOR = '-') {
	if(!_VALUE || (_VALUE == null)) {
		return '';
	}
	
	let _REGEX = new RegExp('\\w{' + _LENGTH + '}(?=.)', 'g');
    
	return _VALUE.toString().replace(_REGEX, '$&' + _SEPARATOR);
}

// ************************************************************************************************************
// showNumber
// Parameters : _VALUE is the value which will be formatted

function showNumber(_VALUE) {
	return parseFloat(_VALUE).toFixed(2);
}


// ************************************************************************************************************

function stringToBoolean(string){
    switch(string.toLowerCase().trim()){
        case "true": case "yes": case "1": return true;
        case "false": case "no": case "0": case null: return false;
        default: return Boolean(string);
    }
}

// ************************************************************************************************************
// parseBool
// Parameters : _VALUE to check if value is a Boolean

function parseBool(_VALUE) {
	return ((typeof(_VALUE) === 'string' && (_VALUE.toLowerCase() === 'true' || _VALUE.toLowerCase() === 'yes')) || _VALUE === 1);
}


// ************************************************************************************************************
// showPhoneNumber
// Parameters : _VALUE to return a formatted phone number

function showPhoneNumber(_VALUE) {
	if(!_VALUE) {
		return '';
	}
	
    _VALUE = _VALUE.replace(/[^\d]/g, '');
	
    if(_VALUE.length == 10) {
      _VALUE = _VALUE.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, "$1.$2.$3.$4.$5");
    }
    
	return _VALUE;
}

// ************************************************************************************************************
// showLink
// Parameters : _VALUE to return a formatted link with _PARSER attribute

function showLink(_PARSER, _VALUE) {
	if(!_VALUE || (_VALUE == null)) {
		return '';
	}
	
	if(!_PARSER || (_PARSER == null)) {
		return '';
	}
    
	return '<a href="%s:%s">%s</a>'.format(_PARSER, _VALUE, _VALUE);
}

// ************************************************************************************************************
// showMail
// Parameters : _VALUE to return a formatted link with mailto attribute

function showMail(_VALUE) {
	if(!_VALUE || (_VALUE == null)) {
		return '';
	}
    
	return showLink('mailto', _VALUE);
}

// ************************************************************************************************************
// showPhone
// Parameters : _VALUE to return a formatted link with tel attribute

function showPhone(_VALUE) {
	if(!_VALUE || (_VALUE == null)) {
		return '';
	}
    
	return showLink('tel', _VALUE);
}

// ************************************************************************************************************
// ************************************************************************************************************

function getDateOfISOWeek(w, y) {

	console.log(w);
	console.log(y);
    var simple = new Date(y, 0, 1 + (w - 1) * 7);
    var dow = simple.getDay();
	console.log(dow);
	console.log(simple);
    var ISOweekStart = simple;
    if (dow <= 4)
        ISOweekStart.setDate(simple.getDate() - simple.getDay() + 1);
    else
        ISOweekStart.setDate(simple.getDate() + 8 - simple.getDay());
    return ISOweekStart;
}


// ************************************************************************************************************
// ************************************************************************************************************

function UrlExists(url)
{
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    return http.status;
}


// Decode en JS
function decodeHTMLEntities(text) {
  var textArea = document.createElement('textarea');
  textArea.innerHTML = text;
  return textArea.value;
}
