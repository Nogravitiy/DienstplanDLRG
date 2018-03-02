<?php

namespace App\Http\Controllers;

use App\Position;
use App\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $positions_user_past = Position::where('user_id', '=', Auth::user()->id)->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))->count();
        $positions_total_past = Position::where('user_id', '!=', null)->join('services', 'positions.service_id', '=', 'services.id')
                            ->where('services.date', '<', DB::raw('CURDATE()'))->count();
        $positions_free = Position::where('user_id', '=', null)->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '>=', DB::raw('CURDATE()'))->count();
        $top_users = Position::where('user_id', '!=', null)->with('user')->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))->selectRaw('user_id, count(*) as aggregate')->groupBy('user_id')->limit(10)->get();

        return view('home.index', compact('positions_user_past', 'positions_total_past', 'positions_free', 'top_users'));
    }

    public function mailtest(){
        $tableheader = \App\Qualification::where('isservicedefault', true)->get();
        $services = Service::where([['date','>=', DB::raw('CURDATE()')], ['date', '<=', \Carbon\Carbon::today()->addMonth(2)]])->orderBy('date')->with('positions.qualification')->get();

        return view('email.serviceslist', compact('tableheader', 'services'));
    }
}
