<?php
use Illuminate\Support\Facades\URL;
// use URL;
$p_url = URL::previous();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
    <title>Token Mismatch Error</title>
</head>
<body>
<h2>程式錯誤 Exception -- Token Mismatch</h2>
Session 過期或表單櫚置太久，請回 <a href='<?= $p_url ?>'>前頁</a> 再試一次 <br>
Session expired, please go <a href='<?= $p_url ?>'>back</a> and retry.
</body>
</html>
