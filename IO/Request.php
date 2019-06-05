<?php
/**
 * Request
 */

namespace PhpureCore\IO;

use Parse;
use PhpureCore\Core;
use PhpureCore\Mapping\BootType;
use PhpureCore\Exception\Exception;

/**
 * Class Request
 * @package PhpureCore\IO
 */
class Request
{
    /**
     * @var \PhpureCore\Bootstrap\Cargo
     */
    public $cargo = null;

    public $crypto = false;
    public $local = false;
    public $header = null;
    public $cookie = null;
    public $method = null;
    public $content_type = null;
    public $body = '';

    public $client_id = null;
    public $ip = '127.0.0.1';

    /**
     * @var $input \PhpureCore\IO\Input
     */
    public $input = null;

    /**
     * build Request by BootType
     */
    public function init()
    {
        $this->input = Core::get(\PhpureCore\IO\Input::class);
        switch ($this->cargo->getBootType()) {
            case BootType::AJAX_HTTP:
                $header = array();
                $server = array();
                foreach ($_SERVER as $k => $v) {
                    $server[strtolower($k)] = $v;
                    if (strpos($k, 'HTTP_') === 0) {
                        $header[strtolower(str_replace('HTTP_', '', $k))] = $v;
                    }
                }
                $this->header = $header;
                $this->cookie = $_COOKIE;
                $this->method = strtoupper($server['request_method']);
                $this->content_type = !empty($server['content_type']) ? strtolower(explode(';', $server['content_type'])[0]) : null;
                $this->input->setFile(Parse::fileData($_FILES));
                break;
            case BootType::SWOOLE_HTTP:
                break;
            case BootType::SWOOLE_WEB_SOCKET:
                break;
            case BootType::SWOOLE_TCP:
                break;
            default:
                Exception::throw('Request invalid boot type');
                break;
        }
        if (!Crypto::checkToken($this)) {
            Exception::throw('welcome');
        }
        //
        $this->client_id = $this->header['client_id'] ?? '';
        $this->input->setStack($this->header['stack'] ?? '');
        // IP
        $ip = null;
        $ip === null && $ip = $this->header['x_real_ip'] ?? null;
        $ip === null && $ip = $this->header['client_ip'] ?? null;
        $ip === null && $ip = $this->header['x_forwarded_for'] ?? null;
        $ip === null && $ip = $server['remote_addr'] ?? null;
        $ip && $this->ip = $ip;
        $this->local = ($ip === '127.0.0.1');
        if (!$this->ip) {
            Exception::throw('iam pure');
        }
        // 解密协议
        // Crypto::cipherMethods();
        switch ($this->method) {
            case 'GET':
                switch ($this->content_type) {
                    case 'multipart/form-data':
                        $body = $_GET['body'] ?? null;
                        $this->body = is_string($body) ? $body : json_encode($_GET);
                        break;
                    default:
                        $this->body = file_get_contents('php://input') ?? '';
                        break;
                }
                break;
            case 'POST':
                switch ($this->content_type) {
                    case 'multipart/form-data':
                        $body = $_POST['body'] ?? null;
                        $this->body = is_string($body) ? $body : json_encode($_POST);
                        break;
                    default:
                        $this->body = file_get_contents('php://input') ?? '';
                        break;
                }
                break;
            case 'DEFAULT':
                Exception::throw('method error');
                break;
        }
        $this->crypto = Crypto::isCrypto($this);
        if ($this->crypto === true) {
            $inputData = json_decode(Crypto::input($this), true) ?? [];
        } else {
            $inputData = json_decode($this->body, true) ?? [];
        }
        $this->input->setData($inputData);
    }

    public function __construct(object $cargo)
    {
        $this->cargo = $cargo;
        return $this;
    }

}