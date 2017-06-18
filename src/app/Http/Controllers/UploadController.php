<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class UploadController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @return Response
     */
    public function upload()
    {
        echo "<pre>";
        print_r($_REQUEST);
        print_r($_FILES);
        echo "</pre>";

        if (isset($_FILES['files'])) {
            foreach ($_FILES['files']['tmp_name'] as $tmpName) {
                if (!file_exists($tmpName)) {
                    continue;
                }
                //    echo file_get_contents($tmpName);
            }
        }

        return new JsonResponse(['success' => 1]);
    }
}