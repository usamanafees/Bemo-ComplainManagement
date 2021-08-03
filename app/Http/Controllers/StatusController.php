<?php

namespace App\Http\Controllers;
use App\Status;
use Illuminate\Http\Request;
use Log;

class StatusController extends Controller
{

    public function createStatus(Request $request)
    {
        $request->user()->statuses()->create(
            [
                'title' => $request->title,
                'slug' => $request->title,
                'order' => $request->order,
            ]);
            return $request->user()->statuses()->with('tasks')->get();

    }

    public function removeStatus(Request $request)
    {
        if(isset($request->id) && !empty($request->id)) {
            $sta = Status::find($request->id)->delete();
            if($sta)
            {
                return $request->user()->statuses()->with('tasks')->get();
            }else
            {
                return response()->json(['success' => 'false', 'id' => $request->id]);
            }
           
        }
    }
}
