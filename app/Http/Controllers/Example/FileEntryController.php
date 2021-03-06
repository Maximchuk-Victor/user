<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Fileentry;
use Illuminate\Http\Response;
use Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FileEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $entries = Fileentry::all();

        return view('fileentries.index', compact('entries'));
    }

    public function add() {
        $file = Request::file('filefield');
        $extension = $file->getClientOriginalExtension();
        Storage::disk('local')->put($file->getFilename().'.'.$extension,  File::get($file));
        $entry = new Fileentry();
        $entry->mime = $file->getClientMimeType();
        $entry->original_filename = $file->getClientOriginalName();
        $entry->filename = $file->getFilename().'.'.$extension;

        $entry->save();

        return redirect('fileentry');
    }

    /**
     * Function to get files from server (.xls reports)
     *
     * @param $filename
     * @return $this
     */
    public function get($filename){
        $file = Storage::disk('local')->get('/xlsReports/'.$filename);

        return (new Response($file, 200))
            ->header('Content-Type', "");
    }
}
