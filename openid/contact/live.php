<?php
define('AUTHCOOKIE', 'wl_auth');
define('ERRORCODE', 'error');
define('ERRORDESC', 'error_description');
define('ACCESSTOKEN', 'access_token');
define('AUTHENTICATION_TOKEN', 'authentication_token');
define('CODE', 'code');
define('SCOPE', 'scope');
define('EXPIRESIN', 'expires_in');
define('REFRESHTOKEN', 'refresh_token');

// Update the following values
define('CLIENTID', LIVE_APP_ID);
define('CLIENTSECRET', LIVE_APP_SECRET);

// Make sure this is identical to the redirect_uri parameter passed in WL.init() call.
define('CALLBACK', LIVE_CALLBACK_URL);

define('OAUTHURL', 'https://login.live.com/oauth20_token.srf');  

function buildQueryString($array)
{
    $result = '';
    foreach ($array as $k => $v)
    {
        if ($result == '')
        {
            $prefix = '';
        }
        else
        {
            $prefix = '&';
        }
        $result .= $prefix . rawurlencode($k) . '=' . rawurlencode($v);
    }

    return $result;
}

function parseQueryString($query)
{
    $result = array();
    $arr = preg_split('/&/', $query);
    foreach ($arr as $arg)
    {
        if (strpos($arg, '=') !== false)
        {
            $kv = preg_split('/=/', $arg);
            $result[rawurldecode($kv[0])] = rawurldecode($kv[1]);
        }
    }
    return $result;
}

function sendRequest($url,
    $method = 'GET',
    $data = array(),
    $headers = array())
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if($method == 'POST')
    {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, null,'&'));
    }
    
    $res = curl_exec($ch);
    
    curl_close($ch);
    
    return $res;
    
}


function requestAccessToken($content)
{
    $response = sendRequest(
        OAUTHURL,
        'POST',
        $content);

    if ($response !== false)
    {
        $authToken = json_decode($response);
        if (!empty($authToken) && !empty($authToken->{ACCESSTOKEN}))
        {
            return $authToken;
        }
    }

    return false;
}

function requestAccessTokenByVerifier($verifier)
{
    return requestAccessToken(array(
                                   'client_id' => CLIENTID,
                                   'redirect_uri' => CALLBACK,
                                   'client_secret' => CLIENTSECRET,
                                   'code' => $verifier,
                                   'grant_type' => 'authorization_code'
                              ));
}

function requestAccessTokenByRefreshToken($refreshToken)
{
    return requestAccessToken(array(
                                   'client_id' => CLIENTID,
                                   'redirect_uri' => CALLBACK,
                                   'client_secret' => CLIENTSECRET,
                                   'refresh_token' => $refreshToken,
                                   'grant_type' => 'refresh_token'
                              ));
}

function handlePageRequest()
{
    if (!empty($_GET[ACCESSTOKEN]))
    {
        // There is a token available already. It should be the token flow. Ignore it.
        return;
    }

    $verifier = @$_GET[CODE];
    if (!empty($verifier))
    {
        $token = requestAccessTokenByVerifier($verifier);
        if ($token !== false)
        {
            handleTokenResponse($token);
        }
        else
        {
            handleTokenResponse(null, array(
                                           ERRORCODE => 'request_failed',
                                           ERRORDESC => 'Failed to retrieve user access token.'));
        }

        return;
    }

    $refreshToken = readRefreshToken();
    
    if (!empty($refreshToken))
    {
        $token = requestAccessTokenByRefreshToken($refreshToken);
        if ($token !== false)
        {
            handleTokenResponse($token);
        }
        else
        {
            handleTokenResponse(null, array(
                                           ERRORCODE => 'request_failed',
                                           ERRORDESC => 'Failed to retrieve user access token.'));
        }

        return;
    }

    $errorCode = @$_GET[ERRORCODE];
    $errorDesc = @$_GET[ERRORDESC];

    if (!empty($errorCode))
    {
        handleTokenResponse(null, array(
                                       ERRORCODE => $errorCode,
                                       ERRORDESC => $errorDesc));
    }

    return $token ;
}

function readRefreshToken()
{
    // read refresh token of the user identified by the site.
    return null;
}

function saveRefreshToken($refreshToken)
{
    // save the refresh token and associate it with the user identified by your site credential system.
}

function handleTokenResponse($token, $error = null)
{
    $authCookie = $_COOKIE[AUTHCOOKIE];
    $cookieValues = parseQueryString($authCookie);

    if (!empty($token))
    {
        $cookieValues[ACCESSTOKEN] = $token->{ACCESSTOKEN};
        $cookieValues[AUTHENTICATION_TOKEN] = $token->{AUTHENTICATION_TOKEN};
        $cookieValues[SCOPE] = $token->{SCOPE};
        $cookieValues[EXPIRESIN] = $token->{EXPIRESIN};

        if (!empty($token->{ REFRESHTOKEN }))
        {
            saveRefreshToken($token->{ REFRESHTOKEN });
        }
    }

    if (!empty($error))
    {
        $cookieValues[ERRORCODE] = $error[ERRORCODE];
        $cookieValues[ERRORDESC] = $error[ERRORDESC];
    }

    setrawcookie(AUTHCOOKIE, buildQueryString($cookieValues), 0, '/');
}

?>