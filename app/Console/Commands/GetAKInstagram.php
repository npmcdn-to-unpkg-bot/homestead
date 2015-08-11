<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetAKInstagram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getakinstagram:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get tweets from abbotkinneybl home timeline.';

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
        //
        // see GetNBATweets for notes
        file_get_contents('http://abbotkinneyblvd.' . config('app.domain') . '/instagram/getfeed');
    }
}
