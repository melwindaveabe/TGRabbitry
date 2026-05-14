<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CLog;
use Illuminate\Support\Facades\DB;
use App\Services\Image;

class SettingController
{

    public function updateDeal(Request $req){
        DB::table('settings')->update([
            'deal_desc' => $req['desc'],
            'deal_date_until' => $req['date']
        ]);

        CLog::create('Updated', 'Deal');
    }

    public function updateNumber(Request $req){
        $data = [
            'number' => $req['number'],
        ];

        $image = $req->file('image');
        if ($image){
            $data['gcash_qr'] = Image::proccessDefName($image, 'images/website', DB::table('settings')->first()?->gcash_qr);
        } 

        DB::table('settings')->update($data);

        CLog::create('Updated', 'Number');
    }

    public function updateQuote(Request $req){
        $req->validate([
            'quote_title' => 'required',
            'quote_body' => 'required',
        ]);
        $data = [
            'quote_title' => $req['quote_title'],
            'quote_body' => $req['quote_body'],
        ];

        $image = $req->file('quote_image');
        if ($image){
            Image::remove('images/website', DB::table('settings')->first()?->quote_image);
            $data['quote_image'] = Image::proccessDefName($image, 'images/website');
        } 

        DB::table('settings')->update($data);

        CLog::create('Updated', 'Blog Quote');
    }

    public function updateSettings(Request $req){
        $req->validate([
            'name' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        $data = [
            'name' => $req['name'],
        ];

        $image = $req->file('image');
        if ($image){
            $data['logo'] = Image::proccessDefName($image, 'images/website', DB::table('settings')->first()?->logo);
        } 

        DB::table('settings')->update($data);

        CLog::create('Updated', 'Settings');
    }

}
