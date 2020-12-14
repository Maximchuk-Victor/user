<?php

namespace App\Http\Controllers;

use App\GetPostLevelData\GetFacebookPostData;
use App\GetPostLevelData\GetGoogleplusPostData;
use App\GetPostLevelData\GetInstagramPostData;
use App\GetPostLevelData\GetLinkedinPostData;
use App\GetPostLevelData\GetTwitterPostData;
use App\GetPostLevelData\GetYoutubePostData;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Convidera\ChannelMetrics\Contracts\ChannelMetrics;
use \Convidera\SocialNexus\Model\Channel as SocialNexus;

class ResController extends Controller
{
    public function twitter(ChannelMetrics $cmp)
    {
        $metrics = [
            'twitter' => [
                'reach' => [
                    'interactionRate' => ['2'],
                ],
                'engagement' => [
                    'retweets' => ['2'],
                    'favs' => ['3'],
                    'impressions' => ['2'],
                    'replies' => ['1'],
                ],

            ],

        ];

        $twitter = GetTwitterPostData::withQuintly($cmp, "2015-12-01", "2015-12-31", "daily", "171313", $metrics['twitter'], null);
        $twitter->getTwitterData();
        dd($twitter);
    }

    public function twitter2($cmp, $csvData)
    {
        $metrics = [
            'twitter' => [
                'reach' => [
                    'interactionRate' => [],
                    'engagements' => [],
                ],
                'engagement' => [
                    'retweets' => [],
                    'impressions' => [],
                ],

            ],

        ];

        $twitter = GetTwitterPostData::withQuintly($cmp, "2015-11-02", "2015-11-22", "daily", "171313", $metrics['twitter'], $csvData);
        $twitter->getTwitterData();
        dd($twitter);
    }

    public function instagram(ChannelMetrics $cmp)
    {
        $metrics = [
            'instagram' => [
                'reach' => [
                    'interactionRate' => ['1'],
                ],
                'engagement' => [
                    'likes' => ['2'],
                    'comments' => ['2'],
                ],

            ],

        ];

        $twitter = GetInstagramPostData::withQuintly($cmp, "2015-12-01", "2015-12-31", "daily", "172150", $metrics['instagram']);
        $twitter->getInstagramData();
        dd($twitter);
    }
	
    public function linkedin(ChannelMetrics $cmp)
    {
        $metrics = [
            'linkedin' => [
                'reach' => [
                    'interactionRate' => ['1'],
                ],
                'engagement' => [
                    'likes' => ['2'],
                    'comments' => ['2'],
                ],

            ],

        ];

        $twitter = GetLinkedinPostData::withQuintly($cmp, "2015-12-01", "2015-12-31", "daily", "172150", $metrics['linkedin']);
        $twitter->getLinkedinData();
        dd($twitter);
    }


    public function youtube(ChannelMetrics $cmp)
    {
        $metrics = [
            'youtube' => [
                'reach' => [
                    'dislikes' => ['3'],
                ],
                'engagement' => [
                    'likes' => ['2'],
                ],

            ],

        ];
        //103692
        $twitter = GetYoutubePostData::withQuintly($cmp, "2015-10-01", "2015-12-31", "daily", "171314", $metrics['youtube'], null);
        $twitter->getYoutubeData();
        dd($twitter);
    }

    public function youtube2($cmp, $csvData, $metrics)
    {


        $twitter = GetYoutubePostData::withQuintly($cmp, "2015-11-15", "2015-12-25", "daily", "171314", $metrics['youtube'], $csvData);
        $twitter->getYoutubeData();
        dd($twitter);
    }

    public function googleplus(ChannelMetrics $cmp)
    {
        $metrics = [
            'googleplus' => [
                'reach' => [
                    'circledBy' => ['3'],
                ],
                'engagement' => [
                    'likes' => ['2'],
                ],

            ],

        ];

        $twitter = GetGoogleplusPostData::withQuintly($cmp, "2015-09-01", "2015-12-31", "daily", "171315", $metrics['googleplus']);
        $twitter->getGoogleplusData();
        dd($twitter);
    }

    public function facebook()
    {
        $channel = SocialNexus::findOrNew("36");

        $metrics = [
            'facebook' => [
                'reach' => [
                    'shares' => ['2'],
                    'likes' => ['1'],
                ],
                'engagement' => [
                    'comments' => ['3'],
                ],

            ],

        ];

        $facebookData = new GetFacebookPostData($channel->getService(), $channel->auth_id, "daily", "2015-10-01", "2016-01-10", $metrics['facebook']);
        $completeData = $facebookData->getAllData();
        dd($completeData['2015-10-01']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
