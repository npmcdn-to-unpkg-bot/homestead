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
    
    public static function getInstance($domain = false, $subdomain = false)
    {
        if (static::$instance === null ) {
            $instance = new static;
            $instance::setDomains($domain, $subdomain);            
            $instance::setSubdomainArr();
            
            // TODO check for invalid subdomain
        }
        return $instance;
    }
    
    /*
     * Set the domain and subdomain
     */
    private static function setDomains($domain = false, $subdomain = false)
    {
        
        if ($domain !== false && $subdomain !== false ) {
            // set domain and subdomain manually
            self::$domain = $domain;
            self::$subdomain = $subdomain;
        } else {
            // set values based on what was determined in config/app.php
            self::$domain = config('app.domain');
            self::$subdomain = config('app.subdomain');
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
                'twitterScreenName' => 'nbablvd',
                'categoryDepth' => 3
            ),
            'abbotkinneyblvd' => array(
                'name' => 'Abbot Kinney',
                'nameShort' => 'Abbot Kinney Blvd.',
                'nameLong' => 'Abbot Kinney Boulevard',
                'database' => 'abbotkinney',
                'pageTitle' => "NowArena.com : Latest social media about what's going down and coming up on Abbot Kinney Blvd.",
                'baseUrl' => 'http://abbotkinneyblvd.' . self::$domain,
                'description' => "Food, fashion, cocktails, pop culture, street art from the denizens and curators of the premiere boulevard in Venice Beach, California.",
                'twitterScreenName' => 'abbotkinneybl',
                'categoryDepth' => 2
            )
        );
        
    }
    
    public static function getSubdomainArr($subdomain = '') 
    {
                
        $subdomain = ($subdomain =='' ) ? self::$subdomain : $subdomain;
        
        $subdomainData = self::getSubdomainData();

        return $subdomainData[$subdomain];
        
    }
    /*
     * db on prod have 'nowarena' prefix
     */
    public static function getDatabase() 
    {
        $db = self::$subdomainArr['database'];
        if (env('APP_ENV') != 'local') {
            $db = 'nowarena' . $db;
        }
        return $db;
        
        
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
    
    public static function getCategoryDepth()
    {
        return self::$subdomainArr['categoryDepth'];
    }
    
    private function __construct() {}
    private function __clone() {}
    
}
