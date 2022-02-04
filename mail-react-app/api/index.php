<?php
require __DIR__ . '/vendor/autoload.php';
include '../../includes/common_lib.php';
include './config.php';
/*echo $_SESSION['ao_founder'];die;
if ($_SESSION['ao_founder'] != 1) {
  die('Insufficient Rights');
}*/
function getClient()
{
  $client = new Google_Client();
  $client->setApplicationName('XOOM');
  $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
  $client->setAuthConfig('credentials.json');
  $client->setRedirectUri('https://xactbid.pocketofficepro.com/mail-react-app/api/index.php');
  $client->setAccessType('offline');
  $client->setApprovalPrompt('force');
  global $mysqli;
  // $mysqli = new mysqli("localhost", "root", "root", "xactbid_web");
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
  }

  $tokenPath = './auth-token/' . $_SESSION['ao_userid'] . '-token.json';
  if (file_exists($tokenPath)) {
    $accessToken = json_decode(
      file_get_contents($tokenPath),
      true
    );
  } else {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));

    if (isset($_GET['code'])) {
      $authCode = $_GET['code'];
      $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
      header('Location: ' . filter_var(
        $GLOBALS['SERVER_URI'].'/mail-react-app/api/index.php',
        FILTER_SANITIZE_URL
      ));
      if (!file_exists(dirname($tokenPath))) {
        mkdir(dirname($tokenPath), 0700, true);
      }
      $sql = "INSERT INTO gmail_token (user_id) VALUES(" . $_SESSION['ao_userid'] . ")";
      $mysqli->query($sql);

      file_put_contents($tokenPath, json_encode($accessToken));
    } else {
      exit('No code found');
    }
  }
  $client->setAccessToken($accessToken);

  if ($client->isAccessTokenExpired()) {

    $refreshTokenSaved = $client->getRefreshToken();

    $client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);

    $accessTokenUpdated = $client->getAccessToken();

    $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;

    $accessToken = $refreshTokenSaved;
    $client->setAccessToken($accessToken);

    file_put_contents(
      $tokenPath,
      json_encode($accessTokenUpdated)
    );
  }
  return $client;
}
getClient();
?>
<script>
  window.close();
</script>