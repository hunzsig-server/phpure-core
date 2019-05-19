<?php

namespace PhpureCore\IO;

use PhpureCore\Config\Crypto as Config;
use PhpureCore\Handle;

class Crypto
{

    /**
     * @param $str
     * @return string
     */
    private static function encrypt(string $str)
    {
        $type = Config::get('io_request_type');
        $secret = Config::get('io_request_secret');
        $iv = Config::get('io_request_iv');
        if (!$type || !$secret || !$iv) {
            Handle::abort('Crypto encrypt error');
        }
        return openssl_encrypt($str, $type, $secret, 0, $iv);
    }

    /**
     * @param $str
     * @return string
     */
    private static function decrypt(string $str)
    {
        $type = Config::get('io_request_type');
        $secret = Config::get('io_request_secret');
        $iv = Config::get('io_request_iv');
        if (!$type || !$secret || !$iv) {
            Handle::abort('Crypto encrypt error');
        }
        return openssl_decrypt($str, $type, $secret, 0, $iv);
    }

    /**
     * 展示所有的 Cipher Methods
     */
    public static function cipherMethods()
    {
        Handle::abort('Cipher Methods', openssl_get_cipher_methods());
    }

    /**
     * 获得加密的自定义协议头
     * 当body数据以此为协议头时，认为其为加密串
     */
    public static function protocol(): string
    {
        return Config::get('io_request_protocol') ?? 'CRYPTO|';
    }

    /**
     * 是否隐秘请求
     * @param Request $request
     * @return bool
     */
    public static function isCrypto(Request $request): bool
    {
        return strpos($request->body, self::protocol()) === 0;
    }

    /**
     * 对照 IO token
     * @param Request $request
     * @return bool
     */
    public static function checkToken(Request $request)
    {
        if (empty($request->header['platform']) || empty($request->header['token'])
            || empty($request->header['client_id']) || empty($request->header['pure'])) {
            return false;
        }
        if (Config::get('io_token') !== $request->header['token']) {
            return false;
        }
        $token = strtolower(trim($request->header['platform'] . $request->header['token'] . $request->header['client_id'] /*. $request->body*/));
        $sha256 = hash_hmac('sha256', $token, Config::get('io_token_secret'));
        if (!$sha256 || $request->header['pure'] !== $sha256) {
            return false;
        }
        return true;
    }

    /**
     * 处理input
     * @param Request $request
     * @return bool
     */
    public static function input(Request $request)
    {
        return self::decrypt(str_replace_once(self::protocol(), '', $request->body));
    }

    /**
     * 处理request
     * @param Request $request
     * @return bool
     */
    public static function response(Request $request)
    {
        return self::encrypt(str_replace_once(self::protocol(), '', $request->body));
    }

}