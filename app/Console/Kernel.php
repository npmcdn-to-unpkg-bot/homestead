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
        \App\Console\Commands\GetNBAInstagram::class,
        \App\Console\Commands\GetAKInstagram::class,        
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
        $schedule->command('getnbatweets:run')->cron('*/5 * * * *')->sendOutputTo('/tmp/cronlog.txt');
        $schedule->command('getaktweets:run')->cron('*/6 * * * *')->sendOutputTo('/tmp/cronlog.txt');
        $schedule->command('getnbainstagrams:run')->cron('*/7 * * * *')->sendOutputTo('/tmp/cronlog.txt');
        $schedule->command('getakinstagram:run')->cron('*/1 * * * *')->sendOutputTo('/tmp/cronlog.txt');
    }
}
