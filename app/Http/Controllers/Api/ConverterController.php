<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ConverterController extends Controller
{
    function currencyConverter(Request $request){

        try{

            $amount = urlencode($request->amount); //valor desejado a ser convertido

            $from_Currency = urlencode($request->from_currency);// recebe a moeda que sera convertida

            $to_Currency = urlencode($request->to_currency);// recebe a moeda final

            $query = "{$from_Currency}_{$to_Currency}";//moedas selecionadas

            $apiKey = env('APP_KEY');//Key para validar a api
            $json = Http::get("https://free.currconv.com/api/v7/convert?q={$query}&compact=ultra&apiKey={$apiKey}"); //api com as cotaçoes de moedas em tempo real

            $obj = json_decode($json, true); //transforma o objeto em json
            //dd($obj);

            $val = $obj["$query"]; //pega as moedas a serem convertidas

            $total = $val * $amount; // pega os valores da moedas e multiplica

            $formatValue = number_format($total, 2, '.', ''); //formata o valor total em duas casas decimais
            /**Retornando os vores em jason */
            return response()->json([
                'data' =>[
                    'value' => $amount,
                    'convertedValue ' => $formatValue,

                        '_metadata'=>[
                            'rate' => $val,
                            'from' => $from_Currency,
                            'to' => $to_Currency
                        ]
                ]
            ]);
        }catch (\Exception $e){
        /**Verifica se o campo from_Currency está preenchido */
            if($from_Currency == NULL ){
                 return response()->json(['error'=>'Esta faltando o parametro from_currency, inserir apenas letras'], 400);

            }
            /**Verifica se o campo to_Currency está preenchido */
           if($to_Currency == NULL){
                return response()->json(['error'=>'Esta faltando o parametro to_currency, inserir apenas letras'], 400);

           }
           /**Verifica se o campo amount está preenchido */
           if($amount == null ){
            return response()->json(['error'=>'Esta faltando o parametro amount, inserir apenas numeros, reais ou inteiros'], 400);

           }
           /**Verifica se os valores inseridos correspondem ao campo certo */
           if(!!is_string($from_Currency) || !!is_string($to_Currency) || !!is_float($amount)){
            return response()->json(['erro'=>'Insrira apenas letras nos campos from e to currency, e apenas numeros no campo amount']);
            }

        }
    }
}
