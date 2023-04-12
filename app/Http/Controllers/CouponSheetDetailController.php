<?php

namespace App\Http\Controllers;
use App\Repositories\CouponSheetRepository;
use Illuminate\Http\Request;


class CouponSheetDetailController extends Controller
{

    protected $couponSheetRepository;

    public function __construct()
    {
        $this->couponSheetRepository = new CouponSheetRepository;
    }

    public function scanCoupon(Request $request) 
    {
        return $this->couponSheetRepository->scanCoupon($request);
    }

    public function markCouponToReady(Request $request) 
    {
        return $this->couponSheetRepository->markCouponToReady($request);
    }

    public function getCouponDeficit(Request $request) 
    {
        return $this->couponSheetRepository->getCouponDeficit($request);
    }
}
