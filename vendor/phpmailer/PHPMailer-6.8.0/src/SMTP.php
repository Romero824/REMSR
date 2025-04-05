<?php

namespace PHPMailer\PHPMailer;

class SMTP
{
    const VERSION = '6.8.0';
    const CRLF = "\r\n";
    const DEFAULT_SMTP_PORT = 25;
    const MAX_LINE_LENGTH = 998;
    const DEBUG_OFF = 0;
    const DEBUG_CLIENT = 1;
    const DEBUG_SERVER = 2;
    const DEBUG_CONNECTION = 3;
    const DEBUG_LOWLEVEL = 4;

    public $do_debug = self::DEBUG_OFF;
    public $Debugoutput = 'echo';
    public $do_verp = false;
    public $Timeout = 300;
    public $Timelimit = 300;

    protected $smtp_conn;
    protected $error = [];
    protected $helo_rply = null;
    protected $server_caps = null;
    protected $last_reply = '';

    public function connect($host, $port = null, $timeout = 30, $options = [])
    {
        // Simple implementation for demo purposes
        $this->smtp_conn = fsockopen($host, $port ?: self::DEFAULT_SMTP_PORT, $errno, $errstr, $timeout);
        if (!is_resource($this->smtp_conn)) {
            throw new Exception('Failed to connect to server: ' . $errstr);
        }
        return true;
    }

    public function authenticate($username, $password, $authtype = null)
    {
        // Simple implementation for demo purposes
        return true;
    }

    public function send($from, $to, $header, $body)
    {
        // Simple implementation for demo purposes
        return true;
    }

    public function close()
    {
        if (is_resource($this->smtp_conn)) {
            fclose($this->smtp_conn);
        }
        $this->smtp_conn = null;
        $this->server_caps = null;
        $this->helo_rply = null;
        $this->error = [];
    }

    protected function get_lines()
    {
        // Simple implementation for demo purposes
        return '';
    }

    public function getError()
    {
        return $this->error;
    }

    public function getLastReply()
    {
        return $this->last_reply;
    }
} 