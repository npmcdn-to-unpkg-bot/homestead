<?php
namespace App;

use App\ModelNA;
/**
 * Description of Scraper
 *
 * @author matt
 */

// TODO use factory to generate and set local values, eg. getTeamFromWiki regex
class Scraper extends ModelNA {
    
    public function __construct($keyword) 
    {
        $this->keyword = $keyword;
    }
    
    public function scrapeTeam($memberObj)
    {
        $r = $this->scrapeGoogleResult($memberObj, $this->keyword);
        if ($wikiPageUrl = $this->getWikipageFromGoogleResult($r)) {
            $r = $this->scrapeWikipage($wikiPageUrl);
            return $this->getTeamFromWiki($r);
        }
    }
     
    protected function getTeamFromWiki($page) 
    {
        preg_match('~(plays|playing) for the <a[^>]+>([^<]+)<~is', $page, $arr);
        return isset($arr[2]) ? $arr[2] : false;
    }
    
    protected function scrapeWikipage($url)
    {
        //echo "<br><br>".$url;
        $r = $this->callCurl($url);
        return $r;
    }
    
    protected function scrapeGoogleResult($memberObj)
    {
        
        $name = urlencode($memberObj->name . " " . $this->keyword);
        $url = "https://www.google.com/search?q=" . $name . "&ie=utf-8&oe=utf-8";
        //echo "<br><br>".$url;
        $r = $this->callCurl($url);
        return $r;
        
    }

    public function getWikipageFromGoogleResult($r)
    {
       
        //echo htmlspecialchars($r)."<hr>";
        preg_match_all('~https://en.wikipedia.org/wiki/[^&]+~is', $r, $arr);
        if (isset($arr[0][0])) {
            return $arr[0][0];
        } else {
            return false;
        }
    }
    
    public function getGoogleInstagram($r)
    {
        
    }
    
    public function getGoogleTwitter($r)
    {
        
    }
    
    public function getGoogleFacebook($r)
    {
        
    }
}
