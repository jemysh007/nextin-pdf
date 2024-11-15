<?php

namespace App\Http\Controllers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JDController extends Controller
{
    //

    public function removeRefined()
    {
        $files = glob('refined/*'); // get all file names

        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }

        $file = new Filesystem;

        $file->cleanDirectory('storage/app');
        $file->cleanDirectory('storage/app/compress');
        $file->cleanDirectory('storage/app/imageToPdf');
        $file->cleanDirectory('storage/app/lockPdf');
        $file->cleanDirectory('storage/app/merge');
        $file->cleanDirectory('storage/app/normalSplit');
        $file->cleanDirectory('storage/app/office2Pdf');
        $file->cleanDirectory('storage/app/pdf2Images');
        $file->cleanDirectory('storage/app/repairPdf');
        $file->cleanDirectory('storage/app/rotatePDF');
        $file->cleanDirectory('storage/app/unlockPdf');
        $file->cleanDirectory('storage/app/watermarkPdf');
    }
}
