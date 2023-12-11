class queryCore {

    _connector = {}; //for : websocket
    _method = 'ajax'; //another : websocket
    _async = true; //for : ajax
    _upload = false; //for : ajax
    _DATA = {};
    _url = null;

    constructor(is_mobile) {
        this._is_mobile = is_mobile;
    }

    _init(_method = 'ajax') {

        this._setMethod(_method);
        console.log("Prepare query",this._method);
        if(this._method == 'websocket') {
            this._initWebsocket();
        }
    }

    _initWebsocket() {
        //init webocket
        this._connector[this._method] = new WebSocket(_URL_WSS + '/wss');

        let method = this._method;
        let _connector = this._connector[this._method];

        _connector.addEventListener('open', function (event) {

            let _DATA = {};
            _DATA['load'] = 'server';
            _DATA['method'] = 'start';

            _connector.send(JSON.stringify(_DATA));
        });

        _connector.addEventListener('message', function (event) {

            let _RESULT = JSON.parse(event.data);
            if(_RESULT.DEBUG != undefined) {
                debugLines(_RESULT.DEBUG);
            }

            let _HTML = '';

            let _METHOD = '';

            _METHOD = 'callback' + _RESULT.Type;

            if(typeof(window[_METHOD]) !== 'undefined') {
                window[_METHOD](_RESULT);
            }
            else {
                console.log('Method ' + _METHOD + ' does not exists.');
            }
        });

        _connector.addEventListener('error', function (event) {
            if(event.target.readyState == 3) {
                console.log("Le serveur WEBSOCKET est désactivé, veuillez contacter un administrateur");
            }
        });

        _connector.addEventListener('close', (event) => {
            console.log('La connexion a été fermée avec succès.');
        });
    }

    _setMethod(m) {
        switch(m) {
            case 'websocket':
            case 'ajax':
                this._method = m;
            break;
            default:
                this._method = 'ajax';
            break;
        }
    }

    _processUrl(url) {
        let _U = url;
            _U+='?key=' + _KEY;

    }

    _query(_DATA,callback = null) {

        this._DATA = _DATA;

        switch(this._method) {
            case 'websocket':
                this._websocket();
            break;
            case 'ajax':
            default:
            this._ajax(callback);
        }
    }

    _websocket() {
        _connector[this._method].send(JSON.stringify(this._DATA));
    }


    _ajax(callback) {

        let _URL = this._processUrl(this._url);

        if(_upload) { //upload files 
            let _DATA = new FormData();
            // $(this)[0].files
            _DATA.append('documents[context]', context);
            
            _DATA.append('Action', 'PUT');

            for(_INDEX = 0; _INDEX < _DOCUMENTS.length; _INDEX++) {
                _DATA.append('file_' + _INDEX, _DOCUMENTS[_INDEX]);
            }

            $.ajax({
                xhr: function() {
                    let _XHR = new window.XMLHttpRequest();
                    let _ELEMENT = $('#Loading span');
                    
                    _XHR.upload.addEventListener('progress', function(_EVENT) {
                        if(_EVENT.lengthComputable) {
                            let Percentage = parseFloat((_EVENT.loaded / _EVENT.total) * 100).toFixed(2);
                            
                            _ELEMENT.text(Percentage);
                            
                            if(parseInt(Percentage) == 100) {
                                $("#Message_Loading").text(LANG['text_documents_sending']);
                            }
                        }
                    }, false);
                    
                    return _XHR;
                },
                method: 'POST',
                url: _URL,
                dataType: 'json',
                data: _DATA,
                cache: false,
                contentType: false,
                processData: false,
                timeout: 300000,
                
                success: function (_RESULT) {
                    if(callback != null) {
                        callback(_RESULT);
                        return true; 
                    }
                }
            });
        }else{
            $.post(_URL, _DATA, function(_RESULT) {
                
                // debugLines(_RESULT.DEBUG);

                if(callback != null) {
                    callback(_RESULT);
                    return true; 
                }else{
                    //TODO : !!!!!!!!!!!!!!!!!!!!!
                }
            });        
        }
    }   
}

let queryCoreWSS = new queryCore(_MOBILE);
queryCoreWSS._init('x');