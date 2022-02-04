<?php
error_reporting(0);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
include './config.php';
function getClient($ao_userid)
{
  $client = new Google_Client();
  $client->setApplicationName('XOOM');
  $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
  $client->setAuthConfig('credentials.json');
  $client->setRedirectUri($GLOBALS['SERVER_URI'].'/mail-react-app/api/index.php');
  $client->setAccessType('offline');
  $client->setApprovalPrompt('force');

  $tokenPath = './auth-token/' . $ao_userid . '-token.json';echo "<pre>";
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

      file_put_contents($tokenPath, json_encode($accessToken));
    } else {
      exit('No code found');
    }
  }
  $client->setAccessToken($accessToken);
  
  
  if ($client->isAccessTokenExpired()) 
  {
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

function getLabels($ao_userid)
{  
  $client = getClient($ao_userid);
  $clientSecret = 'XuY0nlbPlL-wWGABBrEGijbv';
  $token = $client->getAccessToken();
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://gmail.googleapis.com/gmail/v1/users/me/labels?key=" . $clientSecret,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer " . $token['access_token']
    ),
  ));

  $response = curl_exec($curl);
  curl_close($curl);
  return json_decode($response, true);
}

function getMailList($labelName, $ao_userid)
{
  $client = getClient($ao_userid);
  $clientSecret = 'XuY0nlbPlL-wWGABBrEGijbv';
  $token = $client->getAccessToken();
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://gmail.googleapis.com/gmail/v1/users/me/messages?labelIds={$labelName}&key={$clientSecret}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer " . $token['access_token']
    ),
  ));

  $response = curl_exec($curl);
  curl_close($curl);
  return json_decode($response, true);
}

function getMessageFull($id, $format, $ao_userid)
{
  $client = getClient($ao_userid);
  $clientSecret = 'XuY0nlbPlL-wWGABBrEGijbv';
  $token = $client->getAccessToken();
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://gmail.googleapis.com/gmail/v1/users/me/messages/{$id}?format={$format}&key={$clientSecret}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer " . $token['access_token']
    ),
  ));

  $response = curl_exec($curl);
  curl_close($curl);
  return json_decode($response, true);
}

function getAttachment($id,$atch_id, $format, $ao_userid)
{
  $client = getClient($ao_userid);
  $clientSecret = 'XuY0nlbPlL-wWGABBrEGijbv';
  $token = $client->getAccessToken();
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://gmail.googleapis.com/gmail/v1/users/me/messages/{$id}/attachments/{$atch_id}?format={$format}&key={$clientSecret}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer " . $token['access_token']
    ),
  ));

  $response = curl_exec($curl);
  curl_close($curl);
  return json_decode($response, true);
}



$sql = "SELECT u.user_id FROM users u, gmail_token g WHERE u.user_id=g.user_id";
$getUserIds = $mysqli->query($sql);

if ($getUserIds->num_rows > 0) {
  while ($row = $getUserIds->fetch_assoc()) {
    $ao_userid = $row['user_id'];
    $getEmailList = [];
    $inserts = array();
    $allLabels = getLabels($ao_userid);
  
    //echo "<pre>";print_r($allLabels);die;

    if (isset($allLabels['labels']) && count($allLabels['labels']) > 0) {
      foreach ($allLabels['labels'] as $i => $labels) {
        $mailList = getMailList('INBOX',$ao_userid);//$labels['id'], 
        
        if (isset($mailList['messages']) && count($mailList['messages']) > 0) {
          foreach ($mailList['messages'] as $key => $list) {
            $fullMessage = getMessageFull($list['id'], 'full', $ao_userid);
            
            $subject = array_values(array_filter($fullMessage['payload']['headers'], function ($k) {
              return $k['name'] == 'Subject';
            }));
            $from = array_values(array_filter($fullMessage['payload']['headers'], function ($k) {
              return $k['name'] == 'From';
            }));
            $to = array_values(array_filter($fullMessage['payload']['headers'], function ($k) {
              return $k['name'] == 'To';
            }));

            $snippet = mysqli_real_escape_string($mysqli, $fullMessage['snippet']);
            $subjectValue = mysqli_real_escape_string($mysqli, $subject[0]['value']);
            $fromValue = $from[0]['value'] ? mysqli_real_escape_string($mysqli, $from[0]['value']) : '';
            $toValue = mysqli_real_escape_string($mysqli, $to[0]['value']);
            
           /* $attach_arr=[];
            foreach($fullMessage['payload']['parts'] as $key=>$attachment)
            {
                if($key!=0)
                {
                    $attach_id = $attachment['body']['attachmentId'];
                    
                    $attach_arr[] = getAttachment($list['id'],$attach_id, 'full', $ao_userid);
                    
                }
            }
            //die;
            echo "<pre>";print_r($attach_arr);die;*/
            
            $sql = "SELECT message_id FROM gmail_import WHERE mail_date='".$fullMessage['internalDate']."' AND message_id='".$fullMessage['id']."' AND label_name='". $labels['name']."' AND from_name='".$fromValue."' AND to_mail='".$toValue."' AND subject='".$subjectValue."'";
            $getmsg = $mysqli->query($sql);
            $getmsg->num_rows == 0;
            if($getmsg->num_rows==0 && !empty($fullMessage['id']))
            {
                $inserts[] = "('" . $fullMessage['id'] . "', '" . $fullMessage['threadId'] . "', '" . $labels['name'] . "', '" . $fromValue . "', '" . $toValue . "', '" . $subjectValue . "', '" . $snippet . "', '" . $fullMessage['internalDate'] . "', " . $ao_userid . ")";
            }
          }
          //echo "<pre>";print_r($inserts);die;
        }
      }
      //echo "<pre>";print_r($inserts);die;
      $sqlInsertEmailQuery = "INSERT IGNORE INTO gmail_import (message_id, thread_id, label_name, from_name, to_mail, subject, snippet, mail_date, user_id) VALUES " . implode(", ", $inserts) . "";
      if ($mysqli->query($sqlInsertEmailQuery) === FALSE) {
        echo "Error: " . $sqlInsertEmailQuery . "<br>" . $mysqli->error;
      }
      else
      {
          echo "Syncronization Don Successfuly"; 
      }
    } else {
      $getEmailList['isError'] = true;
      $getEmailList['message'] = 'Not Found';
    }
  }
}
