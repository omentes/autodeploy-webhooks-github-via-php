<?php

$hookSecret = null;  # set NULL to disable check

$rawPost = NULL;
if ($hookSecret !== NULL) {
    if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
        throw new \Exception("HTTP header 'X-Hub-Signature' is missing.");
    } elseif (!extension_loaded('hash')) {
        throw new \Exception("Missing 'hash' extension to check the secret code validity.");
    }
    list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2) + array('', '');
    if (!in_array($algo, hash_algos(), TRUE)) {
        throw new \Exception("Hash algorithm '$algo' is not supported.");
    }
    $rawPost = file_get_contents('php://input');
    if ($hash !== hash_hmac($algo, $rawPost, $hookSecret)) {
        throw new \Exception('Hook secret does not match.');
    }
};

if (!isset($_SERVER['CONTENT_TYPE'])) {
    throw new \Exception("Missing HTTP 'Content-Type' header.");
} elseif (!isset($_SERVER['HTTP_X_GITHUB_EVENT'])) {
    throw new \Exception("Missing HTTP 'X-Github-Event' header.");
}
switch ($_SERVER['CONTENT_TYPE']) {
    case 'application/json':
        $json = $rawPost ?: file_get_contents('php://input');
        break;
    case 'application/x-www-form-urlencoded':
        $json = $_POST['payload'];
        break;
    default:
        throw new \Exception("Unsupported content type: $_SERVER[HTTP_CONTENT_TYPE]");
}

$payload = json_decode($json);
switch (strtolower($_SERVER['HTTP_X_GITHUB_EVENT'])) {
    case 'ping':
        echo 'pong';
        break;
    case 'push':
        pushHandler($payload);
        break;
    default:
        header('HTTP/1.0 404 Not Found');
        echo "Event:$_SERVER[HTTP_X_GITHUB_EVENT] Payload:\n";
        var_export($payload);
}


/**
 * @param Object $payload
 */
function pushHandler(Object $payload): void
{
    if (isset($payload) && isset($payload->ref) && $payload->ref == 'refs/heads/alpha') {
        echo 'alpha ';
        if (isset($payload) && isset($payload->head_commit->message) && strpos($payload->head_commit->message, 'Merge pull') !== false) {
            echo 'Catch!';
            exec('sh ../deploy.alpha.sh');
        }
        die();
    }
    if (isset($payload) && isset($payload->ref) && $payload->ref == 'refs/heads/beta') {
        echo 'beta ';
        if (isset($payload) && isset($payload->head_commit->message) && strpos($payload->head_commit->message, 'Merge pull') !== false) {
            echo 'Catch!';
            exec('sh ../deploy.beta.sh');
        }
        die();
    }
    if (isset($payload) && isset($payload->ref) && $payload->ref == 'refs/heads/master') {
        echo 'beta ';
        if (isset($payload) && isset($payload->head_commit->message) && strpos($payload->head_commit->message, 'Merge pull') !== false) {
            echo 'Catch!';
            exec('sh ../deploy.production.sh');
        }
        die();
    }
    echo 'Void.';
}
