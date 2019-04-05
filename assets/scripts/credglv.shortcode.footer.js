

;(function($, credglv){
    /**
     * Shortcode
     * @constructor
     */
    function Shortcodes() {
        this.shortcodes = {};
        this.ready = false;
        var self = this;
        this.run = function () {
            if (parseInt(credglv.config.debug) === 1) {
                if (credglv.config.lazyShortcode) {
                    self.lazyLoad()
                } else {
                    for (var id in self.shortcodes) {
                        /*var shortcode = self.shortcodes[id];
                        if (typeof (shortcode.run) === 'function' && (typeof (shortcode.executed) === 'undefined' || shortcode.executed == false)) {
                            shortcode.executed = true;
                            shortcode.run();
                            $(".credglv-shortcode-" + id).addClass("loaded");
                            shortcode.trigger('done', shortcode);
                        }*/
                        self.do(id);
                    }
                }
            }
            
            setTimeout(function () {
                $('.credglv-shortcode-block').addClass('loaded');
            }, 500);
            this.ready = true;
        }
    }

    /**
     *
     * @param name
     */
    Shortcodes.prototype.do = function (name) {
        var shortcodes = [];
        if (typeof (name) !== 'object') {
            shortcodes.push(name);
        } else {
            shortcodes =  name;
        }
        for ( var i in shortcodes) {
            name = shortcodes[i];
            var shortcode = this.getShortcodeById(name);
            if (shortcode && !shortcode.executed) {
                try {
                    shortcode.executed = true;
                    shortcode.run();
                    credglv.log("Running shortcode", name);
                    shortcode.trigger('done', shortcode);
                } catch (e) {
                    credglv.log(e);
                }
            } else {
                if (typeof (shortcode) === 'undefined') {
                    credglv.log('Shortcode not found', name);
                }
            }
            $(".credglv-shortcode-" + name).addClass("loaded");
        }
    };

    /**
     * Get shortcode by ID
     * @param id
     * @returns {*}
     */
    Shortcodes.prototype.getShortcodeById = function (id) {
        return this.shortcodes[id];
    };

    /**
     * Register a shortcode
     * override if shortcode id is already registered
     * @param id
     * @param obj
     */
    Shortcodes.prototype.register = function (id, obj) {
        obj.id = id;
        _.extend(obj, Backbone.Events);
        if (typeof (this.shortcodes[id]) !== 'undefined') {
            var shortcode = this.shortcodes[id];
            /* for (var key in obj) {
             shortcode[key] = obj[key];
             }*/
            _.extend(shortcode, obj);
            obj = shortcode;
        }
        for (var name in obj.binds) {
            obj.on(name, obj.binds[name]);
        }
        this.shortcodes[id] = obj;
        if (this.ready) {
            self.do(id);
        }
    };

    /**
     * Load short code
     * @param shortcode
     * @param callback
     */
    Shortcodes.prototype.lazyLoad = function () {
        var self = this;
        $('.shortcode-blocks').each(function (i, e) {
            var shortcode_id = $(e).data('id');
            var data = $(e).data();
            var id = $(e).attr('id');
            if (typeof (id) === 'undefined') {
                id = 'shortcode-undefined-' + shortcode_id + '-' + Math.random();
            }
            delete data['id'];
            if (id) {
                self.loadShortcodeContent(id, shortcode_id, data, function(){
                    credglv.log('Shortcode ' + id + ' is loaded');
                });
            }
        });
    };

    /**
     * Load shortcode content from backend via ajax
     * @param id
     * @param data
     * @param callback
     */
    Shortcodes.prototype.loadShortcodeContent = function (id, shortcode_id, data, callback){
        var self = this;
        credglv.request.post('', {
            action : 'get_shortcode_content',
            shortcode_id : shortcode_id,
            data : data
        }, function(result) {
            if (result) {
                if (result.html) {
                    var container = $('#' + id);
                    if (container) {
                        container.removeClass('shortcode-blocks').addClass('shortcode-blocks-loaded');
                        container.html(result.html);
                    }
                }
                /**
                 * Load difined resources
                 */
                if (result.resources) {
                    var total = result.resources.length;
                    var currentLoaded = 0;
                    function checkResourceReady(){
                        currentLoaded += 1;
                        if (currentLoaded === total) {
                            var shortcode = self.getShortcodeById(id);
                            if (shortcode) {
                                shortcode.run();
                                shortcode.trigger('done', shortcode);
                            }
                        }
                    }
                    for (var i in result.resources) {
                        var resource = result.resources[i];
                        if (resource.type === 'script') {
                            credglv.resource.loadScript(resource.id, resource.url, false, function(url){
                                credglv.log('Shortcode script loaded : ', url);
                                checkResourceReady();
                            });
                        } else {
                            checkResourceReady();
                            credglv.resource.loadStyle(resource.id, resource.url, false, function(url){
                                credglv.log('Shortcode style loaded : ', url);
                            });
                        }
                    }
                    if (typeof (callback === 'function')) {
                        callback();
                    }
                }
            }
        })
    };
    /**
     *
     * @type {Shortcodes}
     */
    var shortcode = new Shortcodes();
    credglv.registerComponent('shortcodes', shortcode);
    credglv.shortcodes = shortcode;
})(jQuery, credglv);