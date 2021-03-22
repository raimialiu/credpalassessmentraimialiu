<?php

namespace App\Http\Controllers;

use App\ExchangeRate\ConcreteExchangeRate;
use Illuminate\Http\Request;
use App\ExchangeRate\IExchangeRate;
use App\Models\User;
use App\Notifications\alertnotification;
use Exception;
use Faker\Provider\Uuid;
use Illuminate\Support\Facades\Http;
use stdClass;

class exchangerate extends Controller
{
    //
    private $helper;
    private $request;

    public function __construct()
    {
        //$this->helper = new \App\ExchangeRate\ConcreteExchangeRate();

    }
    private function output($content, $statusCode=200) {
        return response(json_encode($content), $statusCode)
                ->header('Content-Type', 'application/json');
    }
    private function getSuccessResponse($data=null) {
        $std = new stdClass();
        $std->status = 'success';
        $std->message = 'request successfully executed';
        $std->data = $data;

        return $std;
    }

    private function getFailedResponse($description, $data=null) {
        $std = new stdClass();
        $std->status = 'failed';
        $std->message = 'request failed';
        $std->description = $description;
        $std->data = $data;

        return $std;
    }
    public function GetExchangeRate(Request $request) {
        $this->request = $request;
        try {
            $api_key = env('fixer_secret_key');
            $base = $this->getJsonKey('base');
            $symbols =$this->getJsonKey('symbols');
            $threshold = $this->getJsonKey('threshold');
            $url = "http://data.fixer.io/api/latest?access_key=$api_key";

            if($base != null || !empty($base)) {
                if($symbols == null || count($symbols) == 0) {
                    return $this->output($this->getFailedResponse('symbols are required and should at least one symbol'), 400);
                }

                if($threshold == null || empty($threshold)) {
                    return $this->output($this->getFailedResponse('please set a threshold for your base currency'), 400);
                }
                //return $this->output($this->getFailedResponse('base symbol is required'), 400);
                 //join()
                $symbolsToString = implode(",", $symbols);
                //var_dump($symbolsToString);
                $url = "http://data.fixer.io/api/latest?access_key=$api_key&base=$base&symbols=$symbolsToString";
            }

            $currentUser = $request->header('currentUser');
            
            //$decoded = json_decodejson_encode($request->header('currentUser'));

           // var_dump($decoded);
            if($currentUser == null || empty($currentUser)) {
                return $this->output($this->getFailedResponse('invalid request'), 400);   
            }
           
            $user = new User();
            $user->email = $currentUser;
            

            $returnData = Http::get($url);
            $jsonResponse = $returnData->json();
           //var_dump(json_encode($jsonResponse["rates"]));
            if($jsonResponse["success"] == false) {
                return $this->output($this->getFailedResponse($jsonResponse["error"]["type"]));
            }
            // loop over the rates and see the one that match the threshhold
            foreach($jsonResponse["rates"] as $rate) {
                if($threshold < $rate) {
                    $message = "Your base symbol " . $base. " currently at ". $rate. "to ";
                    // this line send notification
                   // $user->notify(new alertnotification($message));
                }
            }
            return $this->output($this->getSuccessResponse($jsonResponse["rates"]));
        }
        catch(Exception $es) {
            return $this->output($this->getFailedResponse($es->getMessage()), 500);
        }

    }

    private function getJsonKey($keyName) 
    {
        return $this->request->json()->get($keyName);
    }
    public function RegisterNewUser(Request $request) 
    {
        try 
        {
            $this->request = $request;

            $name = $this->getJsonKey('name');
            $email = $this->getJsonKey('email');
            $password = $this->getJsonKey('password');
            $newUser = new User();
        
            $newUser->email = $email;
            $newUser->password = $password;
            $newUser->name = $name;

           // $newUser = $user;
            //$newUser->email = $user->email;
            if($newUser->email == null || empty($newUser->email)) {
                return $this->output($this->getFailedResponse('email is required'), 400);
            }
            if($newUser->password == null || empty($newUser->password)) 
            {
                return $this->output($this->getFailedResponse('password is required'), 400);
            }

            if($newUser->name == null || empty($newUser->name)) 
            {
                return $this->output($this->getFailedResponse('name is required'), 400);
            }

            $findUser = User::where('email', $newUser->email)->first();

          //  var_dump($findUser);
            if($findUser != null || !empty($findUser)) {
                
    
                return $this->output($this->getFailedResponse($newUser->email." exist before"), 400);
       
            }
            $newUser->password = md5($newUser->password);
            $newUser->access_key = Uuid::uuid();
            //$newUser->name = $user->name;
    
            $result = $newUser->save();
            if($result) 
            {
                //$std->status = ''
                return $this->output($this->getSuccessResponse());
            }
    
            return $this->output($this->getFailedResponse('unable to add new user'), 400);
        }
        catch(Exception $es) {
            return $this->output($this->getFailedResponse($es->getMessage()), 500);
        }
       

        

        //return $this->helper->AddNewUser($newUser);
    }
}
