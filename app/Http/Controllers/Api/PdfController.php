<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ilovepdf\Ilovepdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Ilovepdf\HtmlpdfTask;
use Ilovepdf\ImagepdfTask;
use Ilovepdf\OfficepdfTask;
use Ilovepdf\PdfjpgTask;
use Ilovepdf\ProtectTask;
use Ilovepdf\RepairTask;
use Ilovepdf\RotateTask;
use Ilovepdf\SplitTask;
use Ilovepdf\UnlockTask;
use Ilovepdf\WatermarkTask;

class PdfController extends Controller
{
    //

    function json($res = [0, "Invalid Request", []])
    {

        echo json_encode([
            "status" => boolval($res[0]),
            "msg" => $res[1],
            "data" => (object)$res[2],
        ]);
    }
    function compress(Request $request)
    {

        $res = [0, "Invalid Request", []];

        try {

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'mimes:pdf', 'max:10240'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $file = $request->file('file');

            $tool = "compress";
            $path = $file->storePublicly($tool);
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $ilovepdf = new Ilovepdf(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));
            $myTask = $ilovepdf->newTask($tool);

            $file1 = $myTask->addFile(Storage::path($path));

            $outPutFileName = $name;
            $myTask->setOutputFilename($outPutFileName);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            // dd(public_path("refined"));
            $myTask->download(public_path("refined"));


            $res[0] = true;
            $res[1] = "File Compressed Successfully";
            $res[2]['file'] = url("refined/" . $outPutFileName);
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        $this->json($res);
    }

    function merge(Request $request)
    {
        $res = [0, "Invalid Request", []];

        try {
            $validator = Validator::make(
                $request->all(),

                [
                    'files' => ['required', 'array', 'min:2'],
                    'files.*' => ['required', 'mimes:pdf', 'max:10240'],
                ],
                [
                    'files.*.required' => 'Please upload a pdf',
                    'files.*.mimes' => 'Only pdf files are allowed',
                    'files.*.max' => 'Sorry! Maximum allowed size for a File is 10MB',
                ]
            );

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $files = $request->file('files');

            $tool = "merge";

            foreach ($files as $file) {
                $names[] = $file->getClientOriginalName();
                $paths[] = $file->storePublicly($tool);
                $extensions[] = $file->getClientOriginalExtension();
            }

            $ilovepdf = new Ilovepdf(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));
            $myTask = $ilovepdf->newTask($tool);

            foreach ($paths as $path) {
                $file1 = $myTask->addFile(Storage::path($path));
            }

            $outPutFileName = "MergedPDF" . time();
            $myTask->setOutputFilename($outPutFileName);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            $myTask->download(public_path("refined"));

            Storage::delete($paths);

            $res[0] = true;
            $res[1] = "File Merged Successfully";
            $res[2]['file'] = url("refined/" . $outPutFileName . ".pdf");
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        return $this->json($res);
    }

    function image2Pdf(Request $request)
    {
        $res = [0, "Invalid Request", []];

        try {
            $validator = Validator::make(
                $request->all(),

                [
                    'files' => ['required', 'array', 'min:1'],
                    'files.*' => ['required', 'mimes:jpg,jpeg,png,bmp', 'max:10240'],
                ],
                [
                    'files.*.required' => 'Please upload an image',
                    'files.*.mimes' => 'Only jpg,jpeg,png, and bmp images are allowed',
                    'files.*.max' => 'Sorry! Maximum allowed size for a File is 10MB',
                ]
            );

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $files = $request->file('files');

            $tool = "imageToPdf";

            foreach ($files as $file) {
                $names[] = $file->getClientOriginalName();
                $paths[] = $file->storePublicly($tool);
                $extensions[] = $file->getClientOriginalExtension();
            }

            $myTask = new ImagepdfTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));

            foreach ($paths as $path) {
                $file1 = $myTask->addFile(Storage::path($path));
            }

            $outPutFileName = "image2Pdf" . time();
            $myTask->setOutputFilename($outPutFileName);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            $myTask->download(public_path("refined"));

            Storage::delete($paths);

            $res[0] = true;
            $res[1] = "Image to PDF Converted Successfully";
            $res[2]['file'] = url("refined/" . $outPutFileName . ".pdf");
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        return $this->json($res);
    }

    function office2Pdf(Request $request)
    {
        $res = [0, "Invalid Request", []];

        try {

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'mimes:doc,docx,ppt,pptx,xls,xlsx,odt,odp,ods', 'max:10240'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $file = $request->file('file');

            $tool = "office2Pdf";
            $path = $file->storePublicly($tool);
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $myTask = new OfficepdfTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));

            $file1 = $myTask->addFile(Storage::path($path));

            $outPutFileName = $name;
            $fileNameWithoutExt = pathinfo($outPutFileName, PATHINFO_FILENAME) . time();;

            $myTask->setOutputFilename($fileNameWithoutExt);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            // dd(public_path("refined"));
            $myTask->download(public_path("refined"));

            $res[0] = true;
            $res[1] = "File Converted to Pdf Successfully";
            $res[2]['file'] = url("refined/" . $fileNameWithoutExt . ".pdf");
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        $this->json($res);
    }

    function pdf2Image(Request $request)
    {

        $res = [0, "Invalid Request", []];

        try {

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'mimes:pdf', 'max:10240'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $file = $request->file('file');

            $tool = "pdf2Images";
            $path = $file->storePublicly($tool);
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $myTask = new PdfjpgTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));

            $file1 = $myTask->addFile(Storage::path($path));

            $outPutFileName = $name;
            $fileNameWithoutExt = pathinfo($outPutFileName, PATHINFO_FILENAME) . time();;

            $myTask->setOutputFilename($fileNameWithoutExt);
            $myTask->setPackagedFilename($fileNameWithoutExt);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            $myTask->download(public_path("refined"));

            if (file_exists("refined/" . $fileNameWithoutExt . ".zip")) {
                $fileToSend = "refined/" . $fileNameWithoutExt . ".zip";
            } else if (file_exists("refined/" . $fileNameWithoutExt . "-0001.jpg")) {
                $fileToSend = "refined/" . $fileNameWithoutExt . "-0001.jpg";
            } else {
                $fileToSend = "";
            }

            if ($fileToSend != "") {
                $res[0] = true;
                $res[1] = "PDF to Images Converted Successfully";
                $res[2]['file'] = url($fileToSend);
            } else {
                $res[1] = "Something wrong with the file";
            }
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        $this->json($res);
    }

    function rotatePdf(Request $request)
    {

        $res = [0, "Invalid Request", []];

        $degree = $request->get('degree', 90);
        try {

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'mimes:pdf', 'max:10240'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $file = $request->file('file');

            $tool = "rotatePDF";
            $path = $file->storePublicly($tool);
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $myTask = new RotateTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));

            $file1 = $myTask->addFile(Storage::path($path));

            $file1->setRotation($degree);

            $outPutFileName = $name;
            $myTask->setOutputFilename($outPutFileName);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            $myTask->download(public_path("refined"));

            $res[0] = true;
            $res[1] = "File Rotated Successfully";
            $res[2]['file'] = url("refined/" . $outPutFileName);
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        $this->json($res);
    }

    function get_domain($url)
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }

    function html2Pdf(Request $request)
    {

        $res = [0, "Invalid Request", []];

        try {

            $validator = Validator::make($request->all(), [
                'link' => ['required', 'url'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $link = $request->get('link');

            $tool = "html2Pdf";

            $myTask = new HtmlpdfTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));

            $file = $myTask->addUrl($link);

            // $myTask->setPageMargin(20);
            // $myTask->setSinglePage(true);

            $domain = $this->get_domain($link);
            $outPutFileName = time();
            $myTask->setOutputFilename($outPutFileName);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            $file = $myTask->download(public_path("refined"));

            $res[0] = true;
            $res[1] = "HTML to PDF Converted Successfully";
            $res[2]['file'] = url("refined/" . $outPutFileName . ".pdf");
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        $this->json($res);
    }

    function lockPdf(Request $request)
    {

        $res = [0, "Invalid Request", []];

        try {

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'mimes:pdf', 'max:10240'],
                'password' => ['required'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $file = $request->file('file');
            $password = $request->get("password");
            $tool = "lockPdf";
            $path = $file->storePublicly($tool);
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $myTask = new ProtectTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));

            $file1 = $myTask->addFile(Storage::path($path));
            $myTask->setPassword($password);

            $outPutFileName = time() . $name;
            $myTask->setOutputFilename($outPutFileName);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            $myTask->download(public_path("refined"));

            $res[0] = true;
            $res[1] = "File Protected with Given Password Successfully";
            $res[2]['file'] = url("refined/" . $outPutFileName);
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        $this->json($res);
    }

    function unlockPdf(Request $request)
    {

        $res = [0, "Invalid Request", []];
        try {

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'mimes:pdf', 'max:10240'],
                'password' => ['required'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $file = $request->file('file');
            $password = $request->get("password");
            $tool = "unlockPdf";
            $path = $file->storePublicly($tool);
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $myTask = new UnlockTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));

            $file1 = $myTask->addFile(Storage::path($path));
            $file1->setPassword($password);

            $outPutFileName = time() . $name;
            $myTask->setOutputFilename($outPutFileName);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            $myTask->download(public_path("refined"));

            $res[0] = true;
            $res[1] = "File Unlocked Successfully";
            $res[2]['file'] = url("refined/" . $outPutFileName);
        } catch (\Ilovepdf\Exceptions\ProcessException  $th) {
            $res[1] = "Your Password is Wrong";
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        $this->json($res);
    }

    function splitPdf(Request $request)
    {

        $res = [0, "Invalid Request", []];

        try {

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'mimes:pdf', 'max:10240'],
                'range' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $file = $request->file('file');
            $range = $request->get("range");
            $tool = "normalSplit";
            $path = $file->storePublicly($tool);
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();



            $myTask = new SplitTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));

            $file1 = $myTask->addFile(Storage::path($path));

            $outPutFileName = $name;
            $fileNameWithoutExt = pathinfo($outPutFileName, PATHINFO_FILENAME);
            $outPutFolderName = "Split" . time();

            $myTask->setRanges($range);

            $myTask->setOutputFilename($outPutFileName);

            $myTask->setPackagedFilename($outPutFolderName);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            $myTask->download(public_path("refined"));


            if (file_exists("refined/" . $outPutFolderName . ".zip")) {
                $fileToSend = "refined/" . $outPutFolderName . ".zip";
            } else if (file_exists("refined/" . $fileNameWithoutExt . "-$range.pdf")) {
                $fileToSend = "refined/" . $fileNameWithoutExt . "-$range.pdf";
            } else {
                $fileToSend = "";
            }

            if ($fileToSend != "") {
                $res[0] = true;
                $res[1] = "File Splitted Successfully";
                $res[2]['file'] = url($fileToSend);
            } else {
                $res[1] = "Something wrong with the file";
            }
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        $this->json($res);
    }

    function splitPdfMerge(Request $request)
    {

        $res = [0, "Invalid Request", []];

        try {

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'mimes:pdf', 'max:10240'],
                'range' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $file = $request->file('file');
            $range = $request->get("range");
            $tool = "normalSplit";
            $path = $file->storePublicly($tool);
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $myTask = new SplitTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));

            $file1 = $myTask->addFile(Storage::path($path));

            $outPutFileName = time() . $name;

            $myTask->setRanges($range);

            $myTask->setOutputFilename($outPutFileName);

            $myTask->setMergeAfter(true);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            $myTask->download(public_path("refined"));

            $res[0] = true;
            $res[1] = "File Compressed Successfully";
            $res[2]['file'] = url("refined/" . $outPutFileName);
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        $this->json($res);
    }


    function repairPdf(Request $request)
    {

        $res = [0, "Invalid Request", []];
        try {

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'mimes:pdf', 'max:10240'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $file = $request->file('file');
            $tool = "repairPdf";
            $path = $file->storePublicly($tool);
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $myTask = new RepairTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));

            $file1 = $myTask->addFile(Storage::path($path));

            $outPutFileName = time() . $name;
            $myTask->setOutputFilename($outPutFileName);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            $myTask->download(public_path("refined"));

            $res[0] = true;
            $res[1] = "File Repaired Successfully";
            $res[2]['file'] = url("refined/" . $outPutFileName);
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        $this->json($res);
    }

    function watermarkPdf(Request $request)
    {

        $res = [0, "Invalid Request", []];
        try {

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'mimes:pdf', 'max:10240'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $file = $request->file('file');
            $wMode = $request->get('mode', 'text');
            $wText = $request->get('text', 'Watermark Text');
            $wPages = $request->get('pages', 'all');
            $wVposition = $request->get('vertical_position', 'bottom');
            $wHposition = $request->get('horizontal_position', 'center');
            $wFontFamily = $request->get('font-family', 'Arial Unicode MS');
            $wFontStyle = $request->get('font_style', "Bold");
            $wFontSize = $request->get('font_size', 14);
            $wFontColor = $request->get('font_color', "#000000");
            $wTp = $request->get('transparency', 50);

            $tool = "watermarkPdf";
            $path = $file->storePublicly($tool);
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $myTask = new WatermarkTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));

            $myTask->setMode($wMode);
            $myTask->setText($wText);
            $myTask->setPages($wPages);
            $myTask->setVerticalPosition($wVposition);
            $myTask->setHorizontalPosition($wHposition);
            // $myTask->setVerticalPositionAdjustment("100");
            // $myTask->setHorizontalPositionAdjustment("100");

            $myTask->setFontFamily($wFontFamily);
            $myTask->setFontStyle($wFontStyle);
            $myTask->setFontSize($wFontSize);
            $myTask->setFontColor($wFontColor);
            $myTask->setTransparency($wTp);
            $myTask->setLayer("above");

            $file1 = $myTask->addFile(Storage::path($path));

            $outPutFileName = time() . $name;
            $myTask->setOutputFilename($outPutFileName);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            $myTask->download(public_path("refined"));

            $res[0] = true;
            $res[1] = "Water-mark added Successfully";
            $res[2]['file'] = url("refined/" . $outPutFileName);
        } catch (\Throwable $th) {
            $res[1] = "Something wrong with the file";
        }

        $this->json($res);
    }

    function watermarkPdfImage(Request $request)
    {

        $res = [0, "Invalid Request", []];
        try {

            $validator = Validator::make($request->all(), [
                'file' => ['required', 'mimes:pdf', 'max:10240'],
                'image' => ['required', 'mimes:jpg,jpeg,png,bmp', 'max:10240'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = $message;
                }
                $res[1] = $errors[0];
                return $this->json($res);
            }

            $file = $request->file('file');
            $file2 = $request->file('image');
           
            $tool = "watermarkPdf";
            $path = $file->storePublicly($tool);
            $path2 = $file2->storePublicly($tool);

            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $myTask = new WatermarkTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILOVEPDF_SECRET_KEY'));

            $myTask->setMode("image");

            $file1 = $myTask->addFile(Storage::path($path));

            $watermakImage = $myTask->addElementFile(Storage::path($path2));

            $myTask->setImageFile($watermakImage);

            $outPutFileName = time() . $name;
            $myTask->setOutputFilename($outPutFileName);

            $myTask->execute();

            $uploadFolder = public_path('') . $tool;
            Storage::makeDirectory($uploadFolder);

            $myTask->download(public_path("refined"));

            $res[0] = true;
            $res[1] = "Water-mark added Successfully";
            $res[2]['file'] = url("refined/" . $outPutFileName);
        } catch (\Throwable $th) {
            dd($th);
            $res[1] = "Something wrong with the file";
        }

        $this->json($res);
    }
}
