<?php

//服务端的seed，自己修改，增加随机性，需要与index.php中的seed一致
$server_token_seed = 233;

// 处理登录请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['code']) && isset($_POST['device'])) {
        $email = $_POST['email'];
        $device = $_POST['device'];
        $code = $_POST['code'];
        // 验证码验证逻辑
        if ($code == generateVerificationCode($email, $device)) {
            // 验证码正确
            $response  = json_encode(generateToken($email, $device));
        } else {
            // 验证码不正确
            $response = 'code_invalid';
        }
    } else {
        if (isset($_POST['email']) && isset($_POST['device']) && isset($_POST['token'])) {
            $email = $_POST['email'];
            $device = $_POST['device'];
            $token = $_POST['token'];
            // 验证token
            if ($token == AskToken($email, $device)) {
                $response  = json_encode(generateToken($email, $device));
            } else {
                $response = 'token_invalid';
            }
        } else {
            if (isset($_POST['email']) && isset($_POST['device'])) {
                $email = $_POST['email'];
                $device = $_POST['device'];
                // 执行发送包含验证码的邮件的操作
                $code = generateVerificationCode($email, $device);
                $response = sendVerificationEmail($email, $code);
            } else {
                // 参数缺失
                $response = 'invalid';
            }
        }
    }
} else {
    // 非法请求
    $response = 'invalid';
}

echo $response;


function generateVerificationCode($email, $deviceId)
{
    // 取时间戳的300间隔倍数
    $timestamp = floor(time() / 300) * 300;
    // 构建种子用于生成固定验证码
    global $server_token_seed;
    $seed = $timestamp . $email . $deviceId . $server_token_seed;;
    // 初始化随机数生成器
    mt_srand((int)(substr(hexdec(hash('sha256', $seed)), 0, 10) * 100000));
    // 生成4位随机数验证码
    $code = mt_rand(100000, 999999);
    return $code;
}

function sendVerificationEmail($email, $code)
{
    $logFile = 'send.log';
    if (!file_exists($logFile)) {
        file_put_contents($logFile, '');
    }
    $logContents = file($logFile);

    $email_limit = false;
    foreach ($logContents as $line) {
        $parts = explode(':', $line);
        $thisEmail = $parts[0];
        $timestamp = $parts[1];
        if (time() - $timestamp > 300) {
            $logContents = array_diff($logContents, array($line));
        } else {
            if ($email == $thisEmail) {
                $email_limit = true;
            }
        }
    }

    file_put_contents($logFile, implode("\n", $logContents));
    if ($email_limit) {
        return 'too_frequent';
    }

    //发送邮件的代码，根据自己服务器的情况修改
    $command = "echo 'code: $code' | mailx -s 'code' $email";

    shell_exec($command);
    $logEntry = "$email:" . time();
    file_put_contents($logFile, $logEntry . "\n", FILE_APPEND);
    return 'code_please';
}

function generateToken($email, $deviceId)
{
    // 取时间戳的250w间隔倍数
    $timestamp = floor(time() / 2500000) * 2500000;
    // 提前生成下一组token
    $timestamp1 = $timestamp + 2500000;
    // 两组
    global $server_token_seed;
    $seed0 = $timestamp . $email . $deviceId . $server_token_seed;
    $seed1 = $timestamp1 . $email . $deviceId . $server_token_seed;
    // 生成token
    $token0 = hash('sha256', $seed0);
    $token1 = hash('sha256', $seed1);
    return  [
        'token' => $token0,
        'f_token' => $token1
    ];
}

function askToken($email, $deviceId)
{
    // 取时间戳的250w间隔倍数
    $timestamp = floor(time() / 2500000) * 2500000;
    global $server_token_seed;
    $seed = $timestamp . $email . $deviceId . $server_token_seed;
    // 生成token
    $token = hash('sha256', $seed);
    return  $token;
}
