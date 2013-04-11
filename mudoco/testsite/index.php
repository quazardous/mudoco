<?php include_once __DIR__.'/etc/config.php'; ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $_SERVER['HTTP_HOST']; ?></title>
<script type="text/javascript">

// MuDoCo Queue
var q = [];

// init MuDoCo with MuDoCo server base URL and local beacon URL
q.push({
	init: 'serverBase',
	value: '<?php print $serverBase; ?>'});

q.push({
	init: 'localBeacon',
	value: '<?php print $localBeacon; ?>'});

// add the JS part of the hello plugin
// see this.callbacks.fallback in mudoco/server/public/mdc.js
q.push({
	plugin: 'hello',
	value: function(mode, params, success, error)	{
		this.callbacks.fallback.call(this, mode, params, success, error);
		if (mode == 'success') {
			  alert(params.data.hello);	  
		}
	}});
	
q.push({query: 'session', vars: {k: 'foo'}});

// add a JS callback to the queue
q.push(function() { document.testcookie.foo.value=this.data.foo; });

var _mdc;
// asynch call static MuDoCo JS script
(function() {
  var e = document.createElement('script'); e.type = 'text/javascript'; e.async = true; e.onload = function() { _mdc = new MuDoCo(q); _mdc.processQ(); };
  e.src = '<?php print $serverBase; ?>/public/mdc.min.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(e, s);
})();
  
</script>
</head>
<body>
<h1><?php echo $_SERVER['HTTP_HOST']; ?></h1>
<p>
Test some custom plugin.
<a href="javascript:_mdc.query('hello');">hello</a>
</p>
<p>
Set some multi domain cookie.
</p>
<form name="testcookie" onsubmit="javascript:_mdc.query('session', {k: 'foo', v:this.foo.value}); return false;">
<strong>foo</strong> = <input type="text" name="foo" />
<input type="submit" value="Cookie test" />
</form>
</body>
</html>