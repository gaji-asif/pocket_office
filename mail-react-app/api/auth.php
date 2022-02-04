<?php
require __DIR__ . '/vendor/autoload.php';
include '../../includes/common_lib.php';
UserModel::isAuthenticated();
if($_SESSION['ao_founder'] != 1) {
    die('Insufficient Rights');
}

function update_token($token){

    try {

        $client = new Google_Client();
        $client->setApplicationName('Gmail API PHP Quickstart');
        $client->addScope("https://www.googleapis.com/auth/gmail.readonly");
        $client->setAuthConfig('credentials.json');
        $client->setAccessType('offline');
        $client->setApprovalPrompt ('force');
        $client->setPrompt('select_account consent');
        $client->setAccessToken($token);

        if ($client->isAccessTokenExpired()) {
            $refresh_token = $client->getRefreshToken();
            if(!empty($refresh_token)){
                $client->fetchAccessTokenWithRefreshToken($refresh_token);      
                $token = $client->getAccessToken();
                $token['refresh_token'] = json_decode($refresh_token);
                $token = json_encode($token);
            }
        }

        return $token;

    } catch (Exception $e) { 
        $error = json_decode($e->getMessage());
        if(isset($error->error->message)){
            return 'error '. $error->error->message;
        }
    }
}

function getClient() {
    $conn = new mysqli("pocketofficepro.com", "pocketoffice_xactbid", "Qs(o6X!;m8#L", "pocketoffice_xactbid");

    $sql = "SELECT * FROM refresh_token";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $token = $row['original'];
            $refresh_token = $row['refresh'];
        }
    }
    $conn->close();

    $response = new stdClass();
    $client = new Google_Client();
    $client->setApplicationName('Gmail API PHP Quickstart');
    $client->addScope("https://www.googleapis.com/auth/gmail.readonly");
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setApprovalPrompt ('force');
    $client->setPrompt('select_account consent');
    $client->setAccessToken($token);

    if ($client->isAccessTokenExpired()) {
        $client->refreshToken($refresh_token);
        $newtoken = $client->getAccessToken();
        $client->setAccessToken($newtoken);
    }

    $gmail_service = new Google_Service_Gmail($client);

    $gmail_user = 'me';
    $opt_param = array();
    $optParams['labelIds'] = 'INBOX';
    
    $results = $gmail_service->users_messages->listUsersMessages($gmail_user, $opt_param);
    $messages = $results->getMessages();
    
    $getEmailList = [];
    
    if(count($messages) > 0){
        foreach($messages as $key => $message){
            $messageId = $message->id;
            $message = $gmail_service->users_messages->get($gmail_user, $messageId);
            $headers = $message->getPayload()->getHeaders();
            $subject = array_values(array_filter($headers, function($k){
                return $k['name'] == 'Subject';
            }));
            $getEmailList['list'][$key]['subject'] = $subject[0]->getValue();
            $getEmailList['list'][$key]['message'] = $message->getSnippet();
            $getEmailList['list'][$key]['id'] = $messageId;
            $getEmailList['list'][$key]['threadId'] = $message->threadId;
            $getEmailList['count'] = count($messages);
        }
    }
    
    header("Content-type:application/json"); 
    echo json_encode($getEmailList);
}
getClient();
