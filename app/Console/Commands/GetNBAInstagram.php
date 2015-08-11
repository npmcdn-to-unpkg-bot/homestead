<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Site;
use App\TwitterAdapter;
use App\SocialMedia;
use DB;
use Twitter;

class GetNBAInstagram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getnbainstagram:run';

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

        file_get_contents('http://nba.' . config('app.domain') . '/instagram/getfeed');

    }
}
