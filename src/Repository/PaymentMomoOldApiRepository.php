<?php


namespace  Nkaurelien\Momopay\Repository;


use  Nkaurelien\Momopay\Fluent\MomoRequestToPayDto;
use Illuminate\Support\Facades\Log;
use  Nkaurelien\Momopay\Fluent\MomoToken;
use Illuminate\Support\Fluent;

class PaymentMomoOldApiRepository extends  PaymentMomoSandboxRepository
{
    /**
     * Payer avec mtn mobile money
     * @param float $_amount
     * @param string $_tel MSISDN ou Mobile Station ISDN Number du client;
     * est le numéro « connu du public » de l'usager GSM ou UMTS par opposition au numéro IMSI
     * @param null $idbouton
     * @param string $typebouton
     * @return \Illuminate\Http\JsonResponse
     */
    public function payWithMtnMobileMoney(float $_amount, string $_tel, \Illuminate\Http\Request $request)
    {

        $idbouton = $request->input('idbouton');
        $typebouton = $request->input('typebouton', 'PAIE');

        $user = \request()->user();

        $client_id = config('services.mtn.client_id');

        if (!isset($client_id) || strlen($client_id) <= 0) {

            die('INVALID CLIENT ID, Set it in environment variable called MTN_MOMO_ID');
        }

        if (strlen($_tel) <= 0 || $_amount <= 0.0) {

            die('INVALID INPUTS, Set the amount of the transaction');
        }


        $query = compact('_amount', '_tel');
        //$query['idbouton'] = 2;
        // $query['_clP'] = $this->configs['services']['mtn']['client_secret'];
        $query['_email'] = $client_id;
        $query['idbouton'] = $idbouton;
        $query['typebouton'] = $typebouton;

        try {

            // $query_str = \http_build_query($query);


            $url = "https://developer.mtn.cm/OnlineMomoWeb/faces/transaction/transactionRequest.xhtml?typebouton=PAIE&_email=dev%40shlife.fr&_amount={$_amount}&_tel={$_tel}";

            $resBody = $this->http_call($url);


            if (strlen($resBody) > 0) {


                $data = $this->json_decode($resBody);

                if (empty($data->TransactionID)) {

                    switch ($data->StatusCode) {
                        case "529":
                            return response()->json(['error' => "L'opération de paiement à echoué, votre solde est insuffisant"], 400);

                        default:
                            return response()->json(['error' => "L'opération de paiement à echoué"], 400);
                    }
                } else {

                    $data->payload = $request->input('payload', null);

                    Log::info('[Transaction][id:' . $data->TransactionID . '] New payment transaction return code ' . $data->StatusCode . '');
                    //event(new PaymentDone('MTN', $data));
                    //$user->notify(new PayementDoneNotification($data));
                    return response()->json($data, 200);
                }


            }
            return response()->json(['error' => "L'opération de paiement à echoué, La reponse de l'operateur est vide"], 400);


        } catch (\Exception $e) {


            // echo $e->getMessage() . "\n";
            // echo $e->getRequest()->getMethod();
            $status = 400;
            $data = 'Request Failure with code ' . $e->getCode();

            return response()->json($data, $status);

        }
    }


    /**
     * @param $url
     * @return mixed
     * @throws \Exception
     */
    private function http_call($url)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "gzip,deflate",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 2 * 60, //seconde
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_COOKIE => "SESSIONID=" . session()->getId(),
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new \Exception("cURL Error #:" . $err);

        } else {
            return $response;
        }
    }

    /**
     * Wrapper for JSON encoding that throws when an error occurs.
     *
     * @param mixed $value The value being encoded
     * @param int $options JSON encode option bitmask
     * @param int $depth Set the maximum depth. Must be greater than zero.
     *
     * @return string
     * @throws \InvalidArgumentException if the JSON cannot be encoded.
     * @link http://www.php.net/manual/en/function.json-encode.php
     */
    private function json_encode($value, $options = 0, $depth = 512)
    {
        $json = \json_encode($value, $options, $depth);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(
                'json_encode error: ' . json_last_error_msg()
            );
        }

        return $json;
    }


    /**
     * Wrapper for json_decode that throws when an error occurs.
     *
     * @param string $json JSON data to parse
     * @param bool $assoc When true, returned objects will be converted
     *                        into associative arrays.
     * @param int $depth User specified recursion depth.
     * @param int $options Bitmask of JSON decode options.
     *
     * @return mixed
     * @throws \InvalidArgumentException if the JSON cannot be decoded.
     * @link http://www.php.net/manual/en/function.json-decode.php
     */
    private function json_decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        $data = \json_decode($json, $assoc, $depth, $options);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(
                'json_decode error: ' . json_last_error_msg()
            );
        }

        return $data;
    }

}
