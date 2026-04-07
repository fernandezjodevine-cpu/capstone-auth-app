<?php
class GoogleOAuth {
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $tokenEndpoint = 'https://oauth2.googleapis.com/token';
    private $userInfoEndpoint = 'https://www.googleapis.com/oauth2/v2/userinfo';
    private $authorizationEndpoint = 'https://accounts.google.com/o/oauth2/v2/auth';

    public function __construct($clientId, $clientSecret, $redirectUri) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    public function getAuthorizationUrl($scope = ['profile', 'email']) {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scope),
            'access_type' => 'offline',
            'state' => bin2hex(random_bytes(16))
        ];
        
        $_SESSION['oauth_state'] = $params['state'];
        return $this->authorizationEndpoint . '?' . http_build_query($params);
    }

    public function verifyState($state) {
        return isset($_SESSION['oauth_state']) && $_SESSION['oauth_state'] === $state;
    }

    public function getAccessToken($code) {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri
        ];

        $ch = curl_init($this->tokenEndpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function getUserInfo($accessToken) {
        $token = is_array($accessToken) ? ($accessToken['access_token'] ?? null) : $accessToken;
        if (!$token) {
            return null;
        }

        $ch = curl_init($this->userInfoEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
