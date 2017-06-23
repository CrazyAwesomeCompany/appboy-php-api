<?php

namespace CAC\AppBoy\Api;


class AppBoyException extends \Exception
{
    protected $content;

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}
