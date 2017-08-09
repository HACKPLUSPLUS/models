<?php

namespace App\Http\Controllers;

use App\Cruise;
use App\Price;
use App\Broker;
use App\CabinCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CruiseController extends Controller
{
    /**
     * Show the profile for the given cruise.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $brokerId = 2;
        $shipId = 192;
        $portId = 13;
        $destinationId = 9;
        $duration = 7;
        $navigationalRoute = '13,9,157,461,125,105,2,13';
        $now = \Carbon\Carbon::now();
        
        $routeDates = DB::table('cruises')
                ->where([['broker_id', '=', $brokerId], ['ship_id', '=', $shipId], ['haven_id', '=', $portId], ['bestemming_id', '=', $destinationId], ['duur', '=', $duration], ['vaarroute', '=', $navigationalRoute], ['begindatum', '>', $now], ['published', '=', 'Y']])
                ->orderBy('begindatum', 'asc')
                ->get();
        
        /*SELECT id, begindatum FROM cruises
            WHERE `cruises`.`broker_id`=:brokerId
            AND `cruises`.`ship_id`=:shipId
            AND `cruises`.`port_id`=:portId
            AND `cruises`.`bestemming_id`=:destinationId
            AND `cruises`.`duration`=:duration
            AND `cruises`.`vaarroute`=:navigationalRoute
            AND `cruises`.`begindatum`>NOW()
            AND `cruises`.`published`='Y'
            ORDER BY `begindatum` ASC*/
        var_dump($routeDates);
        die;
        $prices = DB::table('prices')
                ->join('cabin_codes', 'cabin_codes.id', '=', 'prices.hut_id')
                ->select(
                        'prices.prijs as prijs',
                        'prices.singleprijs as singleprijs',
                        'prices.kindprijs as kindprijs',
                        'prices.babyprijs as babyprijs',
                        'prices.derdeprijs as derdeprijs',
                        'prices.cruise_id as cruise_id',
                        'prices.brochure as brochure',
                        'prices.brochure_auto as brochure_auto',
                        'prices.prijs_manual as prijs_manual',
                        'prices.rate as rate',
                        'cabin_codes.groep as groep'
                        )
                ->where([['prices.cruise_id', '=', 161051], ['cabin_codes.schip_id', '=', 192], ['groep', '!=', ''], ['cabin_codes.naam', '!=', '(BAK)'], ['cabin_codes.min_bezetting', '>=', 2], ['prijs', '!=', '0.00']])
                ->orWhere('prices.prijs_manual', '!=', '0.00')
                ->orderBy('prijs', 'asc')
                ->groupBy('groep')
                ->get();
        echo '<pre>';
        //AND (`prices`.`prijs`!=0.00 OR `prices`.`prijs_manual`!=0.00)
        //AND `cabin_codes`.`naam`!='(BAK)'
        //AND `cabin_codes`.`min_bezetting`>=2
        var_dump(count($prices));
        foreach($prices as $price) {
            //echo $cruise->id . '<br />';
            var_dump($price);
        }
        die;
        $cabinCodes = DB::table('cabin_codes')
                ->get();
        echo '<pre>';
        foreach($cabinCodes as $cabinCode) {
            //echo $cruise->id . '<br />';
            var_dump($cabinCode);
        }
        die;
        $cruises = DB::table('cruises')
                ->join('brokers', 'cruises.broker_id', '=', 'brokers.id')
                ->select('cruises.id', 'brokers.id AS uid')
                ->get();
        echo '<pre>';
        foreach($cruises as $cruise) {
            //echo $cruise->id . '<br />';
            var_dump($cruise);
        }
        
        //return view('cruise.show', ['cruise' => Cruise::findOrFail($id), 'prices' => Price::all(), 'brokers' => Broker::all()]);
    }
}