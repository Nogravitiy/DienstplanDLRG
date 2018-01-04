<?php

namespace App\Mail;

use App\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ServicesList extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tableheader;
    public $services;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tableheader = \App\Qualification::where('isservicedefault', true)->get();
        //get all services of next 2 month
        $this->services = Service::where([['date','>=', DB::raw('CURDATE()')], ['date', '<=', \Carbon\Carbon::today()->addMonth(2)]])->orderBy('date')->with('positions.qualification')->get();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Wachplan MES')->view('email.serviceslist')->with([
            'tableheader' => $this->tableheader,
            'services' => $this->services,
        ]);
    }
}
