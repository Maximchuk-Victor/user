<?php
namespace App\Http\Controllers;

use App\Http\Requests\CreateAnalyticaDataRequest;
use App\Http\Requests\CreateKPIRequest;
use App\Http\Requests\CreateTemplateRequest;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\CreateSNChannelRequest;
use App\Http\Requests\CreateQChannelRequest;
use App\Http\Requests\CreateUChannelRequest;
use App\GetSocialData\SocialValidators;
use App\Http\Controllers\Auth;
use App\Model\AnalyticData;
use App\Model\Customer;
use App\Model\Customer_Socialnexus;
use App\Model\KeyPerformanceIndicator;
use App\Model\Quintly;
use App\Model\Report;
use App\Model\Template as Template;
use App\Model\Ubermetrics;
use App\User;
use Convidera\SocialNexus\Model\Channel;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\RedirectResponse;
use Request;

class DatabaseController extends Controller
{
    /**
     * Only logged in users should be able to use this functionality
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Customer_Socialnexus::create(["customerID" => "1", "socialnexusID" => "1"]);
    }

    /**
     * Generic method to create new resource
     *
     * @param $table : Model:
     * @param $input : Array for different Model: []
     * @return \Illuminate\Http\Response
     */
    public static function create($table, $input)
    {
        if ($table == 'Channel') $typ = "Convidera\\SocialNexus\\Model\\" . $table;
        else $typ = "App\\Model\\" . $table;

        $table = new $typ;
        return $table::create($input);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store($table)
    {
        $request = Request::all();
        switch ($table) {
            case 'Quintly':
                $arrData = $this->getQuintlyInput($request);
                break;
            case 'Ubermetrics':
                $arrData = $this->getUbermetricsInput($request);
                break;
            case 'Channel':
                $arrData = $this->getSNInput($request);
                break;
            default:
                $arrData = $request;
        }
        if ($table == 'Channel') {
            $typ = "Convidera\\SocialNexus\\Model\\" . $table;
        } else {
            $typ = "App\\Model\\" . $table;
        }
        $table = new $typ;
        $table::insert($arrData);
        return redirect('upload');
    }

    /**
     * Function to store a new SocialNexus channel and validate the input
     *
     * @param CreateSNChannelRequest $request
     * @return RedirectResponse
     */
    public function storeSNChannel(CreateSNChannelRequest $request, SocialValidators $val)
    {
        if ($val->vaildateFacebookChannel('', $request->all()['auth_id'], $request->all()['auth_secret'], $request->all()['auth_token'])) {
            $ch = Channel::create($request->all());
            //$newChannelId = $newChannelId->toArray()['id'];

            $service = $ch->getService();

            $service->app_id = "1440455616255515";
            $token = $service->extendToken()->queryString();

            $till = Carbon::now()->addSeconds($token->expires)->toDateTimeString();

            $ch->auth_token = $token->access_token;
            if(Carbon::now()->toDateTimeString() >= $till) $till = Carbon::now()->addYears(10)->toDateTimeString();

            $ch->valid_until = $till;
            $ch->save();

            return redirect('customer')
                ->with('message', $request->all()['name'] . " added successfully")
                ->with('customerIDLastUsed', $request->all()['customerID']);
        }
        return redirect('customer')
            ->withErrors(["Error: One or more Inputs were wrong!"])
            ->with('customerIDLastUsed', $request->all()['customerID']);
    }

    /**
     * Function to store a new Quintly channel and validate the input
     *
     * @param CreateQChannelRequest $request
     * @return RedirectResponse
     */
    public function storeQChannel(CreateQChannelRequest $request, SocialValidators $val)
    {
        if ($val->validateQuintly($request->all()['provider'], $request->all()['providerID'])) {
            Quintly::create($request->all());
            return redirect('customer')
                ->with('message', $request->all()['name'] . " added successfully")
                ->with('customerIDLastUsed', $request->all()['customerID']);
        }
        return redirect('customer')
            ->withErrors(["Error: Wrong ProviderID. ProviderID could not be validated!"])
            ->with('customerIDLastUsed', $request->all()['customerID']);
    }

    /**
     * Function to store a new Ubermetrics channel
     *
     * @param CreateUChannelRequest $request
     * @return RedirectResponse
     */
    public function storeUChannel(CreateUChannelRequest $request)
    {
        Ubermetrics::create($request->all());

        return redirect('customer')
            ->with('message', $request->all()['name'] . " added successfully")
            ->with('customerIDLastUsed', $request->all()['customerID']);
    }

    /**
     * Function to store a new customer
     *
     * @param CreateCustomerRequest|CreateTemplateRequest $request
     * @return RedirectResponse
     */
    public function storeCustomer(CreateCustomerRequest $request)
    {
        Customer::create($request->all());
        return redirect('customer')->with('message', $request->all()['name'] . " added successfully");
    }

    /**
     * Function to store a new template
     *
     * @param CreateTemplateRequest $request
     * @return RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeTemplate(CreateTemplateRequest $request)
    {
        Template::create($request->all());
        return redirect('template')->with('message', $request->all()['name'] . " added successfully");
    }

    /**
     * Function to store a new AnalyticData
     *
     * @param CreateAnalyticaDataRequest $request
     * @return RedirectResponse
     */
    public function storeAnalyticData(CreateAnalyticaDataRequest $request)
    {
        AnalyticData::create($request->all());
		// return redirect('template')->with('message', $request->all()['analyticName'] . " added successfully");
		echo $request->all()['analyticName'] . " added successfully";
    }

    /**
     * Function to store a new KPI
     *
     * @param CreateKPIRequest $request
     * @return RedirectResponse
     */
    public function storeKPI(CreateKPIRequest $request)
    {
        KeyPerformanceIndicator::create($request->all());
        return redirect('kpi')
            ->with('message', $request->all()['name'] . " added successfully to " . $request->all()['resource'])
            ->with('KPILastUsed', $request->all()['resource']);
    }

    /**
     * Function to get specific KPI for a given resource
     *
     * @param $resource
     * @return \Illuminate\Support\Collection
     */
    public function getKPIForList($resource)
    {
        return KeyPerformanceIndicator::query()->where('resource', $resource)->lists('name', 'name');
    }


    /**
     * FUER WAS??? xD
     * da steht GET aber es wird formatiert?
     * das gleich auch für die weiter unten
     * Formatting SN Inputs
     *
     * @param $request
     * @return array
     */
    public function getSNInput($request)
    {
        foreach ($request['auth_id'] as $key => $n) {
            $arrData[] = array(
                "auth_id" => $n,
                "name" => $request['name'][$key],
                "auth_token" => $request['auth_token'][$key],
                "provider" => $request['provider'][$key],
                "auth_secret" => $request['auth_secret'][$key],
                "customerID" => $request['customerID'][0]
            );
        }
        return $arrData;
    }

    /**
     * Formatting Quintly Inputs
     *
     * @param $request Request Object of Inputs
     * @return array formatted Quintly data
     */
    public function getQuintlyInput($request)
    {
        foreach ($request['providerID'] as $key => $n) {
            $arrData[] = array(
                "providerID" => $n,
                "provider" => $request['provider'][$key],
                "customerID" => $request['customerID'][0]
            );
        }
        return $arrData;
    }

    /**
     * Formatting Ubermetrics Inputs
     *
     * @param $request Request Object of Inputs
     * @return array formatted Ubermetrics data
     */
    public function getUbermetricsInput($request)
    {
        foreach ($request['logIn'] as $key => $n) {
            $arrData[] = array(
                "logIn" => $n,
                "name" => $request['name'][$key],
                "password" => $request['password'][$key],
                "customerID" => $request['customerID'][0]
            );
        }
        return $arrData;
    }

    /**
     * Generic method to list entries for a given table
     *
     * @param $table : Model
     * @param int|string $id
     * @return \Illuminate\Http\Response
     */
    public static function show($table, $id = "")
    {
        if ($table == 'Channel') $typ = "Convidera\\SocialNexus\\Model\\" . $table;
        else $typ = "App\\Model\\" . $table;

        if ($table == 'Customer') {
            $table = new $typ;
            $user_id = \Auth::user()->id;

            if ($user_id == 2) $table = \App\Model\Customer::where('name', '!=', 'Vodafone')->get();
            elseif ($user_id == 1) $table = \App\Model\Customer::where('name', '!=', 'Unitymedia')->get();;

            return $table;

        } else {
            $table = new $typ;
            return $table::all();
        }

    }

    /**
     * Generic method to list entries for a given parameter and a given clause
     *
     * @param $table
     * @param $where
     * @param $is
     * @return mixed
     */
    public static function showWhere($table, $where, $is)
    {
        if ($table == 'Channel') $typ = "Convidera\\SocialNexus\\Model\\" . $table;
        else $typ = "App\\Model\\" . $table;

        $table = new $typ;

        return $table::query()->where($where, $is)->get();
    }

    /**
     * Generic method to list entries for a given parameter and two given clauses
     *
     * @param $table
     * @param $where
     * @param $is
     * @param $where2
     * @param $is2
     * @return mixed
     */
    public static function showDoubleWhere($table, $where, $is, $where2, $is2)
    {
        $typ = "App\\Model\\" . $table;
        $table = new $typ;
        return $table::query()
            ->where($where, $is)
            ->where($where2, $is2)
            ->get();
    }

    /**
     * Generic method to list entries for a given parameter and three given clauses
     *
     * @param $table
     * @param $where
     * @param $is
     * @param $where2
     * @param $is2
     * @param $where3
     * @param $is3
     * @return mixed
     */
    public static function showTripleWhere($table, $where, $is, $where2, $is2, $where3, $is3)
    {
        $typ = "App\\Model\\" . $table;
        $table = new $typ;
        return $table::query()
            ->where($where, $is)
            ->where($where2, $is2)
            ->where($where3, $is3)
            ->get();
    }

    /**
     * This function returns a list with specific channels per provider from a customer
     *
     * For now, SocialNexus is the only source you can get Facebook data
     * and Quintly collects data from: Twitter, YouTube, Google and Instagram
     *
     * @param string $customer
     * @return array
     */
    public function getCustomerSpecificChannelsPerProvider($customer = "")
    {
        $facebook = [];
        $twitter = [];
        $youtube = [];
        $googleplus = [];
        $instagram = [];
		$linkedin = [];

        $quintly = Quintly::query()->where("customerID", $customer)->get();
        $socialnexus = Channel::query()->where("customerID", $customer)->get();
        $ubermetrics = Ubermetrics::query()->where("customerID", $customer)->get();

        foreach ($quintly as $quent) {
            switch ($quent["provider"]) {
                //No Facebook data will be collected from quitly
                /*
                case "facebook":
                    array_push($facebook, $quent);
                    break;
                */
                case "twitter":
                    array_push($twitter, $quent);
                    break;
                case "youtube":
                    array_push($youtube, $quent);
                    break;
                case "googlePlus":
                    array_push($googleplus, $quent);
                    break;
                case "instagram":
                    array_push($instagram, $quent);
                    break;
                default:
                case "linkedin":
                    array_push($linkedin, $quent);
                    break;
                default:
            }
        }

        foreach ($socialnexus as $snexus) {
            switch ($snexus["provider"]) {
                case "facebook":
                    array_push($facebook, $snexus);
                    break;
                //only Facebook is collected from SocialNexus
                /*
                case "twitter":
                    array_push($twitter, $snexus);
                    break;
                case "youtube":
                    array_push($youtube, $snexus);
                    break;
                case "googleplus":
                    array_push($googleplus, $snexus);
                    break;
                case "instagram":
                    array_push($instagram, $snexus);
                    break;
                */
                default:
            }
        }

        return [
            "Facebook" => $facebook,
            "Twitter" => $twitter,
            "Youtube" => $youtube,
            "Googleplus" => $googleplus,
            "Instagram" => $instagram,
			"Linkedin" => $linkedin,
            "Ubermetrics" => $ubermetrics
        ];
    }


    /**
     * Function to get specific reports for a given customer
     *
     * @param string $customer
     * @return array
     */
    public function getCustomerSpecificReport($customer = "")
    {
        $id = [];
        $templateName = [];
        $reportDatum = [];
        $reportZeitspanne = [];
        $reportZeitspanneBis = [];
        $reportInterval = [];
        $reportLevel = [];
        $reportDownloadlink = [];

        $report = Report::query()->where("customerID", $customer)->get();

        foreach ($report as $rep) {
            $templateid = Template::query()->where("id", $rep['templateID'])->get();
            array_push($id, $rep['id']);
            array_push($templateName, $templateid[0]['name']);
            array_push($reportDatum, $rep['created_at']);
            array_push($reportZeitspanne, ($rep['since']));
            array_push($reportZeitspanneBis, ($rep['until']));
            array_push($reportInterval, $rep['interval']);
            array_push($reportLevel, $rep['level']);
            array_push($reportDownloadlink, $rep['downloadlink']);

        }
        return [
            "Name" => $templateName,
            "Datum" => $reportDatum,
            "Zeitspanne" => $reportZeitspanne,
            "ZeitspanneBis" => $reportZeitspanneBis,
            "Interval" => $reportInterval,
            "Level" => $reportLevel,
            "Downloadlink" => $reportDownloadlink,
            "id" => $id,
        ];
    }


    /**
     * Function to get specific analytic group name for a given template
     *
     * @param $templateID
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAnalyticGroupNamesForTemplate($templateID)
    {
        return AnalyticData::query()
            ->where("templateID", $templateID)
            ->get(["id", "resource", "analyticGroupName", "analyticName", "weight"]);
    }

    /**
     * Update the specified resource and validate the update
     *
     * @param $table
     * @param  int $id
     * @param $column
     * @param $value
     * @param SocialValidators $val
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public static function update($table, $id, $column, $value, SocialValidators $val)
    {
        $test = $table;

        if ($table == 'Channel') $typ = "Convidera\\SocialNexus\\Model\\" . $table;
        else $typ = "App\\Model\\" . $table;

        $table = new $typ;

        $model = $table::findOrFail($id);

        $model->$column = $value;
        if ($test == 'Channel') {
            if ($val->vaildateFacebookChannel('', $model->auth_id, $model->auth_secret, $model->auth_token)) {

                $service = $model->getService();

                $service->app_id = "1440455616255515";
                $token = $service->extendToken()->queryString();

                $till = Carbon::now()->addSeconds($token->expires)->toDateTimeString();


                $model->auth_token = $token->access_token;
                if(Carbon::now()->toDateTimeString() >= $till) $till = Carbon::now()->addYears(10)->toDateTimeString();
                $model->valid_until = $till;

                $model->save();



                return 'The update was succesfully!';
            }
            return 'The update failed!';
        }

        if ($test == 'Quintly') {
            if ($val->validateQuintly($model->provider, $model->providerID)) {
                $model->save();
                return 'The update was succesfully!';
            }
            return 'The update failed!';
        }
        $model->save();
    }

    /**
     * Update the specified resource
     *
     * @param $table
     * @param $id
     * @param $column
     * @param $value
     */
    public static function updateWithoutValidation($table, $id, $column, $value)
    {

        if ($table == 'Channel') $typ = "Convidera\\SocialNexus\\Model\\" . $table;
        else $typ = "App\\Model\\" . $table;

        $table = new $typ;

        $model = $table::findOrFail($id);

        $model->$column = $value;

        $model->save();
    }

    /**
     * Remove the specified resource
     *
     * @param $table
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($table = "", $id = "")
    {
        if ($table == 'Channel') $typ = "Convidera\\SocialNexus\\Model\\" . $table;
        else $typ = "App\\Model\\" . $table;

        $table = new $typ;
        return $table::destroy($id);
    }
}
