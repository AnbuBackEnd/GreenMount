<?php

namespace App\Http\Controllers;
use Auth;
use Hash;
use Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function registerEncrypt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);


        if(!$validator->fails())
        {
            $response['data']=$this->encryptData(json_encode($request->all()));
            //$response=$this->encrypt($output);
            $code = 200;
        }
        else
        {
            $response['message']=[$validator->errors()->first()];
        // $response=$this->encrypt($output);
            $code = 200;
        }
        return response($response, $code);
    }
    public function getAllDetails()
    {
        $users=User::all();
    }
    public function deleteUsersEncrypt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'objectId' => 'required',
        ]);


        if(!$validator->fails())
        {
            $response['data']=$this->encryptData(json_encode($request->all()));
            //$response=$this->encrypt($output);
            $code = 200;
        }
        else
        {
            $response['message']=[$validator->errors()->first()];
        // $response=$this->encrypt($output);
            $code = 200;
        }
        return response($response, $code);
    }
    public function deleteUsers(Request $request)
    {
        $rules = [
            'objectId' => 'required',
        ];
        $input=(array)$this->decrypt($request->input('input'));
        $validator = Validator::make($input, $rules);
        if(!$validator->fails())
        {
            echo $input['objectId'];
        }
        else
        {
            $output['status']=false;
            $output['message']=[$validator->errors()->first()];
            //$response=$this->encrypt($na);
            $response['data']=$this->encryptData($output);
            $code=400;
        }

            // if (Users::where('id', '=', $bookId)->where('deleteStatus',0)->count() == 1)
            // {
            //     Bookcatagory::where('id',$bookId)->update(array('deleteStatus' => 1));
            //     $output['status'] = true;
            //     $output['message'] = 'Successfully Deleted';
            //     $response['data']=$this->encryptData($output);
            //     $code=200;
            // }
            // else
            // {
            //     $output['status'] = false;
            //     $output['message'] = 'No Records Found';
            //     $response['data']=$this->encryptData($output);
            //     $code=400;
            // }

        //return response($response, $code);
    }
    public function register(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ];
        $input=(array)$this->decrypt($request->input('input'));
        $validator = Validator::make($input, $rules);
        if(!$validator->fails())
        {
            if (User::where('email', '=', $input['email'])->count() == 0)
            {
                $output=array();
                $outputfinal=array();
                $user = new User;
                $user->name = $input['name'];
                $user->email = $input['email'];
                $user->password = Hash::make($input['password']);
                if($user->save())
                {
                    $output['status']=true;
                    $output['message']='Otp send to Your Mail';
                    $response['data']=$this->encryptData($output);
                    //$response=$this->encrypt($output);
                    $code = 200;
                }
                else
                {
                    $output['status']=true;
                    $output['message']='Something went wrong. Please try again later.';
                    $response['data']=$this->encryptData($output);
                    // $response=$this->encrypt($output);
                    $code = 400;
                }
            }
            else
            {
                $output['status']=false;
                $output['message']='Already Exists';
                $response['data']=$this->encryptData($output);
            // $response=$this->encrypt($output);
                $code = 409;
            }
        }
        else
        {
            $output['status']=false;
            $output['message']=[$validator->errors()->first()];
            //$response=$this->encrypt($output);
            $response['data']=$this->encryptData($output);
            $code=400;
        }
        return response($response, $code);
    }
    public function encryptData($content)
    {
        $key='s#Jv6ejUxs7MKcgyTkC3X9zZLjslGw2f';
		$iv='K10Djpm7%9On%q7K';
        if (gettype($content) == 'string') {
            $encrypted = base64_encode(openssl_encrypt($content, 'AES-256-CBC', $key, OPENSSL_RAW_DATA,$iv));
        }
        else{
            $encrypted = base64_encode(openssl_encrypt(json_encode($content), 'AES-256-CBC', $key, OPENSSL_RAW_DATA,$iv));
        }
        return $encrypted;

    }
    public function decrypt($input){
        $key='s#Jv6ejUxs7MKcgyTkC3X9zZLjslGw2f';
		$iv='K10Djpm7%9On%q7K';
		$result=openssl_decrypt(base64_decode($input), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return json_decode($result);
	}
}
