<?php

namespace App\Models\Elastic;

class Attachment
{
    /**
     * @var string
     */
    public $contentType;
    /**
     * @var string
     */
    public $fileName;
    /**
     * @var string
     */
    public $content;
    /**
     * @var string
     */
    public $text;
    /**
     * @var string[]
     */
    public $tags = array();
    /**
     * @var string
     */
    public $id;
}