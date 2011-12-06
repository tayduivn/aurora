<?php

$header_background='#285e85';
$header_color='#ffffff';

$body_background='#ffffff';
$body_color='#333';
$body_link='#285e85';


print <<<END

<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang='en' xml:lang='en' xmlns="http://www.w3.org/1999/xhtml">
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Twitter</title>
        
  </head>

  <body class="yui-skin-sam inikoo">
<div>

<script src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
new TWTR.Widget({
  version: 2,
  type: 'profile',
  rpp: 4,
  interval: 30000,
  width: 280,
  height: 300,
  theme: {
    shell: {
      background: '$header_background',
      color: '$header_color'
    },
    tweets: {
      background: '$body_background',
      color: '$body_color',
      links: '$body_link'
    }
  },
  features: {
    scrollbar: false,
    loop: false,
    live: false,
    behavior: 'all'
  }
}).render().setUser('inikoo_devel').start();
</script>
</div>
</body>
</html>

END;

?>