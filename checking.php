<?php

$key = 'buffaguard_checkingbrowser'; 
$method = 'AES-256-CBC';
$iv = '1234567890123456'; 

$userAgent = $_SERVER['HTTP_USER_AGENT'];
$domain = $_SERVER['HTTP_HOST'];
$tomorrow = date('dmY', strtotime('+1 day'));
$dataToEncrypt = $domain . '/' . $tomorrow . '/' . $userAgent;

$encrypted = openssl_encrypt($dataToEncrypt, $method, $key, 0, $iv);
$encrypted = base64_encode($encrypted); 

if (isset($_COOKIE['_buffa_guard'])) {

    $encryptedData = base64_decode($_COOKIE['_buffa_guard']);
    $decryptedData = openssl_decrypt($encryptedData, $method, $key, 0, $iv);

    list($decryptedDomain, $decryptedDate, $decryptedUserAgent) = explode('/', $decryptedData, 3); 

    $today = date('dmY');
    $tomorrow = date('dmY', strtotime('+1 day'));

    $expectedValueToday = $domain . '/' . $today;
    $expectedValueTomorrow = $domain . '/' . $tomorrow;

    if (($decryptedDomain == $domain) && ($decryptedDate == $today || $decryptedDate == $tomorrow) && ($decryptedUserAgent == $userAgent)) {
      return;
    }
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checking Your Browser</title>
  <style>
      html, body {
          height: 100%;
          margin: 0;
          background-color: #121212; 
          color: #ffffff; 
      }
      body { 
          display: flex; 
          justify-content: center; 
          align-items: center; 
          text-align: center; 
          font-family: Arial, sans-serif; 
          flex-direction: column;
      }
      .message { font-size: 20px; font-weight: bold; margin-bottom: 10px; font-family: 'Helvetica', Arial, sans-serif; }
      .small { font-size: 16px; font-weight: normal; margin-bottom: 10px; }
      #rayId { font-size: 16px; margin-bottom: 10px; }
      .protection {
          font-size: 14px;
          margin-top: 20px;
      }
      #rayIdValue {
          font-family: 'Courier', 'Courier New', monospace;
          font-weight: bold;
      }
      .br {
          font-size: 14px;
          margin-top: 5px;
      }
      .highlight {
          color: #27a4f2; 
      }

  </style>
  <style>

      .loader {
        width: 108px;
        height: 60px;
        color: #269af2;
        --c: radial-gradient(farthest-side,currentColor 96%,#0000);
        background: 
          var(--c) 100% 100% /30% 60%,
          var(--c) 70%  0    /50% 100%,
          var(--c) 0    100% /36% 68%,
          var(--c) 27%  18%  /26% 40%,
          linear-gradient(currentColor 0 0) bottom/67% 58%;
        background-repeat: no-repeat;
        position: relative;
        margin-bottom: 16px; 
      }
      .loader:after {
        content: "";
        position: absolute;
        inset: 0;
        background: inherit;
        opacity: 0.4;
        animation: l7 1s infinite;
      }
      @keyframes l7 {
        to {transform:scale(1.8);opacity:0}
      }
  </style>

</head>
<body>
  <div class="loader"></div>  
  <div class="message">Checking your browser before accessing <?php echo $domain; ?>.</div>
  <div class="br"></div>
  <div class="small">This process is automatic. Your browser will redirect to your request content shortly.</div>
  <div class="br"></div>
  <div class="small">Please allow up to <span id="countdown">5</span> seconds…</div>
  <div class="protection">DDoS protection by <span class="highlight">ConCuMoMong</span></div>
  <div class="br"></div>
  <div id="rayId">Ray ID: <span id="rayIdValue"></span></div>

    <script>
        var waitTime = 5;
        var expireHours = 24; 
        var expireDate = new Date(new Date().getTime() + expireHours * 60 * 60 * 1000);

        document.getElementById('countdown').innerText = waitTime;

        function updateWaitMessage() {
            if(waitTime >= 0) {
                document.getElementById('countdown').innerText = waitTime;
                waitTime--;
                setTimeout(updateWaitMessage, 1000);
            } else {
                document.cookie = "_buffa_guard=<?php echo $encrypted; ?>; expires=" + expireDate.toUTCString() + "; path=/";
                window.location.reload();
            }
        }

      function generateRayId() {
          var numbers = '0123456789';
          var letters = 'abcdef'; 
          var characters = numbers + letters; 
          var rayId = '';
          for (var i = 0; i < 16; i++) { 
              rayId += characters.charAt(Math.floor(Math.random() * characters.length));
          }
          document.getElementById('rayIdValue').innerText = rayId;
      }

        generateRayId();
        updateWaitMessage();
    </script>
</body>
</html>
<?php
    exit();
?>