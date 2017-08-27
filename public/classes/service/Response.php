<?php
/**
 * Created by PhpStorm.
 * User: ming
 * Date: 8/12/2017
 * Time: 9:57 PM
 */

class Response implements JsonSerializable {
    private $status;
    private $message;
    private $data;

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function jsonSerialize(){
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data
        ];
    }
}