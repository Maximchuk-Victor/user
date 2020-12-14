<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Carbon\Carbon;
use App\Reports\ExclusivReport;
use ExclusivReportHandler;
use Request;
use Excel;

class ExcelController extends Controller {

    protected $excel;


    /**
     * @param ExclusivReport $import is the loaded csv file
     */
    public function importReport(ExclusivReport $import)
    {
        // get the results
        //$import->take(10); // get 10 first results
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $import->handleImport();
    }


    /**
     * This Method reads the input file
     */
    public function readFile()
    {

        $fileName_FaceBook = Request::all();

        //dd($fileName_FaceBook['From']);
        //dd($fileName_FaceBook);

        $dateStart = Carbon::parse($fileName_FaceBook['dateStartFrom']);
        $dateEnd = Carbon::parse($fileName_FaceBook['dateEndsTo']);

        dump($dateStart);
        dump($dateEnd);

        // The object returned by the file method is an instance of the Symfony\Component\HttpFoundation\File\UploadedFile

        $path = $fileName_FaceBook['fileName_FaceBook']->getRealPath();
        $name =$fileName_FaceBook['fileName_FaceBook']->getClientOriginalName();
        //dump($fileName_FaceBook['fileName_FaceBook']->getClientOriginalName());

        $this->readMetricsFromCSV($path, $dateStart, $dateEnd);


    }

    /**
     * @param $path is the path to the file which is used to read the file as Excel class
     *
     *  this method converts the csv to an array and reads through its
     */
    public function readMetricsFromCSV($path, $dateStart, $dateEnd)
    {

        Excel::load($path, function($reader)
        {
            // Format dates + set date format
            //$reader->formatDates(true, 'Y-m-d');

            $reader->take(10);

            dump($reader->toArray());

            $reader->select(['date', 'lifetime_total_likes' , 'daily_unlikes'])->get();

            dump($reader->toArray());

            $dateStart = Carbon::parse("2015-10-10");
            $dateEnd = Carbon::parse("2015-10-16");
            dump($dateStart);


            // iterate through a date periode
            $value=[];
            $valve=0;
            $date = clone($dateStart);
            while ($date->lte($dateEnd))
            {
                $value[$date->format('Y-m-d')]=$valve++;
                $date->addDay();
            }
            dd($value);




            /*
                //dd($reader);
                // Loop through all sheets
                $reader->each(function($sheet)
                {
                    if($sheet->getTitle() != null)
                        dump($name=$sheet->getTitle());

                    // Loop through all rows

                     $sheet->each(function($row)
                    {


                        if( ($row[0] != null) )
                        {
                            //dump($row->toArray());
                            $info=$row;//->toArray();
                            dump($info);
                        }

                        //dump($row);

                    }); // row end

                }); // sheet end

            */

        });
    }


}