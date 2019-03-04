<?php

namespace App\Services\Document;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Parse
{
    /**
     * @param array $fileIds
     *
     * @return void
     */
    public function parse(array $fileIds)
    {
        /** @var UploadedFile $file */
        foreach ($fileIds as $file) {
            // TODO: use a message queue
            shell_exec('cd /var/www && /usr/bin/nohup php artisan document:parsetext ' . $file['_id'] . ' > /dev/null &');
        }
    }
}