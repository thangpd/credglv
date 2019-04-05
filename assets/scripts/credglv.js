

;(function($){
    function Cred() {
        this.config  = credglvConfig;
        this.components = {};
        if (typeof (this.config) === 'undefined') {
            throw new Error('No config for Cred GLV found');
        }
    }

    /**
     * Log debug information to browser console
     * @param e
     */
    Cred.prototype.log = function (e) {
        if (this.config.debug) {
            console.log(e);
        }
    };

    /**
     * Init function
     */
    Cred.prototype.init = function () {
        /**
         * Create new event
         * @type {Event}
         */
        var self = this;
        //Init
        $(document).ready(function(){
            for (var name in self.components) {
                var component = self.components[name];
                try {
                    if (typeof (component.run) === 'function') {
                        component.run();
                    }
                } catch (e) {
                    self.log(e);
                }

            }
            /**
             * Trigger Cred GLV ready event
             */

        });
    };

    /**
     * Register a global component
     * It will be executed after the DOM is ready
     * @param name
     * @param obj
     */
    Cred.prototype.registerComponent = function (name, obj) {
        this.components[name] = obj;
    };

    function Request(credglv) {
        this.type = 'get';
        this.url = '';
        this.headers = [];
        this.data = {};
        this.ajaxUrl = credglv.config.baseUrl  + '/wp-admin/admin-ajax.php';
        this.ajax = false;
        var reqObj = {
            dataType : 'JSON'
        };
        var self = this;
        var busy = false;
        this.send = function(callback){
            /*if (busy) return callback(false);
            busy = true;*/
            if (self.url === '') {
                self.url = self.ajaxUrl;
            }
            reqObj.url = self.url;
            reqObj.type = self.type;
            reqObj.data = self.data;
            reqObj.success = function(response) {
                callback(response);
                busy = false;
            };
            reqObj.error = function (e) {
                callback(false);
                busy = false;
                credglv.log(e);
            };
            reqObj.xhrFields = {
                withCredentials: true
            };
            self.ajax = $.ajax(reqObj);
        };
    }

    Request.prototype.get = function (url, callback) {
        this.url = url;
        this.type = 'GET';
        this.data  = {};
        this.send(callback);
    };

    Request.prototype.post = function (url, data, callback) {
        this.url = url;
        this.data = data;
        this.type = 'POST';
        this.send(callback);
    };

    /**
     * Abort current request
     */
    Request.prototype.abort = function () {
        if (this.ajax) {
            this.ajax.abort();
        }
    };

    function Resource(credglv) {

        this.load = function (type, id, url, position, callback) {
            var element;
            if (typeof (callback) == 'undefined') {
                callback = function() {
                    credglv.log(url, 'loaded');
                }
            }
            if ($('#' + id).length == 0) {
                switch (type) {
                    case 'script' :
                        element = $('<script/>', {
                            //onload : callback,
                            src : url,
                            id : id
                        });
                        break;
                    case 'style' :
                        element = $('<link>', {
                            rel : 'stylesheet',
                            href :  url,
                            type : 'text/css',
                            media : 'all',
                            //onload : callback,
                            id : id
                        });

                        break;


                }
                $('head').first().append(element);
            }

        }
    }

    Resource.prototype.loadScript = function (id, url, position, callback) {
        this.load('script', id, url, position, callback);
    };
    Resource.prototype.loadStyle = function (id, url, callback) {
        var self = this;
        this.load('style', id, url, false, callback);
    };

    function Debug(credglv) {

    }
    Debug.prototype.testSpeed = function (requestNumber) {
        if (typeof (requestNumber) === 'undefined') {
            requestNumber = 10;
        }
        var startTime = (new Date()).getTime();
        var totalTime = 0;
        console.log('Starting testing speed by : ' + requestNumber + ' request.');
        for (var i =0 ; i < requestNumber; i++) {
            var url = window.location.href;
            url = credglv.utils.updateQueryStringParameter(url, 'ts', (new Date()).getTime() + '' + Math.random());
            $.ajax({
                async : false,
                url : url,
                success : function(){
                    var currentTime = (new Date()).getTime();
                    var spentTime = currentTime - startTime;
                    startTime = currentTime;
                    totalTime += spentTime;
                    console.log('Request number ', i +1 , ' : ', spentTime, ' milisecond');
                },
                error : function(){

                }
            })
        }
        console.log('Testing completed. Total request : ', requestNumber, ' .Total time : ', totalTime, '. Avg time : ', totalTime / requestNumber);
    };

    function Utitils(){

    }
    Utitils.prototype.updateQueryStringParameterSearch = function(uri, key, value, push) {
        key = encodeURIComponent(key);
        value = encodeURIComponent(value);
        var re = new RegExp("([?&])" + key + "=(.*?)(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        var url = '';
        if (/\#$/.test(uri)) {
            uri = uri.substr(0, uri.length - 1);
        }
        uri = uri.replace('?&', '?');
        uri = uri.replace('&&', '&');
        
        if (uri.match(re)) {
            url = uri.replace(re, "$1" + key + "=" + value + "," + "$2" + "$3");
        }
        else {
            url = uri + separator + key + "=" + value;
        }

        if (push) {
            history.pushState({}, '', url);
        } else {
            return url;
        }

    };
    Utitils.prototype.updateQueryStringParameter = function(uri, key, value, push) {
        key = encodeURIComponent(key);
        value = encodeURIComponent(value);
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        var url = '';
        if (/\#$/.test(uri)) {
            uri = uri.substr(0, uri.length - 1);
        }
        uri = uri.replace('?&', '?');
        uri = uri.replace('&&', '&');
        
        if (uri.match(re)) {
            url = uri.replace(re, '$1' + key + "=" + value + '$2');
        }
        else {
            url = uri + separator + key + "=" + value;
        }

        if (push) {
            history.pushState({}, '', url);
        } else {
            return url;
        }

    };
    Utitils.prototype.removeQueryStringParameter = function(uri, key, value, push) {
        key = encodeURIComponent(key);
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        if (value) {
            value = encodeURIComponent(value);
            re = new RegExp("([?&])" + key + "=" + value + "(&|$)", "i");
        }
        var url = uri;

        if (uri.match(re)) {
            url = uri.replace(re, '$1' + '$2');
            if (/(\?|&)$/.test(url)) {
                url = url.substr(0, url.length -1);
            }
        }
        url = url.replace('?&', '?');
        url = url.replace('&&', '&');
        if (push) {
            history.pushState({}, '', url);
        } else {
            return url;
        }

    };

    Utitils.prototype.removeQueryStringParameterSearch = function(uri, key, value, push) {

        key = encodeURIComponent(key);
        value = encodeURIComponent(value);
        
        var url = uri;

        var re = new RegExp("([?&])" + key + "=(.*?)(&|$)", "i");
        var ids = [];
        var matches = url.match(re);
        if (matches) {
            ids = matches[2].split(',');
            $.each(ids, function(i, vl){    
                if(vl == value){
                    ids.splice(i, 1)
                }
            })
            ids = ids.join(',');
        }
        if(ids){
            url = url.replace(re, "$1" + key + "=" + ids + "$3");
        }else{
            url = url.replace(re, "$1");
        }

        if (push) {
            history.pushState({}, '', url);
        } else {
            return url;
        }

    };

    window.credglv = new Cred();
    credglv.request = new Request(credglv);
    credglv.resource =  new Resource(credglv);
    credglv.utils = new Utitils(credglv);
    credglv.debug = new Debug(credglv);
    credglv.init();
})(jQuery);
