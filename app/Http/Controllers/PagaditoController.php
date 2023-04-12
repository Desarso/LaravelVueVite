<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\LogRepository;
use App\Libraries\Pagadito;

class PagaditoController extends Controller
{
    protected $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function index()
    {
        return view('pagadito.index');
    }

    public function payment(Request $request)
    {
        $uid = env('PAGADITO_UID');
        $wsk = env('PAGADITO_WSK');
        $sandBox = env('PAGADITO_SANDBOX');

        $Pagadito = new Pagadito($uid, $wsk);

    
        if ($sandBox) {
            $Pagadito->mode_sandbox_on();
        }

        if ($Pagadito->connect()) {
        
            $Pagadito->add_detail('1', "prueba de pago", $request->cost);
            $ern = uniqid();

            if (!$Pagadito->exec_trans($ern)) {
                
                switch($Pagadito->get_rs_code())
                {
                    case "PG2001":
                        /*Incomplete data*/
                    case "PG3002":
                        /*Error*/
                    case "PG3003":
                        /*Unregistered transaction*/
                    case "PG3004":
                        /*Match error*/
                    case "PG3005":
                        /*Disabled connection*/
                    default:
                        dd("$Pagadito->get_rs_code().....$Pagadito->get_rs_message()");
                        break;
                }
            }
        }
    }

    
    public function successPayment(Request $request)
    {
        $uid = env('PAGADITO_UID');
        $wsk = env('PAGADITO_WSK');
        $sandBox = env('PAGADITO_SANDBOX');

        $Pagadito = new Pagadito($uid, $wsk);

    
        if ($sandBox) {
            $Pagadito->mode_sandbox_on();
        }

        if ($Pagadito->connect()) {
            
            $token = $request->token;
            $pedido = $request->comprobante;

            if ($Pagadito->get_status($token)) {

                $estado = $Pagadito->get_rs_status();

                if ($estado == "COMPLETED") {
                    $numero_aprobacion_pg = $Pagadito->get_rs_reference();
                    $fecha_cobro = $Pagadito->get_rs_date_trans();
                } else {
        
                }
            } else {
                //Si hubo un error, se debe controlar aquí.
            }
        }

        // dd($numero_aprobacion_pg);
        return view('pagadito.succesPayment', [
            "data" => $request->all()
        ]);
    }
    
}
