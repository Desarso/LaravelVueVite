<?php

namespace App\Http\Controllers;
use App\Repositories\CouponSheetRepository;
use Illuminate\Http\Request;
use App\Models\CouponSheetHeader;
use App\Exports\CouponsExport;
use Maatwebsite\Excel\Facades\Excel;

class CouponSheetController extends Controller
{
    protected $couponSheetRepository;

    public function __construct()
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->couponSheetRepository = new CouponSheetRepository;
    }

    public function index()
    {
        $activeHeader = CouponSheetHeader::where('status', 'OPEN')->first();

        return view('pages.couponSheet.index', [ 'pageConfigs' => ['pageHeader' => true], 'activeHeader' => $activeHeader]);
    }

    public function getData(Request $request) 
    {
        return $this->couponSheetRepository->getData($request);
    }

    public function create(Request $request) 
    {
        return $this->couponSheetRepository->create($request);
    }

    public function delete(Request $request) 
    {
        return $this->couponSheetRepository->delete($request);
    }

    public function update(Request $request) 
    {
        return $this->couponSheetRepository->update($request);
    }

    public function close(Request $request) 
    {
        return $this->couponSheetRepository->close($request);
    }

    public function sendFiles(Request $request) 
    {
        return $this->couponSheetRepository->sendFiles($request);
    }

    public function getDetail(Request $request) 
    {
        return $this->couponSheetRepository->getDetail($request);
    }

    public function getNext(Request $request) 
    {
        return $this->couponSheetRepository->getNext($request);
    }

    public function getDataScannedCoupons(Request $request) 
    {
        return $this->couponSheetRepository->getDataScannedCoupons($request);
    }

    public function exportCouponsToExcel(Request $request) 
    {
        $myFile = Excel::raw(new CouponsExport($request), \Maatwebsite\Excel\Excel::XLSX);

        $response = array(
            'name' => "Cupones", //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
         );
         
        return response()->json($response);
    }
}
