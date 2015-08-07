<?php
namespace App;

/**
 * Singleton for accessing generic data sitewide based on subdomain
 * TODO make work with parked domains eg. abbotkinneybl.com instead of just abbotkinneyblvd.nowarena.com
 *
 * @author matt
 */
class Site {
    
    protected static $instance = null;
    public static $subdomain = '';
    public static $subdomainArr = array();
    public static $domain = '';
    
    public static function getInstance($subdomain = '')
    {
        if (static::$instance === null ) {
            $instance = new static;
            $instance::setDomains();            
            $instance::setSubdomainArr();
            
            // TODO check for invalid subdomain
        }
        return $instance;
    }
    
    private static function setDomains()
    {
        // determine database connection when a command line/cron
        if (PHP_SAPI == 'cli') {

        } else {
            $arr = explode('.', $_SERVER['HTTP_HOST']);
            if (count($arr) ==2 ) {
                // if the array is only a length of 2, that means it is the domain name plus extension,
                // eg. nowarena.com, so no subdomain
                self::$subdomain = '';
                self::$domain = strtolower($_SERVER['HTTP_HOST']);
            } else {
                self::$subdomain = strtolower($arr[0]);
                unset($arr[0]);
                self::$domain = strtolower(implode('.', $arr));
            }

//            preg_match("~([a-z0-9_]+)\.[a-z0-9_.]+~i", $_SERVER['HTTP_HOST'], $arr);
//            self::$subdomain = isset($arr[1]) ? strtolower($arr[1]) : '';
        }

    }
    
    private static function setSubdomainArr()
    {
        $subdomainData = self::getSubdomainData();
        if (isset($subdomainData[self::$subdomain])) {
            self::$subdomainArr = $subdomainData[self::$subdomain];
        } else {
            self::$subdomainArr = array('Not recognizing subdomain: ' . self::$subdomain);
        }
    }
    
    public static function getSubdomainData() 
    {
        $defaultArr = array(
            'name' => 'NowArena.com',
            'nameShort' => 'NowArena.com',
            'nameLong' => 'NowArena.com',
            'database' => 'users',
            'pageTitle' => 'NowArena.com : What\'s happening now',
            'baseUrl' => 'http://' . self::$subdomain . '.' . self::$domain,
            'thumbUrl' => '',
        );
        return array(
            '' => $defaultArr,
            'www' => $defaultArr,
            'nba' => array(
                'name' => 'NBA',
                'nameShort' => 'NBA',
                'nameLong' => 'National Basketball Association',
                'database' => 'nba',
                'pageTitle' => 'NowArena.com : Latest social media from the NBA',
                'baseUrl' => 'http://nba.' . self::$domain,
                'description' => 'Lebron posts some dank memes, Griffin goes camping, Kobe prepares his legacy and more.', 
                'twitterScreenName' => 'nbablvd'
            ),
            'abbotkinneyblvd' => array(
                'name' => 'Abbot Kinney',
                'nameShort' => 'Abbot Kinney Blvd.',
                'nameLong' => 'Abbot Kinney Boulevard',
                'database' => 'abbotkinney',
                'pageTitle' => "NowArena.com : Latest social media about what's going down and coming up on Abbot Kinney Blvd.",
                'baseUrl' => 'http://abbotkinneyblvd.' . self::$domain,
                'description' => "Food, fashion, cocktails, pop culture, street art from the denizens and curators of the premiere boulevard in Venice Beach, California.",
                'twitterScreenName' => 'abbotkinneybl'
            )
        );
        
    }
    
    public static function getSubdomainArr($subdomain = '') 
    {
                
        $subdomain = ($subdomain =='' ) ? self::$subdomain : $subdomain;
        
        $subdomainData = self::getSubdomainData();

        return $subdomainData[$subdomain];
        
    }
    
    public static function getDatabase() 
    {
        return self::$subdomainArr['database'];
        
    }
    
    public static function getPageTitle()
    {        
        return self::$subdomainArr['pageTitle'];
    }
    
    public static function getNameLong()
    {
        return self::$subdomainArr['nameLong'];
    }
    
    public static function getNameShort()
    {
        return self::$subdomainArr['nameShort'];
    }
    
    public static function getDomain()
    {
        return self::$domain;
    }
    
    public static function getSubdomain()
    {
        return self::$subdomain;
    }
    
    public static function getTwitterScreenName()
    {
        return self::$subdomainArr['twitterScreenName'];
    }
    
    private function __construct() {}
    private function __clone() {}
    
}
