<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\InstagramAdapter;
use App\MemberSocial;
use App\SocialMedia;
use App\Site;

class InstagramController extends Controller
{
    
    public function __construct()
    {
        $keyword = Site::getInstance()->getSubdomain();
        $instagramScreenName = Site::getInstance()->getInstagramScreenName();
        $instagramAccessToken = Site::getInstance()->getInstagramAccessToken();

        $this->socialMediaObj = new SocialMedia($keyword, 'instagram');
        $this->instagramAdapter = new InstagramAdapter($instagramScreenName, $instagramAccessToken, $this->socialMediaObj);
    }
    
    public function addStatus()
    {
        
        if (($socialMediaArr = $this->instagramAdapter->addStatus()) !== false) {
            $this->socialMediaObj->addSocialMedia($socialMediaArr);
        }
        printR($socialMediaArr);
        exit('done');
    }
    
    public function addFriends()
    {
    
        $socialSite = 'instagram';
        $notOnSiteArr = $this->instagramAdapter->parseMembers();

        return view('admin.notonsite', compact('notOnSiteArr', 'socialSite'));
        
    }
  

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
    
}
