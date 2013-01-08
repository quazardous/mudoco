<?php include_once 'etc/config.php'; ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $_SERVER['HTTP_HOST']; ?></title>
<script type="text/javascript">

// MuDoCo Queue
var _mdcq = _mdcq || [];

// init MuDoCo with MuDoCo server base URL and local beacon URL
_mdcq.push({
	init: 'serverBase',
	value: '<?php print $serverBase; ?>',});

_mdcq.push({
	init: 'localBeacon',
	value: '<?php print $localBeacon; ?>',});

// add the JS part of the hello plugin
// see this.callbacks['default'] in mudoco/server/public/mdc.js
_mdcq.push({
	plugin: 'hello',
	value: function(mode, params, success, error)
	{
		this.callbacks.default.call(this, mode, params, success, error);
		if (mode == 'success') {
			  alert(params.data.hello);	  
		}
	},});
	
_mdcq.push({query: 'session', vars: {k: 'foo'}});

// add a JS callback to the queue
_mdcq.push(function() { document.testcookie.foo.value=this.data.foo; });
	
// asynch call static MuDoCo JS script
(function() {
  var mdc = document.createElement('script'); mdc.type = 'text/javascript'; mdc.async = true;
  mdc.src = '<?php print $serverBase; ?>/public/mdc.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(mdc, s);
})();
  
</script>
</head>
<body>
<h1><?php echo $_SERVER['HTTP_HOST']; ?></h1>
<p>
Test some custom plugin.
<a href="javascript:MuDoCo.me().query('hello');">hello</a>
</p>
<p>
Set some multi domain cookie.
</p>
<form name="testcookie" onsubmit="javascript:MuDoCo.me().query('session', {k: 'foo', v:this.foo.value}); return false;">
<strong>foo</strong> = <input type="text" name="foo" />
<input type="submit" value="Cookie test" />
</form>
</body>
</html>