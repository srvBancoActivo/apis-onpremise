<?php

namespace App\Traits;


use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use SoapClient;

trait ApiCallBancoActivo
{
    /**
     * Obtain the data of the originator of the transaction.
     *
     * @param $cuenta
     * @return SimpleXMLElement
     */
    public function consultaMovimientos($cuenta,$tipo)
    {
        $wsdl = 'http://'. env('IP_SERVICIO') .'/GatewayBancoActivoM/services/Core?wsdl';

        $paramst =  [
            'canal'  => "0",
            'RSTACC' => $cuenta,
            'RSTCAF' => $tipo,
            'RSTFAM' => '0',
            'RSTFBL' => '0',
            'RSTFCK' => '0',
            'RSTFD1' => '0',
            'RSTFD2' => '0',
            'RSTFD3' => '0',
            'RSTFRC' => '0',
            'RSTTAM' => '0',
            'RSTTCK' => '0',
            'RSTTD1' => '0',
            'RSTTD2' => '0',
            'RSTTD3' => '0',
            'RSTUSR' => '0'
        ];

        $req = [ 'req' => $paramst ];
//dd($req);
        $client = new SoapClient($wsdl);
        //$Response = $client->DoRemoteFunction($Data); 
        //echo "REQUEST:\n" . htmlentities($client->__getLastRequest()) . "\n";
        //dd($client->__getFunctions());
        //$result = $client->STMRDSJV($req);
        //$movimientos = (array)$client->STMRDSJV($req);
        $movimientos = $client->STMRDSJV($req);
        $array = json_decode(json_encode($movimientos), true);
        //$mov = simplexml_load_string($movimientos);
        dd($array);
        //dd($movimientos->STMRDSJVReturn->STMSDSJV->STMSDSJV->STMACC);
        $mov = $movimientos->STMRDSJVReturn;
        //dd($mov);
        return $mov;
    }

    public function transferenciasInternas($request)
    {
        $wsdl = 'http://'. env('IP_SERVICIO') .'/GatewayBancoActivoM/services/Core?wsdl';

        $paramst =  [
            'canal'  => "09",
            'INTDESC' => $request->descripcion,
            'INTFRMACC' => $request->cuenta_de,
            'INTIPACT' => '',
            'INTREF' => '',
            'INTTOACC' => $request->cuenta_para,
            'INTTRFAMT' => $request->monto,
            'INTTYPE' => '0',
            'INTUSR' => 'usuario',
            'concepto' => '',
            'descTipoOperacion' => '',
            'descTipoTransaccion' => '',
            'emailBeneficiario' => '',
            'id_usuario' => '',
            'nombreBeneficiario' => '',
            'operacion' => ''
        ];

        $req = [ 'req' => $paramst ];
        $client = new SoapClient($wsdl);
        $transferencia = $client->INTRFDSJV($req);
        $array = json_decode(json_encode($transferencia), true);

        $referencia1 = trim($array['INTRFDSJVReturn']['error']['ERDF01']);
        $referencia2 = trim($array['INTRFDSJVReturn']['error']['ERDF02']);
        $referencia3 = trim($array['INTRFDSJVReturn']['error']['ERDF03']);
        $referencia4 = trim($array['INTRFDSJVReturn']['error']['ERDF04']);
        $referencia5 = trim($array['INTRFDSJVReturn']['error']['ERDF05']);
        $referencia6 = trim($array['INTRFDSJVReturn']['error']['ERDF06']);
        $referencia7 = trim($array['INTRFDSJVReturn']['error']['ERDF07']);
        $referencia8 = trim($array['INTRFDSJVReturn']['error']['ERDF08']);
        $referencia9 = trim($array['INTRFDSJVReturn']['error']['ERDF09']);
        $referencia10 = trim($array['INTRFDSJVReturn']['error']['ERDF10']);

        $cod_error = trim($array['INTRFDSJVReturn']['error']['ERDS01']);

        $respuesta['error'] = $cod_error;
        $respuesta['referencia'] = $referencia1;

        return $respuesta;
    }

    public function mesaDeCambio($request)
    {
        $wsdl = 'http://'. env('IP_SERVICIO') .'/GatewayBancoActivoM/services/Core?wsdl';

        //dd($request->cuenta_debitar);

        $paramst =  [
            'canal'  => "",
            'INVCCDRE' => '',
            'INVCCEDU' => '',
            'INVCCTAC' => '',
            'INVCCTAD' => $request->cuenta_acreditar,
            'INVCCTAO' => $request->cuenta_debitar,
            'INVCDDTR' => '',
            'INVCDOTR' => '',
            'INVCMTOC' => $request->comision,
            'INVCMTOD' => $request->monto_divisa,
            'INVCMTOO' => $request->total_pagar,
            'INVCNACI' => '',
            'INVCPROC' => $request->tipo,
            'INVCREFE' => '',
            'INVCTBCV' => '',
            'INVCTCLI' => $request->tasa,
            'INVCTIPO' => 'VB'
        ];

        $req = [ 'req' => $paramst ];
        //dd($req); 
        $client = new SoapClient($wsdl);
        $mesa = $client->NIBVCDI01($req);
        $array = json_decode(json_encode($mesa), true);
        //dd($array);

        if(isset($array['NIBVCDI01Return']['error']['ERDS01'])){
            $respuesta['cod_error_1'] = $cod_error1 = trim($array['NIBVCDI01Return']['error']['ERDS01']);           
        }

        if(isset($array['NIBVCDI01Return']['error']['ERDS01'])){
            $respuesta['cod_error_2'] = $cod_error2 = trim($array['NIBVCDI01Return']['error']['ERDS02']);
        }        
        
        if(isset($cod_error1) == "" && isset($cod_error1) == ""){

            //ejecucion

            $paramst1 =  [
                'canal'  => "",
                'INVCCDRE' => '',
                'INVCCEDU' => '',
                'INVCCTAC' => '',
                'INVCCTAD' => $request->cuenta_acreditar,
                'INVCCTAO' => $request->cuenta_debitar,
                'INVCDDTR' => '',
                'INVCDOTR' => '',
                'INVCMTOC' => '',
                'INVCMTOD' => $request->monto_divisa,
                'INVCMTOO' => $request->total_pagar,
                'INVCNACI' => '',
                'INVCPROC' => $request->tipo,
                'INVCREFE' => trim($array['NIBVCDI01Return']['NIBVCDI01']['INVCREFE']),
                'INVCTBCV' => '',
                'INVCTCLI' => '',
                'INVCTIPO' => 'PC'
            ];

            $req1 = [ 'req' => $paramst1 ];
            //dd($req); 
            $client = new SoapClient($wsdl);
            $mesa1 = $client->NIBVCDI01($req1);
            $array1 = json_decode(json_encode($mesa1), true);


            $respuesta['cuenta_debitar'] = $cuenta_debitar = trim($array1['NIBVCDI01Return']['NIBVCDI01']['INVCCTAO']);
            $respuesta['cuenta_acreditar'] = $cuenta_acreditar = trim($array1['NIBVCDI01Return']['NIBVCDI01']['INVCCTAD']);
            $respuesta['monto_divisa'] = $monto_divisa = trim($array1['NIBVCDI01Return']['NIBVCDI01']['INVCMTOD']);
            $respuesta['tasa'] = $tasa = trim($array1['NIBVCDI01Return']['NIBVCDI01']['INVCTCLI']);
            $respuesta['total_pagar'] = $total_pagar = trim($array1['NIBVCDI01Return']['NIBVCDI01']['INVCMTOO']);
            $respuesta['comision'] = $comision = trim($array1['NIBVCDI01Return']['NIBVCDI01']['INVCMTOC']);
            $respuesta['cod_trans_dest'] = $cod_trans_dest = trim($array1['NIBVCDI01Return']['NIBVCDI01']['INVCDDTR']);
            $respuesta['cod_trans_orig'] = $cod_trans_orig = trim($array1['NIBVCDI01Return']['NIBVCDI01']['INVCDOTR']);
            $respuesta['tipo'] = $tipo = trim($array1['NIBVCDI01Return']['NIBVCDI01']['INVCPROC']);
            $respuesta['referencia'] = $referencia = trim($array1['NIBVCDI01Return']['NIBVCDI01']['INVCREFE']);
            $respuesta['INVCTBCV'] = $INVCTBCV = trim($array1['NIBVCDI01Return']['NIBVCDI01']['INVCTCLI']);
            $respuesta['ejecucion'] = $ejecucion = trim($array1['NIBVCDI01Return']['NIBVCDI01']['INVCTIPO']);

           // dd($cuenta_acreditar);
        }

//comentario

 //dd($respuesta);
        //$respuesta['error'] = $cod_error;
        //$respuesta['referencia'] = $referencia1;

        return $respuesta;
    }
}