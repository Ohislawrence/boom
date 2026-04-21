<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function about()
    {
        return view('frontend.pages.about');
    }

    public function howItWorks()
    {
        return view('frontend.pages.how-it-works');
    }

    public function editorialPolicy()
    {
        return view('frontend.pages.editorial-policy');
    }

    public function privacyNotice()
    {
        return view('frontend.pages.privacy-notice');
    }

    public function responsibleGambling()
    {
        return view('frontend.pages.responsible-gambling');
    }
}
