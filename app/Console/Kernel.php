<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Site;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\GetNBATweets::class,
        \App\Console\Commands\GetAKTweets::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //Site::getInstance('nowarena.dev', 'abbotkinneyblvd');
        //$schedule->call('TwitterController@addstatus')->everyMinute();//->everyMinute();
        //$schedule->call('twitter@addstatus')->everyMinute();
        $schedule->command('getnbatweets:run')->everyFiveMinutes()->sendOutputTo('/tmp/cronlog.txt');
        $schedule->command('getaktweets:run')->everyTenMinutes()->sendOutputTo('/tmp/cronlog.txt');
    }
}
