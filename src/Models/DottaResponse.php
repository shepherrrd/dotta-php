<?php
namespace Dotta\Models;

class DottaResponse
{
    public $status;
    public $message;
    public $data;
    public function __construct($status = false, $message = '', $data = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }
}
