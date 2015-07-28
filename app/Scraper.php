<?php
namespace app;

use App\ModelNA;
/**
 * Description of Scraper
 *
 * @author matt
 */
class Scraper extends ModelNA {
    
    public function scrapeTeam($memberObj, $keyword)
    {
        $r = $this->scrapeGoogleResult($memberObj, $keyword);
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
    
    protected function scrapeGoogleResult($memberObj, $keyword)
    {
        
        $name = urlencode($memberObj->name . " " . $keyword);
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
