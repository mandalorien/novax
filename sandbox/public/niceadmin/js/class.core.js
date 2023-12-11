let Timers = {};
let Debug = false;

Timers.Search = null;

String.prototype.format = function() {
	return [...arguments].reduce((p,c) => p.replace(/%s/,c), this);
};

$(document).ready(function() {
	//secure !!!!
	$('.modal').attr('data-keyboard','false');
	$('.modal').attr('data-backdrop','static');

	$('*[class$="_search"]').on('keypress keyup', function(_E) {
		let _ELEMENT = $(this).find('.input-group-text, .input-group-append .btn:not([data-toggle="collapse"])');
		
		clearTimeout(Timers.Search);
		
		if(_E.which == 13) {
			_ELEMENT.trigger('click');
			_E.preventDefault();
		}
		else {
			Timers.Search = setTimeout(function() {
				_ELEMENT.trigger('click');
			}, 300);
		}
	});
	
	$('input').on('keypress keyup', function(_E) {
		if($(this).is('.is-valid, .is-invalid')) {
			$(this).removeClass('is-valid is-invalid');
		}
	});
	
	$('*[id^="pagination"]').on('click', '.page-link', function() {
		let _METHOD = $(this).parents('ul').attr('data-method');
		let _PAGE = $(this).attr('data-page');
		
		console.log('Call ' + _METHOD + ' with parameter ' + _PAGE)
		window[_METHOD](_PAGE);
	});
	
	$('body').on('click', 'button[data-method!=""], div[data-method!=""], a[data-method!=""], i[data-method!=""]', function(_E) {
		let _METHOD = $(this).attr('data-method');
		let _PARAMETER = $(this).attr('data-parameter');
		let _ELEMENT = $(this);
		
		if((typeof(_METHOD) != 'undefined') && (typeof(window[_METHOD]) != 'undefined')) {
			console.log('Call method ' + _METHOD);
			
			if(typeof(_PARAMETER) == 'undefined') {
				window[_METHOD](_ELEMENT);
			}
			else {
				if(typeof(_ELEMENT) == 'undefined') {
					window[_METHOD](_PARAMETER);
				}
				else {
					window[_METHOD](_PARAMETER, _ELEMENT);
				}
			}
		}
		else if(typeof(_METHOD) != 'undefined') {
			console.error('Method ' + _METHOD + ' does not exist');
		}
	});
	
	$('body').on('keyup keydown keypress', '.number, input[data-type="number"]', function(event) {
		if(!allowOnlyNumber($(this).val(), event)) {
			event.stopImmediatePropagation();
			event.stopPropagation();
			return false;
		}
	});
	
	$(document).on('show.bs.modal', '.modal', function () {
		var zIndex = 1040 + (10 * $('.modal:visible').length);
		$(this).css('z-index', zIndex);
		
		setTimeout(function() {
			$('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
		}, 0);
	});
	
	$(document).on('hidden.bs.modal', '.modal', function() {
		$('.modal:visible').length && $(document.body).addClass('modal-open');
	});
	
	$('[data-toggle="tooltip"]').tooltip();
	
	$('[data-mask!=""]').each(function() {
		let _MASK = $(this).attr('data-mask');
		
		$(this).mask(_MASK);
	});
	
	$('table.sort').find('th[data-sort]').each(function() {
		let _ELEMENT = $(this);
		
		switch(_ELEMENT.attr('data-sort')) {
			case 'DESC':
				$('<i class="fas fa-sort-alpha-up"></i>').appendTo(_ELEMENT);
				break;
				
			case 'ASC':
				$('<i class="fas fa-sort-alpha-down"></i>').appendTo(_ELEMENT);
				break;
				
			default:
				$('<i class="fas fa-sort"></i>').appendTo(_ELEMENT);
		}
		
	});
	
	$('table.sort').on('click', 'th[data-sort]', function() {
		let _ELEMENT = $(this);
		
		switch(_ELEMENT.attr('data-sort')) {
			case 'DESC':
				$(this).attr('data-sort', '');
				$(this).find('i').removeClass('fa-sort fa-sort-alpha-down fa-sort-alpha-up').addClass('fa-sort');
				break;
				
			case 'ASC':
				$(this).attr('data-sort', 'DESC');
				$(this).find('i').removeClass('fa-sort fa-sort-alpha-down fa-sort-alpha-up').addClass('fa-sort-alpha-up');
				break;
				
			default:
				$(this).attr('data-sort', 'ASC');
				$(this).find('i').removeClass('fa-sort fa-sort-alpha-down fa-sort-alpha-up').addClass('fa-sort-alpha-down');
		}
		
		$('*[class$="_search"]:visible').find('.btn').click();
	});
});

// ************************************************************************************************************
// populateRow
// Howto : each th should have a data-field element which is the field on database, and can have a data-class to automatically add class to td.
// Parameters : _ARRAY is the #id of the array to populate / _DATA is the data of your ajax query (can be this) / _OPTIONS is a JSON object for options (like deletion) - not used anymore

function populateRow(_ARRAY, _DATA, _OPTIONS) {
	let _COLUMNS = $('#' + _ARRAY).find('thead th');
	let _HTML = '';
	
	if(typeof(_OPTIONS) == 'undefined') {
		_OPTIONS = {};
	}
	
	_HTML += '<tr';
		_HTML += ((_DATA == null) ? ' data-action="none"' : '');
		_HTML += ((typeof(_OPTIONS.tr_class) !== 'undefined') ? ' class="%s"'.format(_OPTIONS.tr_class) : '');
	_HTML += '>';
		
		if(_DATA != null) {
			$.each(_COLUMNS, function() {
				let _FORMAT = $(this).attr('data-format');
				let _PARAMETER = $(this).attr('data-parameter');
				let _COLUMN = $(this).attr('data-field');
				let _CLASS = $(this).attr('data-class');
				let _SHOW_DATA = $(this).attr('data-show-attr');
				let _ATTRIBUTE = true;
				
				let _VALUE = _DATA[_COLUMN];
				
				if((typeof(_SHOW_DATA) != 'undefined')) {
					_SHOW_DATA = stringToBoolean(_SHOW_DATA);
				}
				else {
					_SHOW_DATA = true;
				}
				
				if((typeof($(this).attr('data-show-attr')) != 'undefined')) {
					_ATTRIBUTE = parseBool($(this).attr('data-show-attr'));
				}
				
				if((typeof($(this).attr('data-flag')) != 'undefined')) {
					_VALUE = (Number(_VALUE) & $(this).attr('data-flag'));
				}
				
				if((typeof($(this).attr('data-object')) != 'undefined')) {
					_DATA[_COLUMN] = btoa(unescape(encodeURIComponent(JSON.stringify(_VALUE))))
				}

				if((typeof(_FORMAT) != 'undefined') && (typeof(window[_FORMAT]) != 'undefined')) {
					if(typeof(_PARAMETER) == 'undefined') {
						_VALUE = window[_FORMAT](_VALUE,_DATA);
					}
					else {
						_VALUE = window[_FORMAT](_VALUE, _PARAMETER);
					}
				}
				else if(typeof(_FORMAT) != 'undefined') {
					console.error('Format method ' + _FORMAT + ' does not exist');
				}
				
				if(_CLASS != 'ignore') {
					if(_SHOW_DATA) {
						_HTML += '<td class="' + _COLUMN + ((_CLASS.length > 0) ? ' ' + _CLASS : '') + '" data-' + _COLUMN + '="' + _DATA[_COLUMN] + '">' + _VALUE + '</td>';
					}
					else {
						_HTML += '<td class="' + _COLUMN + ((_CLASS.length > 0) ? ' ' + _CLASS : '') + '">' + _VALUE + '</td>';
					}
				}
			});
		}
		else {
			_HTML += '<td colspan="' + _COLUMNS.length + '" class="text-center">' + LANG['text_no_result'] + '</td>';
		}
		
	_HTML += '</tr>';
	
	return _HTML;
}

// ************************************************************************************************************
// setPagination
// Parameters : _METHOD is the method to call when we click on a page number / _CURRENT is the current page / _MAX is the maximum number for pages / _CONTAINER is the container where setting pagination

function setPagination(_METHOD, _CURRENT, _MAX, _CONTAINER) {
	_CURRENT = parseFloat(_CURRENT);
	_MAX = parseFloat(_MAX);
	_CONTAINER = (typeof(_CONTAINER) == 'undefined') ? '#pagination' : _CONTAINER;
	
	let _PREVIOUS = (_CURRENT == 1) ? 1 : (_CURRENT - 1);
	let _NEXT = (_CURRENT == _MAX) ? _MAX : (_CURRENT + 1);
	
	let _HTML = '';
	
	_HTML += '<div>';
		_HTML += '<ul data-method="' + _METHOD + '" class="pagination pagination-lg justify-content-center">';
			_HTML += '<li class="page-item' + ((_CURRENT == 1) ? ' disabled' : '') + '"><a class="page-link" data-page="' + _PREVIOUS + '" href="#">&laquo;</a></li>';
			
			if(_CURRENT > 5) {
				_HTML += '<li class="page-item"><a class="page-link" data-page="1" href="#">1</a></li>';
			}
			
			if(_CURRENT > 6) {
				_HTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
			}
			
			for(_INDEX = ((_CURRENT > 4) ? (_CURRENT - 5) : 0); (_INDEX < (_CURRENT + 4)) && (_INDEX < _MAX); _INDEX++) {
				_HTML += '<li class="page-item' + ((_CURRENT == (_INDEX + 1)) ? ' active' : '') + '"><a class="page-link" data-page="' + (_INDEX + 1) + '" href="#">' + (_INDEX + 1) + '</a></li>';
				
				if(((_CURRENT + 4) < _MAX) && ((_INDEX + 1) >= (_CURRENT + 4))) {
					_HTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
					_HTML += '<li class="page-item"><a class="page-link" data-page="' + _MAX + '" href="#">' + _MAX + '</a></li>';
				}
			}
			
			_HTML += '<li class="page-item' + ((_CURRENT == _MAX) ? ' disabled' : '') + '"><a class="page-link" data-page="' + _NEXT + '" href="#">&raquo;</a></li>';
		_HTML += '</ul>';
	_HTML += '</div>';
	
	$(_CONTAINER).empty();
	$(_HTML).appendTo(_CONTAINER);
	
	if(_MAX == 1) {
		$(_CONTAINER).addClass('d-none');
	}
	else {
		$(_CONTAINER).removeClass('d-none');
	}
}

// ************************************************************************************************************
// populateData
// Parameters : _FORM is the form to populate, _DATA is the field values

function populateData(_FORM, _DATA) {
	$.each(_DATA, function(_F, _V) {
		let _ELEMENT = $(_FORM).find('*[name^="' + _F + '"]');
		
		if(Debug) {
			console.log(_F, _V, _ELEMENT);
		}
		
		if(_ELEMENT.attr('type') == 'checkbox') {
			if(parseFloat(_V) == 0) {
				$(_FORM).find('*[name^="' + _F + '"]').prop('checked', false).removeAttr('checked');
			}
			else {
				$(_FORM).find('*[name^="' + _F + '"]').prop('checked', true).attr('checked', 'checked');
			}
		}
		else {
			// $(_FORM).find('*[name^="' + _F + '"]').val(_V);
			$(_FORM).find('*[name^="' + _F + '"]').val(decodeHTMLEntities(_V));
		}
		
		if(_F.substr(-5) == 'flags') {
			$(_FORM).find('*[name^="' + _F + '"]').prop('checked', false).removeAttr('checked');
			
			_V = parseFloat(_V);
			
			for(let _INDEX = 0; _INDEX < 30; _INDEX++) {
				let _FLAG = Math.pow(2, _INDEX);
				
				if(_FLAG & _V) {
					$(_FORM).find('*[name^="' + _F + '"][value="' + _FLAG + '"]').prop('checked', true).attr('checked', 'checked');
				}
			}
		}
	});
}

// ************************************************************************************************************
// resetForm
// Parameters : _FORM to reset inputs, selects and textarea

function resetForm(_FORM) {
	$(_FORM).find('input, select, textarea').each(function() {
		let _NAME = $(this).attr('name');
		let _TYPE = $(this).attr('type');
		let _VALUE = '';
		
		if(_TYPE == 'checkbox') {
			_VALUE = (typeof($(this).attr('data-default')) != 'undefined') ? parseFloat($(this).attr('data-default')) : false;
			_VALUE = (_VALUE == 1) ? true : false;
			
			$(this).prop('checked', _VALUE).attr('checked', _VALUE);
		}
		else {
			_VALUE = (typeof($(this).attr('data-default')) != 'undefined') ? $(this).attr('data-default') : '';
			$(this).val(_VALUE);
		}
	});
}

// ************************************************************************************************************
// getFormData
// Parameters : _FORM to get data

function getFormData(_FORM) {
	let _DATA = {};
	
	$(_FORM).find('input, select, textarea').each(function() {
		let _NAME = $(this).attr('name');
		let _ARRAY = false;
		let _INDEX = 0;
		
		if(typeof(_NAME) != 'undefined') {
			let _TYPE = $(this).attr('type');
			
			if(_NAME.indexOf('[') > -1) {
				_ARRAY = true;
				_NAME = _NAME.substr(0, _NAME.indexOf('['));
				
				do {
					_INDEX++;
				} while((typeof(_DATA[_NAME]) != 'undefined') && (typeof(_DATA[_NAME][_INDEX]) != 'undefined'));
			}
			
			if(_ARRAY && (typeof(_DATA[_NAME]) == 'undefined')) {
				_DATA[_NAME] = {};
			}
			
			if(_TYPE == 'checkbox') {
				if(_ARRAY) {
					if(_NAME.substr(-5) != 'flags') {
						if(typeof($(this).attr('value')) != 'undefined') {
							if($(this).prop('checked')) {
								_DATA[_NAME][_INDEX] = Number($(this).attr('value'));
							}
						}
						else {
							_DATA[_NAME][_INDEX] = Number($(this).attr('checked'));
						}
					}
					else {
						if($(this).prop('checked')) {
							_DATA[_NAME][_INDEX] = Number($(this).attr('value'));
						}
					}
				}
				else {
					if(typeof($(this).attr('value')) != 'undefined') {
						if($(this).prop('checked')) {
							_DATA[_NAME] = Number($(this).attr('value'));
						}
					}
					else {
						_DATA[_NAME] = Number($(this).attr('checked'));
					}
				}
			}
			else {
				if(_ARRAY) {
					_DATA[_NAME][_INDEX] = $(this).val();
				}
				else {
					_DATA[_NAME] = $(this).val();
				}
			}
		}
	});
	
	return _DATA;
}



function debugLines(DEBUGS = []) {

	if(_DEVEL) {
		if($('#devel').length > 0) {

			let REQUESTS_DEBUG = '';

			if(DEBUGS.length > 0) {
				$(DEBUGS).each(function(_INDEX ,_REQS) {
					_REQS.duration = ((parseFloat(_REQS.time_end) - parseFloat(_REQS.time_begin)) * 100000).toFixed(2);
					if($('#devel').find('.list-group-item[data-index="'+ _INDEX + '"][data-load="'+ _REQS.controller + '"]').length <= 0) {
						REQUESTS_DEBUG += '<li class="list-group-item alert-secondary" data-index="'+ _INDEX + '" data-load="'+ _REQS.controller + '">REQ N°' + _INDEX+ ' ['+ _REQS.controller + '][<span class="text-success">'+ _REQS.duration +' ms</span>]<div class="alert alert-secondary" role="alert"><code>'+ _REQS.execute.replace(/\n/g, "<br />") +'</code></div></li>';
					}
					else{
						$('#devel').find('.list-group-item[data-index="'+ _INDEX + '"][data-load="'+ _REQS.controller + '"]').html('REQ N°' + _INDEX+ ' ['+ _REQS.controller + '][<span class="text-success">'+ _REQS.duration +' ms</span>]<div class="alert alert-secondary" role="alert"><code>'+ _REQS.execute.replace(/\n/g, "<br />") +'</code></div>');
					}
					// console.log(_REQS);
				});
				$(REQUESTS_DEBUG).appendTo("#devel ul");
			}
		}
	}
}


function $_GET(param) {
	var vars = [];

	window.location.href.replace( location.hash, '' ).replace( 
		/([^/]+[A-Z-a-z])?/gi, // regexp
		function( m, key, value ) { // callback
			if(typeof key == 'string') {
				if(key != undefined) {
					vars.push(key);
				}
			}
		}
	);


	let tempo = {};
	$(vars).each(function(index,value) {
		switch(index) {
			case 0:
				tempo['protocol'] = value;
			break;
			case 1:
				tempo['domain'] = value;
			break;
			case 2:
				tempo['folder'] = value;
			break;
			case 3:
				tempo['controller'] = value;
			break;
			case 4:
				tempo['method'] = value;
			break;
			default:
				tempo['unknown'] = value;
		}
	});

	if (tempo[param] != undefined) {
		return tempo[param];
	}

	return tempo;
}
