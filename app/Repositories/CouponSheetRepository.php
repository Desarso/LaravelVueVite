<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\CouponSheetHeader;
use App\Models\CouponSheet;
use App\Models\CouponSheetDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CouponSheetImport;
use Carbon\Carbon;

class CouponSheetRepository
{
    protected $localTimezone;

    public function __construct()
    {
        $this->localTimezone = env('LOCAL_TIMEZONE', 'Europe/Madrid');
    }

    public function getData($request)
    {
        $idheader = $this->getCouponSheetHeaderActive();

        return CouponSheet::where('idheader', $idheader)->withCount('coupons')->withCount('escannedCoupons')->orderBy('barcode', 'desc')->get();
    }

    public function create($request)
    {
        $validator = Validator::make($request->all(), [
            'initial_code' => 'required|unique:wh_coupon_sheet_header|min:15,max:15,',
        ]);

        $request['created_by'] = Auth::id();
        $request['startdate']  = Carbon::now($this->localTimezone);

        if($validator->passes())
        {
            CouponSheetHeader::create($request->all());
            
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'errors' => $validator->errors()]);
    }

    public function update($request)
    {
        $validator = Validator::make($request->all(), [
            'barcode' => 'required|unique:wh_coupon_sheet,barcode,' . $request->id,
        ]);

        if($validator->passes())
        {
            $couponSheet = CouponSheet::find($request->id);

            $couponSheet->fill($request->all())->save();
            
            return response()->json(['success' => true]);
        }
     
        return response()->json(['success' => false]);
    }

    public function delete($request)
    {
        $model = CouponSheet::findOrFail($request->id);
        $model->delete();

        return response()->json(['success' => true, 'model' => $model]);
    }

    public function close($request)
    {
        $activeCouponSheet = CouponSheetHeader::where('status', 'OPEN')->first();

        $activeCouponSheet->status     = "CLOSE";
        $activeCouponSheet->finishdate = Carbon::now($this->localTimezone);
        $activeCouponSheet->closed_by  = Auth::id();
        
        $activeCouponSheet->save();

        return response()->json(['success' => true, 'model' => $activeCouponSheet]);
    }

    public function getCouponSheetHeaderActive()
    {
        $activeCouponSheet = CouponSheetHeader::where('status', 'OPEN')->first();

        return is_null($activeCouponSheet) ? null : $activeCouponSheet->id;
    }

    public function sendFiles($request)
    {
        $idheader = $this->getCouponSheetHeaderActive();

        $files = request()->file('files');

        $start = $request->barcode;

        
        if(!$this->validateBarcodeFormat($start)) {
            return response()->json(['success' => false, 'message' => 'El código de la hoja tiene un formato invalido'], 401);
        }

        $countFiles = count($files);
        if($this->validateBarcodeRange($start, $countFiles)) return response()->json(['success' => false, 'message' => 'Verique el rango de códigos, código duplicado'], 401);

        $letters = preg_replace("/[0-9]/", "", $start);
        $start = str_replace($letters, "", $start);

        foreach ($files as $file)
        {
            $barcode = $letters . $start;
            $this->importExcel($file, $barcode, $idheader);
            $start ++;
        }
    }

    public function importExcel($file, $barcode, $idheader)
    {
        $validator = Validator::make(
            ['file' => $file,      'extension' => strtolower($file->getClientOriginalExtension())],
            ['file' => 'required', 'extension' => 'required|in:csv,xlsx,xls']
        );

        if($validator->passes())
        {
            $couponSheetImport = new CouponSheetImport($barcode, $idheader);

            Excel::import($couponSheetImport, $file);

            return response()->json(['success' => true, 'message' => '']);
        }
        else
        {
            return response()->json(['success' => false, 'message' => '']);
        }
    }

    public function getDetail($request)
    {
        $couponSheet = CouponSheet::with('coupons')->find($request->id);

        return view('pages.couponSheet.coupons', ["couponSheet" => $couponSheet]);
    }

    public function getNext()
    {
        $activeCouponSheet = CouponSheetHeader::withCount('sheets')->where('status', 'OPEN')->first();

        if(!is_null($activeCouponSheet))
        {
            if($activeCouponSheet->sheets_count == 0) return $activeCouponSheet->initial_code;

            $lastCouponSheet = CouponSheet::where('idheader', $activeCouponSheet->id)->orderBy('barcode', 'desc')->first();

            if (is_null($lastCouponSheet)) {

                return null;
            } else {
                $barcode =  $lastCouponSheet->barcode;

                $letters = preg_replace("/[0-9]/", "", $barcode);
                $barcode = str_replace($letters, "", $barcode);

                $barcode++;

                return $letters . $barcode;
            }
        }

        return null;
    }

    public function scanCoupon($request)
    {
        $success = true;
        $msg = '';
        $idcoupon = null;

        if (strlen($request->barcode) < 6) {
            return response()->json([
                'success' => false, 
                'msg' => "Código invalido"
            ]);
        }

        $barcode = substr($request->barcode, -7, 6);
        
        $details = DB::table('wh_coupon_sheet_detail as d')
                ->selectRaw('s.barcode, d.position, d.id')
                ->join('wh_coupon_sheet as s', 'd.idsheet', '=', 's.id')
                ->join('wh_coupon_sheet_header as h', 's.idheader', '=', 'h.id')
                ->where('h.status', 'OPEN')
                ->whereNull('d.scandate')
                ->where('d.barcode', $barcode)
                ->orderBy('s.barcode')
                ->get();

        if($details->count() == 0) { 
            $msg = 'No existe';
            $success = false;
        } else {
            $idcoupon = $details->first()->id;
            $sheet = $details->first()->barcode;
            $position = $details->first()->position;
            $msg = 'Hoja ' . $sheet .', casilla '.$position;
        }


        return response()->json([
            'success' => $success, 
            'msg' => $msg,
            'idcoupon' => $idcoupon
        ]);
    }

    public function markCouponToReady($request)
    {
        $success = false;
        $coupon = CouponSheetDetail::find($request->idcoupon);

        if ($coupon) {
            $coupon->scandate = Carbon::now();
            $coupon->save();
            $success = true;
        }

        return response()->json([
            'success' => $success, 
            'idcoupon' => $coupon->id, 
        ]);
    }

    public function getCouponDeficit($request)
    {
        return DB::table('wh_coupon_sheet_detail as d')
                    ->selectRaw('count(d.barcode) as count, d.barcode, d.description')
                    ->join('wh_coupon_sheet as s', 'd.idsheet', '=', 's.id')
                    ->join('wh_coupon_sheet_header as h', 's.idheader', '=', 'h.id')
                    ->where('h.status', 'OPEN')
                    ->whereNull('d.scandate')
                    ->groupBy('d.barcode', 'd.description')
                    ->get();
    }

    public function getDataScannedCoupons($request)
    {
        $active = DB::table('wh_coupon_sheet_header')->where('status', 'OPEN')->first();

        $data = DB::table('wh_coupon_sheet as cs')
                  ->join('wh_coupon_sheet_detail as csd', 'csd.idsheet', '=', 'cs.id')
                  ->where('cs.idheader', $active->id)
                  ->select('idsheet', 'scandate', DB::raw(' DATE_FORMAT(csd.scandate, "%d %b") as scandate'))
                  ->orderBy('scandate', 'asc')
                  ->get();

        $groups = $data->groupBy('scandate');

        $labels = $groups->keys()->toArray();

        $scannedCoupons  = [];
        $totalCoupons    = [];

        foreach($groups as $group)
        {
            array_push($totalCoupons, $group->count());
            array_push($scannedCoupons, $group->whereNotNull('scandate')->count());
        }

        $series = [ ["name" => "Escaneados", "data" => $scannedCoupons] ];

        return ["series" => $series, "series_total" => $totalCoupons, "labels" => $labels];
    }

    private function validateBarcodeRange($start, $countFiles)
    {
        $letters = preg_replace("/[0-9]/", "", $start);
        $start = str_replace($letters, "", $start);

        $newBarcodes = array();

        for ($i= 0; $i < $countFiles ; $i++) { 

            $barcode = $letters . $start;
            array_push($newBarcodes, $barcode);
            $start++;
        }
        
        $validRange = CouponSheet::whereIN('barcode', $newBarcodes)->count();

        return $validRange > 0;
    }

    private function validateBarcodeFormat($barcode)
    {
        if(strlen($barcode) < 15 || strlen($barcode) > 20) return false;

        $hasLetters = preg_replace("/[0-9]/", "", $barcode);

        if (strlen($hasLetters) == 2) {

            $barcode = substr($barcode, 0, 2);
            if ($barcode != "MC" && $barcode != "SF") return false;
        }

        return true;
    }

}