/**
 * MuDoCo - A Multi Domain Cookie
 */

// the main mudoco queue
var _mdcq = _mdcq || [];

MuDoCo = function()
{
	this.options = {localCookieName: 'MDCL'};
	// used for handling simultaneous xss calls contexts
	this.xssPending = [];
	this.data = {};
};

MuDoCo.singleton = null;

MuDoCo.me = function() {
	if (this.singleton == null) {
		this.singleton = new MuDoCo();
	}
	return this.singleton;
};

MuDoCo.prototype.callbacks = {};

MuDoCo.prototype.callbacks.default = function(mode, vars, success, error)
{
	if (mode == 'run') {
		this.mdcXssAjax({
			vars: vars,
			success: success,
			error: error,
		});
	}
};

MuDoCo.prototype.callbacks.session = function(mode, params, success, error)
{
	this.callbacks.default.call(this, mode, params, success, error);
	if (mode == 'success') {
		for(var i in params.data) {
			this.data[i] = params.data[i];
		}	  
	}
};

MuDoCo.prototype.getMNonce = function() {
	return this.getCookie(this.options['localCookieName']);
};

MuDoCo.prototype.getCookie = function(key)
{
    var result;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? (result[1]) : null;
};

/**
 * Try to run the query callback.
 * @param cb callback
 * @param params object
 * @param success callback
 * @param error callback
 */
MuDoCo.prototype.run = function(cb, params, success, error) {
	var self = this;
	// against current nonce
	if (this.getMNonce()) {
		// if it fails this can be because nonce is too old
		cb.call(this, 'run', params, success, function() {
			// retry
			self.localBeacon({
				success: function() { cb.call(self, 'run', params, success, error); },
			});
		});
	}
	else {
		// get a new nonce
		this.localBeacon({
			success: function() { cb.call(self, 'run', params, success, error); },
		});
	}
};

MuDoCo.prototype.processQ = function()
{
	var self = this;
	if(_mdcq.length) {
		var item = _mdcq.shift();
		if (typeof item == 'function') {
			this.run(item, {}, function() { self.processQ(); });
		}
		else if (item.init) {
			// init options
			this.options[item.init] = item.value;
			this.processQ();
		}
		else if (item.plugin) {
			// declare some external query callback
			this.callbacks[item.plugin] = item.value;
			this.processQ();
		}
		else if (item.query) {
			// process a callback
			this.query(item.query, item.vars, function() { self.processQ(); });
		}
		else {
			this.processQ();
		}
	}
};

MuDoCo.prototype.query = function(q, vars, success, error) {
	var vars = vars || {};
	vars.q = q;	
	if (this.callbacks[q] == undefined) {
		q = 'default';
	}	
	this.run(this.callbacks[q], vars, success, error);
}

MuDoCo.prototype.beacon = function(opts){
    // Make sure we have a base object for opts
    opts = opts || {};
    // Setup defaults for options
    opts.url = opts.url || null;
    opts.vars = opts.vars || {};
    opts.error = opts.error || function(){};
    opts.success = opts.success || function(){};
 
    opts.vars.r = Math.random();

    // Split up vars object into an array
    var varsArray = [];
    for(var key in opts.vars){ varsArray.push(key + '=' + opts.vars[key]); }
    // Build query string
    var qString = varsArray.join('&');
 
    // Create a beacon if a url is provided
    if( opts.url )
    {
        // Create a brand NEW image object
        var beacon = new Image();
        // Attach the event handlers to the image object
        if( opts.error )
        { beacon.onerror = opts.error; }
        if( opts.success )
        { beacon.onload = opts.success; }
 
                // Attach the src for the script call
        beacon.src = opts.url + '?' + qString;
    }
};

MuDoCo.prototype.localBeacon = function(opts){
    opts = opts || {};
    opts.url = opts.url || this.options['localBeacon'];
    this.beacon(opts);
};


MuDoCo.prototype.xssAjax = function(opts){
    // Make sure we have a base object for opts
    opts = opts || {};
    // Setup defaults for options
    opts.url = opts.url || null;
    opts.vars = opts.vars || {};
    opts.error = opts.error || function(){};
    opts.success = opts.success || function(){};
    opts.retry = opts.retry || 3;
    var i = this.nextPendingIndex();

    opts.vars.i = i;
    opts.vars.r = Math.random();

    opts.id = opts.id || ('MuDoCo-xssAjax-' + i);

    // Split up vars object into an array
    var varsArray = [];
    for(var key in opts.vars){ varsArray.push(key + '=' + opts.vars[key]); }
    // Build query string
    var qString = varsArray.join('&');
 
    // Call xss ajax if a url is provided
    if( opts.url ) {
	
	this.xssPending[i] = {
		success: opts.success, 
		error: opts.error, 
		vars: opts.vars,
	};

	// retry if xss query fails on network error...
	if (opts.retry > 0) {
		opts.retry--;
		var self = this;
		this.xssPending[i].timeout = window.setTimeout(function() {
			self.xssAjax(opts);
		}, 10000);
	}
			
	var script = document.createElement('script');
        script.setAttribute('type', 'text/javascript');
        script.setAttribute('src', opts.url + '?' + qString);
        script.setAttribute('id', opts.id);

        var script_id;
        if(script_id = document.getElementById(opts.id)){
            document.getElementsByTagName('head')[0].removeChild(script_id);
        }

        // Insert <script> into DOM
        document.getElementsByTagName('head')[0].appendChild(script);
    }
};

MuDoCo.prototype.mdcXssAjax = function(opts){
	opts = opts || {};
	opts.vars = opts.vars || {};
	opts.vars['a'] = this.getMNonce();
	opts.url = opts.url || (this.options['serverBase'] + '/public/xss.php');
	this.xssAjax(opts);
};

MuDoCo.prototype.xssAjaxCallback = function(res) {
	if (res.i != null && this.xssPending[res.i]) {
		var pending = this.xssPending[res.i];
		this.xssPending[res.i] = null;
		if (pending.timeout) window.clearTimeout(pending.timeout);
		var q = pending.vars.q;
		var mode = res.code == 0 ? 'success' : 'error';
		if (q && this.callbacks[q] != undefined) {
			this.callbacks[q].call(this, mode, res);
		}
		else if (q) {
			this.callbacks.default.call(this, mode, res);
		}
		if (res.code >= 0) {
			pending.success(res);
		}
		else {
			// negative code means nonce failure
			pending.error();
		}
	}
};

MuDoCo.prototype.nextPendingIndex = function() {
	var max = 0;
	for (var i in this.xssPending) {
		max = i;
		if (!this.xssPending[i]) return parseInt(i);
	}
	return parseInt(max) + 1;
}

// process the queue !
MuDoCo.me().processQ();
