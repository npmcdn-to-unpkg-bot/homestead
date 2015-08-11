<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Site;
use App\TwitterAdapter;
use App\SocialMedia;
use DB;
use Twitter;

class GetNBATweets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getnbatweets:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // couldn't determine subdomain when running from command line (ie. php artisan schedule:run)
        // so I couldn't get the twitter keys/tokens to set correctly, and I
        // couldn't get the twitter keys/tokens to reconfigure below,
        // so just call the url so that subdomain may be used to set proper twitter keys/tokens. 
        // see config/ttwitter.php
        file_get_contents('http://nba.' . config('app.domain') . '/twitter/getfeed');
        /*
        Site::getInstance('nowarena.dev', 'nba');
        DB::setDefaultConnection('nba');
        //DB::reconnect('nba');
        //var_dump(DB::getConnections());

        ///var_dump(Site::getSubdomainArr());
        $arr = [];

        $arr['CONSUMER_KEY'] = env('NBA_CONSUMER_KEY');
        $arr['CONSUMER_SECRET'] = env('NBA_CONSUMER_SECRET');
        $arr['ACCESS_TOKEN'] = env('NBA_ACCESS_TOKEN');
        $arr['ACCESS_TOKEN_SECRET'] = env('NBA_ACCESS_TOKEN_SECRET');
  
        $arr['consumer_key'] = env('NBA_CONSUMER_KEY');
        $arr['consumer_secret'] = env('NBA_CONSUMER_SECRET');
        $arr['access_token'] = env('NBA_ACCESS_TOKEN');
        $arr['access_token_secret'] = env('NBA_ACCESS_TOKEN_SECRET');
        Twitter::reconfig($arr);
   


        $this->socialMediaObj = new SocialMedia('nba', 'twitter');
        $this->twitterAdapter = new TwitterAdapter('nbablvd');
        if (($socialMediaArr = $this->twitterAdapter->addStatus()) !== false) {
            $this->socialMediaObj->addSocialMedia($socialMediaArr);
        }
        */
    }
}
