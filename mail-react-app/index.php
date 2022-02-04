<?php
include '../includes/common_lib.php';
include '../includes/config/global.php'
?>
<!doctype html>
<html lang="en">

<head>
  <title>Xactbid Gmail Integration</title>
  <meta charset="utf-8">
  <link rel="manifest" href="%PUBLIC_URL%/manifest.json" />
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://xactbid.pocketofficepro.com/mail-react-app/devapp/build/css/bundle.f9a4728f.css" type="text/css">
</head>

<body>
  <?php
  $tokenPath = ROOT_PATH. '/mail-react-app/api/auth-token/' . $_SESSION['ao_userid'] . '-token.json';
  if (!file_exists($tokenPath)) {
  ?>
    <a href="javascript:void(0);" onclick="login()" style="margin-top: 20px; position: absolute">Please Authorize</a>
  <?php
  } else {
  ?>
    <div id="app"></div>
    <script type="text/javascript" src="https://xactbid.pocketofficepro.com/mail-react-app/devapp/build/scripts/bundle.f9a4728f.js" ></script>
    <script type="text/javascript" src="https://xactbid.pocketofficepro.com/mail-react-app/devapp/build/scripts/modernizr.f9a4728f.js" ></script>
  <?php
  }
  ?>
</body>
<script>
  function login() {
    popupCenter('http://xactbid.pocketofficepro.com/mail-react-app/api/index.php', 'Authorize', 400, 400)
  }

  function popupCenter(url, title, w, h) {
    // Fixes dual-screen position                             Most browsers      Firefox
    var dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
    var dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var systemZoom = width / window.screen.availWidth;
    var left = (width - w) / 2 / systemZoom + dualScreenLeft
    var top = (height - h) / 2 / systemZoom + dualScreenTop
    var newWindow = window.open(url, title,
      `
      scrollbars=yes,
      width=${w / systemZoom}, 
      height=${h / systemZoom}, 
      top=${top}, 
      left=${left}
      `
    )

    if (window.focus) newWindow.focus();
  }
</script>

</html>