<?php

//服务端的seed，自己修改，增加随机性,需要与login.php中的seed保持一致
$server_token_seed = 233;

// 读取留言信息
function getMessages()
{
    $filename = 'message.json';

    // 检查文件是否存在，如果不存在则创建一个空文件
    if (!file_exists($filename)) {
        file_put_contents($filename, '[]');
    }

    $messages = file_get_contents($filename);
    return json_decode($messages, true);
}
//生成token
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
// 查询留言
function queryMessages()
{
    $messages = getMessages();

    if (empty($messages)) {
        return 'empty';
    } else {
        return json_encode($messages);
    }
}

// 发表留言
function postMessage($email, $content, $ip, $token, $deviceId)
{
    // 验证 token 是否有效
    if ($token == askToken($email, $deviceId)) {
        // 创建新留言
        $newMessage = [
            'email' => $email,
            'time' => time(),
            'ip' => $ip,
            'content' => $content
        ];

        // 读取既有的留言信息
        $messages = getMessages();

        // 将新留言信息添加到留言数组中
        $messages[] = $newMessage;

        // 将更新后的留言数组写入文件
        file_put_contents('message.json', json_encode($messages));

        return 'ok';
    } else {
        return 'invalid';
    }
}
//删除留言
function deleteMessage($email, $index, $token, $deviceId)
{
    $messages = getMessages();

    // 检查索引是否有效
    if (isset($messages[$index])) {
        // 身份验证
        if (($messages[$index]['email'] != $email)) {
            return 'invalid';
        }
        // token验证
        if ($token != askToken($email, $deviceId)) {
            return 'invalid';
        }
        // 删除留言
        unset($messages[$index]);
        // 重新索引数组
        $messages = array_values($messages);

        // 更新留言文件
        file_put_contents('message.json', json_encode($messages));

        return 'ok';
    } else {
        return 'invalid';
    }
}

//获取ip
function getip()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
        $ip = getenv("HTTP_CLIENT_IP");
    } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
        $ip = getenv("REMOTE_ADDR");
    } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = "unknown";
    }
    return $ip;
}

// 根据请求参数调用相应的功能
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 查询留言
    $response = queryMessages();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['content']) && isset($_POST['token']) && isset($_POST['device'])) {
        // 发表留言
        $email = $_POST['email'];
        $content = $_POST['content'];
        $token = $_POST['token'];
        $deviceId = $_POST['device'];
        $response = postMessage($email, $content, getip(), $token, $deviceId);
    } else {
        $response = 'invalid';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['email']) && isset($_GET['position']) && isset($_GET['token']) && isset($_GET['device'])) {
        // 删除留言
        $email = $_GET['email'];
        $position = $_GET['position'];
        $token = $_GET['token'];
        $deviceId = $_GET['device'];
        $response = deleteMessage($email, $position, $token, $deviceId);
    } else {
        $response = 'invalid';
    }
}

// 返回 JSON 格式的响应
header('Content-Type: application/json');
echo $response;
