<?php

namespace App\Repositories;
use App\Models\Booking;
use App\Models\BookingSet;
use App\Models\Spot;
use App\Models\SpotType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;




class ImpalaRepository
{
	public function loadSpotTypes()
	{

		$http = new \GuzzleHttp\Client;

		$url = 'https://api.getimpala.com/v2/hotel/' . env('IMPALA_HOTEL_ID') . '/area-types/';

		$response = $http->get($url,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . env('IMPALA_API_KEY') ,
					'Content-type'  => 'application/json'
				]
			]);

		$response = json_decode($response->getBody());

		foreach($response->data as $item)
		{
			$spotType = SpotType::updateOrCreate(
				['idimpala' => $item->id ],
				['name' => $item->name, 'description' => $item->description, 'code' => $item->code, ]


			);
			
		}
		
	}


	public function loadSpots()
    {
		$http = new \GuzzleHttp\Client;

		$url = 'https://api.getimpala.com/v2/hotel/' . env('IMPALA_HOTEL_ID') . '/areas/';

		$response = $http->get($url,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . env('IMPALA_API_KEY') ,
					'Content-type'  => 'application/json'
				]
			]);

		$response = json_decode($response->getBody());

		foreach($response->data as $item)
		{
			$urlid = DB::table('wh_spot_type')->where('idimpala', $item->areaTypeId)->first('id');
			$spot = Spot::updateOrCreate(
				['idimpala' => $item->id],
				['name' => $item->name, 'idtype' => $urlid->id, 'status' => $item->status]

			);
		}
	}

	public function loadBooking()
	{
		$http = new \GuzzleHttp\Client;

		$url = 'https://api.getimpala.com/v2/hotel/' . env('IMPALA_HOTEL_ID') . '/bookings/';

		$response = $http->get($url,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . env('IMPALA_API_KEY') ,
					'Content-type'  => 'application/json'
				]
			]);

		$response = json_decode($response->getBody());


		foreach($response->data as $item)
		{
			$urlspot = DB::table('wh_spot')->where('idimpala', $item->areaId)->first('id');

			$urltype = DB::table('wh_spot_type')->where('idimpala', $item->requestedAreaTypeId)->first('id');

			$urlbookingset = DB::table('wh_booking_set')->where('idimpala', $item->bookingSetId)->first('id');

			$booking = Booking::updateOrCreate(
				['idimpala' => $item->id],
				['status' => $item->status, 'idbookingset' => $urlbookingset->id,
				'startdate' => Carbon::parse($item->start),
				'enddate' => Carbon::parse($item->end), 'idspot' => $urlspot->id,
				'idtype' => $urltype->id , 'adultcount' => $item->adultCount, 
				'childcount' => $item->childCount, 'infantcount' => $item->infantCount
			]

		);
		}
		
	}

	public function loadBookingSet()
	{
		$http = new \GuzzleHttp\Client;

		$url = 'https://api.getimpala.com/v2/hotel/' . env('IMPALA_HOTEL_ID') . '/booking-sets/';

		$response = $http->get($url,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . env('IMPALA_API_KEY') ,
					'Content-type'  => 'application/json'
				]
			]);

		$response = json_decode($response->getBody());

		foreach($response->data as $item)
		{
			
			$bookingset = BookingSet::updateOrCreate(
				['idimpala' => $item->id],
				['startdate' => Carbon::parse($item->start), 
				'enddate' => Carbon::parse($item->end), 'bookingIds' => json_encode($item->bookingIds), 'contact'=> json_encode($item->contact)]

			);
		}
	}
}
