<?php

namespace App\ExchangeRate;

use App\ExchangeRate\IExchangeRate;
use App\Models\User;
use Exception;
use Faker\Provider\Uuid;
use GuzzleHttp\Psr7\Request;
use stdClass;

class ConcreteExchangeRate implements IExchangeRate {

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

    private function getFailedResponse($description) {
        $std = new stdClass();
        $std->status = 'failed';
        $std->message = 'request failed';
        $std->description = $description;
       // $std->data = $data;

        return $std;
    }

    
    public function AddNewUser($user) 
    {
        try
        {
            $findUser = User::where('email', $user)->first();

            if($findUser != null || count($findUser) > 0) {
                
    
                return $this->output($this->getFailedResponse($user->email."exist before"), 400);
    
    
            }
    
            $newUser = $user;
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
            $newUser->password = md5($user->password);
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
        

    }
    public function AuthenticateUser($email, $access_key) 
    {
        try
        {
            $findUser = User::where('email', $email)
                ->where('access_key', $access_key)
                ->first();
            if($findUser == null || empty($findUser)) {
            
               return $this->output($this->getFailedResponse("invalid username or password"), 401);
        
        
            }
            $data = json_encode(["sessionId"=>Uuid::uuid()]);
            return $this->output($this->getSuccessResponse($data));

        }
        catch(Exception $es) {
            return $this->output($this->getFailedResponse($es->getMessage()), 500);
        }

    }



}