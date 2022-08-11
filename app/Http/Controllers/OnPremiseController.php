<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiCallBancoActivo;
use App\Traits\ApiResponser;

class OnPremiseController extends ApiController
{
    use ApiCallBancoActivo;

    public function __construct()
    {
        $this->middleware('client.credentials')->only(['index','show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return "Bienvenido a las APIS On Premise";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function consulta_movimientos(Request $request)
    {
    	//$json = $this->consultaMovimientos('8000193919');
    	$json = $this->consultaMovimientos($request->cuenta, $request->tipo);
    	
    	//return $this->showAll(collect($json),200);
    	dd($json);
    	return $json;
    	//dd($json);

        /*$cadena = "";
        $msg = $this->consultaBancos($request->tipo_pagador);
        $respuesta = $this->bam_PRUEBACLIENTE($msg);
        $msj = $respuesta["msg_output"];

        $ret_code = substr("$msj", 10, 4);
        $cantBanco = (int) substr("$msj", 14, 3);

        if($ret_code == "0000"){

            $i=0;
            $a = 17;
            $b = 21;
            $const = 44;


            $cadena = [
                'cantBanco' => $cantBanco,
                'bancos' => [],
            ];

            for($i=0;$i < $cantBanco; $i++){

                $codBanco = substr("$msj", $a, 4);
                $nombreBanco = substr("$msj", $b, 40);

                $a = $a + $const;
                $b = $b + $const;

                $cadena['bancos'][] = [
                    'codBanco' => $codBanco,
                    'nombreBanco' => trim($nombreBanco),
                ];
            }
        }else{

            if(isset($respuesta['error'])){
                if($respuesta['error_code'] == "0000"){
                    $errores = $this->consultaError($ret_code);

                    $cadena = collect ([
                        "code" => $ret_code,
                        "descripcion" => $errores,
                    ]);

                    return $this->errorResponse($cadena,422);
                }else{
                    $ret_code = $respuesta['error_code'];
                    $errores = $respuesta['error_descripcion'];

                    $cadena = collect ([
                        "code" => $ret_code,
                        "descripcion" => $errores,
                    ]);

                    return $this->errorResponse($cadena,500);
                }
            }  
        }

        return $this->showAll(collect($cadena),200);
	*/
    }

    public function transferencias_internas(Request $request)
    {
        //dd('a: '. $request->descripcion);
        $transferencia = $this->transferenciasInternas($request);
        if($transferencia['referencia'] != "" && $transferencia['error'] == ""){

            $cadena = collect ([
                        "code" => '200',
                        "reference" => $transferencia['referencia'],
                    ]);

            return $this->showAll(collect($cadena),200);
        }else{
            $cadena = collect ([
                        "code" => '500',
                        "description_error" => $transferencia['error'],
                    ]);
            return $this->errorResponse($cadena,500);
        }
    }

    public function mesa_de_cambio(Request $request)
    {
        //dd('a: '. $request->cuenta_acreditar);
        $mesa = $this->mesaDeCambio($request);

        if(!isset($mesa['cod_error_1'])){

            $cadena = collect ([
                        "code" => '200',
                        "cuenta_debitar" => $mesa['cuenta_debitar'],
                        "cuenta_acreditar" => $mesa['cuenta_acreditar'],
                        "monto_divisa" => $mesa['monto_divisa'],
                        "total_pagar" => $mesa['total_pagar'],
                        "referencia" => $mesa['referencia'],
                        "cod_trans_dest" => $mesa['cod_trans_dest'],
                        "cod_trans_orig" => $mesa['cod_trans_orig'],
                        "comision" => $mesa['comision'],
                        "tipo" => $mesa['tipo'],
                        "tasa" => $mesa['tasa'],
                        "ejecucion" => $mesa['ejecucion']
                    ]);

            return $this->showAll(collect($cadena),200);
        }else{
            $cadena = collect ([
                        "code" => '500',
                        "cod_error_1" => $mesa['cod_error_1'],
                        "cod_error_2" => $mesa['cod_error_2']
                    ]);
            return $this->errorResponse($cadena,500);
        }
    }
}
