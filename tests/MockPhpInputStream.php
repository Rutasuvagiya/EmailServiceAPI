<?php

namespace Tests;

/**
 * Class MockPhpInputStream
 *
 * A mock implementation of PHP's input stream (`php://input`) for testing purposes.
 * This class allows setting and retrieving stream data, mimicking real HTTP requests.
 */
class MockPhpInputStream
{
    private $position;
    private $data;

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $this->data = isset($GLOBALS['mocked_input']) ? $GLOBALS['mocked_input'] : '';
        $this->position = 0;
        return true;
    }

    public function stream_read($count)
    {
        $ret = substr($this->data, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    public function stream_write($data)
    {
        $left = substr($this->data, 0, $this->position);
        $right = substr($this->data, $this->position + strlen($data));
        $this->data = $left . $data . $right;
        $this->position += strlen($data);
        return strlen($data);
    }

    public function stream_eof()
    {
        return $this->position >= strlen($this->data);
    }

    public function stream_stat()
    {
        return [];
    }
}
