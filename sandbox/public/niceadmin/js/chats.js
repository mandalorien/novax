var _CHANNELS_EXIST = $("#list_channels").length;
var _CHANNEL_OPEN = false;
var _webSocket  = new _webSocket(_Socket);


$(document).ready(function() {

	if(_DEVEL) {
		$("#actions").attr("style","z-index:999;align-items: flex-end;justify-content: flex-end;bottom:75px;");
	}else{
		$("#actions").attr("style","z-index:999;align-items: flex-end;justify-content: flex-end;bottom:0px;");
	}

	if(_CHANNELS_EXIST > 0) {

		//-------------------------------------------------------------------------------------
		//-------------------------------------  START SOCKET ---------------------------------
		//-------------------------------------------------------------------------------------

		let _DATA = {
			'load': 'channels',
			'type': 'loadViewChannels',
			'method':'get',
			'action':'init',
			'number_of_elements':999,
			'page':1,
			'token':TOKEN,
			'user_id':USER_ID
		};

		_DATA.open = false;
		_DATA.close = true;
		_webSocket.start(JSON.stringify(_DATA),'');

		loadUsersDisabled();
		//-------------------------------------------------------------------------------------
		$(document).on('click','.up',function() {
			$("#participants_disabled option:selected").each(function() {
				$(this).attr('data-remove','1');
				$(this.outerHTML).appendTo("#participants_enabled");
				$(this).remove();
			});
		});

		$(document).on('click','.down',function() {
			$("#participants_enabled option:selected").each(function() {
				if(parseFloat($(this).attr('data-remove')) != 0) {
					$(this.outerHTML).appendTo("#participants_disabled");
					$(this).remove();
				}
			});
		});

		document.getElementById('participants_enabled').ondblclick = function()	{
			if(parseFloat($(this.options[this.selectedIndex]).attr('data-remove')) != 0) {
				$(this.options[this.selectedIndex].outerHTML).appendTo("#participants_disabled");
				$(this.options[this.selectedIndex]).remove();
			}
		};

		document.getElementById('participants_disabled').ondblclick = function(){
			$(this.options[this.selectedIndex]).attr('data-remove','1');
			$(this.options[this.selectedIndex].outerHTML).appendTo("#participants_enabled");
			$(this.options[this.selectedIndex]).remove();
		};


	}
});


function resetF(_FORM) {
	resetForm(_FORM);
	loadUsersDisabled();
}

function loadUsersDisabled() {
	let _ARRAY = 'participants_disabled';
	
	if(typeof(_PAGE) == 'undefined') {
		_PAGE = 1;
	}
	
	let _DATA = {
		number_of_elements: 999999,
		page: _PAGE
	};
	
	$.post('/api/users/get?token=' + TOKEN, _DATA, function(_RESULT) {
		
		$("#device_enabled,#device_data_deleted").prop('checked',false).removeAttr('checked');
		
		let _HTML = '';
		// let _HTML = '<option value="*">Séléctionner un utilisateur</option>';
		
		if(!_RESULT.error) {
			$('#' + _ARRAY + '').empty();
			$('#participants_enabled').empty();
			
			if(_RESULT.users.length > 0) {
				// console.log(_RESULT.users);
				$(_RESULT.users).each(function(index,user) {
					if(user.user_id != USER_ID) {
						_HTML += '<option value="'+ user.user_id +'">'+ user.user_lastname +' '+ user.user_firstname +'</option>';
					}
				});
				$(_HTML).appendTo('#' + _ARRAY + '');
			}
		}
	});
}

function getChannel(_CHANNEL_ID) {

    let channel_id = _CHANNEL_ID;
    let _HTML = '';
    let _PARAMETERS = {
        "channel_id": channel_id
    }
    
    $('#channel_form').find('.message').removeClass('alert-danger alert-success').addClass('d-none');
    
    $.post('/api/channels/get?token=' + TOKEN, _PARAMETERS, function(_DATA) {
        
        $('#channel_form .nav a:first').tab('show');

        populateData('#channel_form', _DATA.channels);
        
        // ******************************************************************
		$('#participants_enabled').empty();
		if(_DATA.user_channels.length > 0) {
			$(_DATA.user_channels).each(function() {
				let _EM = $("#participants_disabled option[value='" + this.user_id + "']");
				console.log(_EM);
				if(_EM.length > 0) {

					_EM.attr('data-remove','0');
					$(_EM[0].outerHTML).appendTo("#participants_enabled");
					$(_EM[0]).remove();

				}else{
					let _OPTION = "<option data-remove='0' value='" + this.user_id + "'>" + this.channel_user + "</option>";
					$(_OPTION).appendTo("#participants_enabled");
				}
			});
		}
		// ------------------------------------------------------------------
        $('#channel_form').modal('show');
    });
}

function loadChannels(close = false) {
	//-------------------------------------------------------------------------------------
	let _DATA = {
		'load': 'channels',
		'type': 'loadViewChannels',
		'method':'get',
		'action':'loadChannels',
		'number_of_elements':999,
		'page':1,
		'token':TOKEN,
		'user_id':USER_ID
	};

	_DATA.close = close;
	_DATA.open = false;

	_webSocket.w_send(JSON.stringify(_DATA),'');
	//-------------------------------------------------------------------------------------
}

function loadViewChannels(_RESULT) {

	let _ARRAY = 'list_channels';
	let _HTML = '';
	
	if(!_RESULT.error) {
		$('#' + _ARRAY + '').empty();
		
		if(_RESULT.channels.length > 0) {
			
			$(_RESULT.channels).each(function(index,channel) {
				let channel_name = channel.channel_name;
				let initiales = channel_name.match(/\b\w/g).join('').toUpperCase();

				if($(".channel[data-channel_id='" + channel.channel_id + "']").length <= 0) {
					let D_NONE = 'd-none';
					let PADDING = '';
					if(parseFloat(channel.notifications.total) > 0) {
						D_NONE =''; 
						PADDING ='padding-right: 0px;'; 
					}

					_TOOLTIPS = 'data-toggle="tooltip" data-html="true" title="<b>' + channel_name + '</b> <br><em>' + channel.channel_content + '</em>"';
					_HTML += '<div '+ _TOOLTIPS + ' class="specific_channel d-inline-block" data-channel_id="' + channel.channel_id + '"><button data-channel_id="' + channel.channel_id + '" class="openDialog rounded-circle btn btn-lg mr-2" style="background-color:'+ channel.channel_color +';'+ PADDING +'" type="button" data-method="loadChannel" data-parameter="' + channel.channel_id + '">' + initiales +  '';
					_HTML += '<span class="' + D_NONE + ' rounded-circle badge badge-danger notifications" data-channel_id="' + channel.channel_id + '" style="top: -20px;">' + channel.notifications.total + '</span>';
					_HTML += '</button></div>';
				}else{ //it's open !!!!
					$(".card-header[data-channel_id='" + channel.channel_id + "']").attr('style','background-color:' + channel.channel_color + ';');
					$(".close[data-channel_id='" + channel.channel_id + "']").attr('style','background-color:' + channel.channel_color + ';');
					$(".title_channel[data-channel_id='" + channel.channel_id + "']").html(channel.informations.channel_name);
				}
			});
			$(_HTML).appendTo('#' + _ARRAY + '');

			 $('[data-toggle="tooltip"]').tooltip();
		}else{
			if($("#channel").length > 0) {
				$("#channel").html("");
			}
		}
	}
}

function loadChannel(_CHANNEL_ID,action = 'open') {


	if(action != 'edit') {
		action = 'open';
	}

	//-------------------------------------------------------------------------------------
	let _DATA = {
		'load': 'channels',
		'type': 'openViewChannel',
		'method':'get',
		'action':action,
		'channel_id':_CHANNEL_ID,
		'token':TOKEN,
		'user_id':USER_ID
	};

	_DATA.open = true;	

	_webSocket.w_send(JSON.stringify(_DATA),'');
	//-------------------------------------------------------------------------------------
}

function openViewChannel(_RESULT) {
	$(".openDialog[data-channel_id='" + _RESULT.channels.channel_id +"']").hide();
	$("#channel").append(modelDialog(_RESULT));

	//autot scroll
	if($(".messages[data-channel_id='" + _RESULT.channels.channel_id +"']")[0] != undefined) {
		$(".messages[data-channel_id='" + _RESULT.channels.channel_id +"']")[0].scrollTop = $(".messages[data-channel_id='" + _RESULT.channels.channel_id +"']")[0].scrollHeight;
	}
}

function reloadViewChannel(_RESULT) {

	if(!Array.isArray(_RESULT.channels)) {
		// console.log("RELOAD channel class",_RESULT.channels.channel_id);
		if($('.channel[data-channel_id="' + _RESULT.channels.channel_id + '"]').length > 0) { //Tchat Open

			$(".card-header[data-channel_id='" + _RESULT.channels.channel_id + "']").attr('style','background-color:' + _RESULT.channels.channel_color + ';');
			$(".close[data-channel_id='" + _RESULT.channels.channel_id + "']").attr('style','background-color:' + _RESULT.channels.channel_color + ';');
			$(".title_channel[data-channel_id='" + _RESULT.channels.channel_id + "']").html(_RESULT.informations.channel_name);

			$('.channel[data-channel_id="' + _RESULT.channels.channel_id + '"]').find("textarea[name='message_content']").html('');
			$('.channel[data-channel_id="' + _RESULT.channels.channel_id + '"]').find("textarea[name='message_content']").val('').change();
			$("#channel").append(modelDialog(_RESULT));
			//autot scroll
			if($(".messages[data-channel_id='" + _RESULT.channels.channel_id +"']")[0] != undefined) {
				$(".messages[data-channel_id='" + _RESULT.channels.channel_id +"']")[0].scrollTop = $(".messages[data-channel_id='" + _RESULT.channels.channel_id +"']")[0].scrollHeight;
			}
		}else{
			loadViewChannels(_RESULT);
		}
	}else{
		loadViewChannels(_RESULT);
	}
}
//--- create and udpdate
function saveChannel() {

    let _ACTION = 'add';

    var todayDate = new Date().toISOString().slice(0, 10);

	let _DATA = {
		'load': 'channels',
		'type': 'saveViewChannel',
		'method':_ACTION,
		'action':_ACTION,
		'channel':getFormData('#channel_form'),
		'token':TOKEN,
		'user_id':USER_ID
	};

    // ************************************************
    
    _DATA.channel.channel_flags = "0";
    
    // ************************************************


    if(parseFloat(_DATA.channel.channel_id) <= 0) {
        _ACTION = 'add';

		_DATA.channel.user_id = USER_ID;
        delete _DATA.channel.channel_id;

		let _Participants = [];
		let _Participants_name = {};
		$("#participants_enabled option").each(function() {
			let _Participant = {};
			_Participant.channel_user_id = 0;
			_Participant.channel_id = 0;
			_Participant.user_id = this.value;
			_Participant.channel_user_flags = 0;
			_Participants.push(_Participant);
			_Participants_name[this.value] = $(this).text();
		});

		_DATA.channel_user = _Participants;
		_DATA.channel_user_detail = _Participants_name;
    }
    else {
        _ACTION = 'edit';
		let _Participants = [];
		let _Participants_name = {};
		$("#participants_enabled option[data-remove='1']").each(function() {
			let _Participant = {};
			_Participant.channel_user_id = 0;
			_Participant.channel_id = _DATA.channel.channel_id;
			_Participant.user_id = this.value;
			_Participant.channel_user_flags = 0;
			_Participants.push(_Participant);
			_Participants_name[this.value] = $(this).text();
		});

		_DATA.channel_user = _Participants;
		_DATA.channel_user_detail = _Participants_name;
    }

	_DATA.method = _ACTION;
	_DATA.action = _ACTION;

	_DATA.open = false;
	_DATA.close = true;

	if(_ACTION == 'edit') {
		if($('.channel[data-channel_id="' + _DATA.channel.channel_id + '"]').length > 0) { //Tchat Open
			_DATA.open = true;
			_DATA.close = false;
		}
	}

	//-------------------------------------------------------------------------------------
	_webSocket.w_send(JSON.stringify(_DATA),'');
	//-------------------------------------------------------------------------------------
}

function saveViewChannel(_DATA) {

	let _ACTION = _DATA.Action;
	$('#channel_form').find('.message').removeClass('d-none alert-success alert-danger').find('span').html(LANG[_DATA.ErrorCode]);
	
	if(!_DATA.Error) {

		if(_ACTION == 'edit') {
			loadChannel(parseFloat(_DATA.channel_id),'edit');
		}else{
			loadChannels();
		}

		$('#channel_form').find('.message').addClass('alert-success');
		$('#channel_form').find('input[name="channel_id"]').val('0');
		resetForm("#channel_form");
		$("#channel_form").modal('hide');
	}
	else {
		$('#channel_form').find('.message').addClass('alert-danger');
		
		$.each(_DATA.Fields, function(_FIELD, _MESSAGE) {
			$('#' + _FIELD + '_feedback').text(LANG[_MESSAGE]);
			$('#channel_form').find('*[name="' + _FIELD + '"]').addClass('is-invalid');
		});
	}
}

function quitChannel(_CHANNEL_ID) {

	//-------------------------------------------------------------------------------------
	let _DATA = {
		'load': 'channels',
		'type': 'closeViewChannel',
		'method':'leave',
		'action':'leave',
		'channel_user' : {
			channel_id:_CHANNEL_ID,
			user_id:USER_ID,
			channel_flags:1
		},
		'token':TOKEN,
		'user_id':USER_ID
	};

	_webSocket.w_send(JSON.stringify(_DATA),'');
	closeViewChannel(_CHANNEL_ID);
}

function deleteChannel(_CHANNEL_ID) {

	//-------------------------------------------------------------------------------------
	let _DATA = {
		'load': 'channels',
		'type': 'confirmDeleteChannel',
		'method':'get',
		'action':'init',
		'number_of_elements':999,
		'page':1,
		'token':TOKEN,
		'user_id':USER_ID,
		'channel_id':_CHANNEL_ID
	};

	_DATA.open = true;
	_DATA.close = false;
	_webSocket.w_send(JSON.stringify(_DATA),'');
}

function confirmDeleteChannel(_RESULT) {
	if(parseFloat(_RESULT.channels.participants) > 2) {
		alert("vous ne pouvez pas supprimez cette conversation, il y a plus de 2 participants");
	}else{
		quitChannel(_RESULT.channels.channel_id);
	}
}

function addMessage(_CHANNEL_ID) {

	if($('.channel[data-channel_id="' + _CHANNEL_ID + '"]').find("textarea[name='message_content']").val().trim() == "") {
		alert("Attention votre message est vide !");
		return;
	}

	// console.log("addMessage");

	//-------------------------------------------------------------------------------------
	let _DATA = {
		'load': 'channels',
		'type': 'reloadViewChannel',
		'method':'get',
		'action':'send',
		'channel_id':_CHANNEL_ID,
		'message':$('.channel[data-channel_id="' + _CHANNEL_ID + '"]').find("textarea[name='message_content']").val(),
		'token':TOKEN,
		'user_id':USER_ID
	};


	_DATA.open = false;
	_DATA.close = true;

	if($('.channel[data-channel_id="' + _CHANNEL_ID + '"]').length > 0) { //Tchat Open
		_DATA.open = true;
		_DATA.close = false;
	}

	_webSocket.w_send(JSON.stringify(_DATA),'');
	//-------------------------------------------------------------------------------------

}

function closeViewChannel(_RESULT) {

	let _channel_id = (typeof _RESULT == 'object' ? _RESULT.channel_id : _RESULT);

	$(".channel[data-channel_id='" + _channel_id +"']").remove();
	$(".openDialog[data-channel_id='" + _channel_id +"']").show();
	loadChannels(true);
}

function modelDialog(_DATA) {

	// console.log("modelDialog");
	let _CHANNEL = _DATA.channels;
	let _INFORMATION = _DATA.informations;
	let _MESSAGES = _DATA.messages;

	let _STYLE = "width:330px;";
		_STYLE +="height:460px;";
		_STYLE +="margin-bottom: -5px;";
	
	if($(".channel[data-channel_id='" + _CHANNEL.channel_id + "']").length <= 0) {

		// console.log("Channel en cours de création");
		// console.log(_DATA);
		let _HTML  = '';
		_HTML += '<div class="channel card mx-2" data-channel_id="' + _CHANNEL.channel_id + '" style="'+ _STYLE +'">';
		// ----------------------------- BEGIN HEADER
		_HTML += '<div class="card-header" data-channel_id="' + _CHANNEL.channel_id + '" style="background-color:' + _CHANNEL.channel_color + ';">'; 
		_HTML += '<div class="row">';
		_HTML += '<div class="col-lg-2">';

		//TODO - a non-admin user can leave the channel  
		// if(_CHANNEL.user_id == USER_ID) {
			_HTML += '<div class="btn-group dropleft">';
			_HTML += '<button style="background-color:' + _CHANNEL.channel_color + ';" type="button" data-channel_id="' + _CHANNEL.channel_id + '" class="close" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cog" aria-hidden="true"></i></button>';

			_HTML += '<div class="dropdown-menu">';
				if(_CHANNEL.user_id == USER_ID) {
					_HTML += '<button class="dropdown-item" type="button" data-method="getChannel" data-parameter="' + _CHANNEL.channel_id + '">Paramètre du salon</button>';
					_HTML += '<button class="dropdown-item text-danger" data-method="deleteChannel" type="button" data-parameter="' + _CHANNEL.channel_id + '">Supprimer le salon</button>';
				}else{
    				_HTML += '<button class="dropdown-item text-danger" data-method="quitChannel" type="button" data-parameter="' + _CHANNEL.channel_id + '">Quitter le salon</button>';
				}
			_HTML += '</div>';

			_HTML += '</div>';
		// }
		_HTML += '</div>';

		_HTML += '<div class="title_channel col-lg-8 small" data-channel_id="' + _CHANNEL.channel_id + '">'; 
		_HTML += _INFORMATION.channel_name;
		_HTML += '</div>';

		_HTML += '<div data-channel_id="' + _CHANNEL.channel_id + '" class="col-lg-2">'; 
		_HTML += '<button type="button" class="close" data-dismiss="modal" aria-label="Close" data-method="closeViewChannel" data-parameter="' + _CHANNEL.channel_id + '"><span aria-hidden="true">×</span></button>';
		_HTML += '</div>';
		_HTML += '</div>';
		_HTML += '<div class="col">'; 

		_HTML += '</div>';

		_HTML += '<div class="col">'; 

		_HTML += '</div>';
		_HTML += '</div>';
		// ----------------------------- END HEADER
		// ----------------------------- BEGIN BODY
		_HTML += '<div class="card-body" style="background-color:#F5E6D8;">';
			_HTML += '<div data-channel_id="' + _CHANNEL.channel_id + '" class="row messages overflow-auto" style="max-height:290px;">';
				_HTML += modelMessages(_MESSAGES);
			_HTML += '</div>';
		_HTML += '</div>';
		// ----------------------------- END BODY

		_HTML += '<div class="card-footer" style="background-color:#F5E6D8;">';
			_HTML += '<div class="row">';
			_HTML += '<div class="input-group">';
					_HTML += '<textarea name="message_content" data-field="message_content" class="form-control" placeholder="Veuillez respecter la RGPD et ne pas tenir de propos diffamatoires." rows="1"></textarea>';
					_HTML += '<div class="input-group-append">';
						_HTML += '<button type="button" class="btn btn-success btn-lg" data-method="addMessage" data-parameter="' + _CHANNEL.channel_id + '"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>';
					_HTML += '</div>';
				_HTML += '</div>';
			_HTML += '</div>';
		_HTML += '</div>';
		_HTML += '</div>';
		return _HTML;
	}else{
		// console.log(_DATA);
		$(".card-header[data-channel_id='" + _CHANNEL.channel_id + "']").attr('style','background-color:' + _CHANNEL.channel_color + ';');
		$(".close[data-channel_id='" + _CHANNEL.channel_id + "']").attr('style','background-color:' + _CHANNEL.channel_color + ';');
		$(".title_channel[data-channel_id='" + _CHANNEL.channel_id + "']").html(_INFORMATION.channel_name);

		$(".messages[data-channel_id='" + _CHANNEL.channel_id + "']").html(modelMessages(_MESSAGES));

		let _DATA = {
			'load': 'channels',
			'type': 'loadViewChannels',
			'method':'get',
			'action':'init',
			'number_of_elements':999,
			'page':1,
			'token':TOKEN
		};

		_webSocket.start(JSON.stringify(_DATA),'');

	}
}

function modelMessages(_MESSAGES) {
	let _HTML = "";
	if(_MESSAGES.length <= 0) {
		_HTML += '<div class="col">'; 
		_HTML += 'Aucun messages actuellement.';
		_HTML += '</div>';
	}else{

		let _DATE = new Date('1970-01-01');

		let _RECEIPT = '';
		$(_MESSAGES).each(function(I_M,_Message) {
			
			let _DATE_C = new Date((_Message.message_date).toString().substr(0, 10));
			if(_DATE_C.getTime() > _DATE.getTime()) {
				_DATE = _DATE_C;

				_HTML += '<div class="rounded col-lg-4"></div>'; 
				_HTML += '<div class="rounded col-lg-4 mb-2" style="background-color:#fff;">'; 
				_HTML += '<div class="text-center"><small>' + showDate(_Message.message_date) + '</small></div>';
				_HTML += '</div>';
				_HTML += '<div class="rounded col-lg-3"></div>'; 

			}

			if(_RECEIPT != _Message.receipt_name) {
				_RECEIPT = _Message.receipt_name;
			}

			let _CLASS_MESSAGE  = '';

			switch(_Message.message_type) {
				case 'information':
					_CLASS_MESSAGE = 'text-center';
					_HTML += '<div class="rounded small col-lg-12 information">';
				break;
				case 'tchat':
					if(parseFloat(_Message.user_src_id) != USER_ID) {
						_CLASS_MESSAGE = 'text-left';
						_HTML += '<div class="rounded col-lg-10 alert alert-light">';
						_HTML += '<div class="text-danger small">' + _RECEIPT + '</div>';
					}else{
						_CLASS_MESSAGE = 'text-right';
						_HTML += '<div class="rounded col-lg-2"></div>'; 
						_HTML += '<div class="rounded col-lg-10 alert alert-success">'; 
					}
				break;
			}


			_HTML += '<div class="'+ _CLASS_MESSAGE +'">' + _Message.message_content + '</div>';

			switch(_Message.message_type) {
				case 'tchat':
					_HTML += '<div class="text-right"><small>' + showHours(_Message.message_date,false) + '</small></div>';
					if(parseFloat(_Message.user_src_id) != USER_ID) {
						_HTML += '</div>';
						_HTML += '<div class="rounded col-lg-2"></div>'; 
					}else{
						_HTML += '</div>';
					}
				break;
				case 'information':
				default:
					_HTML += '<div class="text-right"><hr></div>';
					_HTML += '</div>';
				break;
			}
		});
	}

	return _HTML;
}

function get_random_color() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.round(Math.random() * 15)];
    }
    return color;
}

function LightenDarkenColor(col,amt) { var usePound = false; if ( col[0] == "#" ) { col = col.slice(1); usePound = true; } var num = parseInt(col,16); var r = (num >> 16) + amt; if ( r > 255 ) r = 255; else if (r < 0) r = 0; var b = ((num >> 8) & 0x00FF) + amt; if ( b > 255 ) b = 255; else if (b < 0) b = 0; var g = (num & 0x0000FF) + amt; if ( g > 255 ) g = 255; else if ( g < 0 ) g = 0; return (usePound?"#":"") + (g | (b << 8) | (r << 16)).toString(16); }