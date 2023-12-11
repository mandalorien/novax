//https://www.w3.org/TR/IndexedDB/
class Database {

    _connector = null;
    _db = null;
    _db_object = null;
    _is_mobile = false;

    _database = null;
    _table = null;
    _fields = {};
    _fields_autorized = {};
    _DATA = {};
    _CONDITIONS = {};

    constructor(is_mobile) {
        this._is_mobile = is_mobile;
    }

    init(_method = 'default') {
        if (!('indexedDB' in window)) {
            console.log("This browser doesn't support IndexedDB");
            return;
        }else{
            if(this._is_mobile) {
                this.initMobile();
                return;
            }

            const _table = this._table;
            const _fields = this._fields;

            var DATA = this._DATA;
            var CONDITIONS = this._CONDITIONS;
            var _fields_autorized = this._fields_autorized;
            var _db_object = this._db_object;

            //connect !!!!
            this._connector = indexedDB.open(this._database, 3);
            this._connector.onupgradeneeded = function() {

                const _db = this.result;

                let _ID = _table + "_id";
                if(!_db.objectStoreNames.contains(_table)) {
                    _db_object =  _db.createObjectStore(_table, {keyPath:_ID,autoIncrement:true,unique: true});

                    for (const [key, value] of Object.entries(_fields)) {
                        let _F = key;
                        console.log(`${key}: ${value}`);
                        _db_object.createIndex(_F, _F ,{unique: value.unique});
                        _fields_autorized[key] = true;
                    }
                }
            };

            this._connector.onsuccess  = function() {

                var tx;
                var store;
                var index;
                var request;
                const _db = this.result;
                switch(_method) {
                    case '_set':
                        console.log('_set onsuccess');
                        tx = _db.transaction(_table, "readwrite");
                        store = tx.objectStore(_table);

                        if(DATA[_table].length > 0) {

                            $(DATA[_table]).each(function() {

                                let _DATA = {};

                                for (const [field, value] of Object.entries(this)) {
                                    if(Object.hasOwn(_fields, field)) {
                                        _DATA[field] = value;
                                    }
                                }

                                if(Object.entries(_DATA).length == (Object.entries(_fields).length)) {
                                    store.put(_DATA);
                                }
                            });
                        }

                        tx.oncomplete = function(ev) {
                            console.log("add transaction oncomplete",ev);
                        };

                        tx.onerror = function(ev) {
                            console.log("add transaction onerror",ev);
                        };
                    break;
                    case '_get':
                        console.log('_get onsuccess');
                        tx = _db.transaction(_table, "readonly");
                        store = tx.objectStore(_table);

                        if((typeof(window['result'+ _table]) == 'undefined')) {
                            console.log('Need Method result'+ _table);
                            return;
                        }

                        if(CONDITIONS.length > 1) {
                            window['result'+ _table](null);
                        }else{
                            if(CONDITIONS.length == 1) {
                                if(Object.hasOwn(_fields, CONDITIONS[0].field)) {
                                    index = store.index(CONDITIONS[0].field);
                                    request = index.get(CONDITIONS[0].value);

                                    request.onsuccess = function() {
                                        const matching = request.result;
                                        if (matching !== undefined) {
                                            // A match was found.
                                            window['result'+ _table](matching);
                                        } else {
                                            // No match was found.
                                            window['result'+ _table](null);
                                        }
                                    };
                                }else{
                                    window['result'+ _table](null);
                                }
                            }else{
                                window['result'+ _table](null);
                            }
                        }
                    break;
                    case '_gets':
                        console.log('_gets onsuccess');
                        tx = _db.transaction(_table, "readonly");
                        store = tx.objectStore(_table);
                        var _results = [];
                        if((typeof(window['results'+ _table]) == 'undefined')) {
                            console.log('Need Method results'+ _table);
                            return;
                        }

                        if(CONDITIONS.length > 1) {
                            window['results'+ _table](null);
                        }else{
                            if(CONDITIONS.length == 1) {
                                if(Object.hasOwn(_fields, CONDITIONS[0].field)) {
                                    index = store.index(CONDITIONS[0].field);
                                    request = index.openCursor(IDBKeyRange.only(CONDITIONS[0].value));

                                    request.onsuccess = function() {
                                        const cursor = request.result;
                                        if (cursor) {
                                            // Called for each matching record.
                                            _results.push(cursor.value);
                                            cursor.continue();
                                        }else{
                                            if(_results.length > 0) {
                                                window['results'+ _table](_results);
                                            }else{
                                                window['results'+ _table](null);
                                            }
                                        }
                                    };
                                }else{
                                    window['results'+ _table](null);
                                }
                            }else{
                                window['results'+ _table](null);
                            }
                        }
                    break;
                    case 'default':
                    default:
                        console.log('default');
                    break;
                }

                this.result.close();
            };

            this._connector.onerror = function() {
                console.log("init onerror");
                this.result.close();
            };
        }  
    }

    initMobile() {
        if(localStorage.getItem(this._database +'.'+ this._table) == null) {
            //init data
            let _DATA = [{}];
            localStorage.setItem(this._database +'.'+ this._table,JSON.stringify(_DATA));
        }
        console.log("initMobile",this._fields);
    }

    _set(DATA) {
        this._DATA = DATA;
        if(this._is_mobile) {
            this._setMobile(DATA);
            return;
        }

        this.init('_set');
    }

    _setMobile(DATA) {

        let _RESULT = JSON.parse(localStorage.getItem(this._database +'.'+ this._table));
        const _table = this._table;
        const _fields = this._fields;
        const _fields_autorized = this._fields_autorized;
        
        let _TEMPO = [];
        if(DATA[this._table].length > 0) {
            
            $(DATA[this._table]).each(function() {

                let _DATA = {};

                for (const [field, value] of Object.entries(this)) {
                    if(Object.hasOwn(_fields, field)) {
                        _DATA[field] = value;
                    }
                }

                _TEMPO.push(_DATA);
            });

            localStorage.setItem(this._database +'.'+ this._table,JSON.stringify(_TEMPO));
        }
    }

    _get(CONDITIONS) {
        this._CONDITIONS = CONDITIONS;

        if(this._is_mobile) {
            this._setMobile(DATA);
            return;
        }

        this.init('_get');
    }

    _gets(CONDITIONS) {
        this._CONDITIONS = CONDITIONS;

        if(this._is_mobile) {
            this._setMobile(DATA);
            return;
        }

        this.init('_gets');
    }
}

function resulttest(RESULT) {
    console.log("resultstest");
    console.log(RESULT);
}

/********* METHOD EXEMPLE ***********/
if(!navigator.onLine) {

    let _Databases = {};
    _Databases['legacies']= {};
    _Databases['legacies']['test'] = new Database(_MOBILE);
    _Databases['legacies']['test']._database = 'legacies';
    _Databases['legacies']['test']._table = 'test';
    _Databases['legacies']['test']._fields['name'] = {};
    _Databases['legacies']['test']._fields['name']['unique'] = false;
    _Databases['legacies']['test']._fields['flags']= {};
    _Databases['legacies']['test']._fields['flags']['unique'] = false;

    _Databases['legacies']['test'].init();

    let _DATA = {
        'test': [{
            'name':'first',
            'flags':0,
        }]
    };

    _Databases['legacies']['test']._set(_DATA);
    _Databases['legacies']['test']._get([{'field':'name','value':'first'}]);
}