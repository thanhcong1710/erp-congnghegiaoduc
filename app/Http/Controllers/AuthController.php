<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{ 
   
    /**
     * Register new user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request){
        $validate = Validator::make($request->all(), [
            'name'      => 'required',
            'email'      => ['required', 'email', 'unique:users'],
            // 'phone'     => ['required','unique:users','regex:/^(84[3|5|7|8|9]|0[3|5|7|8|9])+([0-9]{8})\b$/'],
            'password'  => 'required|min:4|confirmed',
        ]);        
        if ($validate->fails()){
            return response()->json([
                'status' => 0,
                'message' => 'Email đã tồn tại trên hệ thống.'
            ], 200);
        }        
        $user = new User;
        $user->name = $request->name;
        $user->email = u::convertPhoneNumber($request->email);
        // $user->phone = u::convertPhoneNumber($request->phone);
        $user->password = bcrypt($request->password);
        $user->menuroles = 'user';
        $user->status = '0';
        $user->email_verified_code = uniqid();
        $user->save(); 
        
        return response()->json([
            'status' => 1,
            'message' => 'successfully'
        ]);
    } 

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = request(['hrm_id', 'password']);
        $user = u::getObject(array('hrm_id'=>$request->hrm_id), 'users');
        if($user && $user->status!=1){
            return response()->json([
                'status' => 0,
                'type' => 'inactive',
                'message'=>'Tài khoản chưa được kích hoạt.'
            ]);
        }

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'status' => 0, 
                'type' => 'account',
                'message'=>'Mã nhân viên hoặc mật khẩu không chính xác.'
            ]);
        }

        u::updateSimpleRow(array('api_token'=>$token), array('id'=>auth()->user()->id), 'users');
        return $this->respondWithToken($token, $request->hrm_id);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        
        $this->validate($request, [
            'token' => 'required'
        ]);
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'status' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $hrm_id)
    {
        return response()->json([
            'status' => 1,
            'accessToken' => $token,
            'userData' => [
                'displayName' => auth()->user()->name,
                'hrm_id' => auth()->user()->hrm_id,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone,
                'photoURL' => auth()->user()->avatar_url ? auth()->user()->avatar_url : "/images/avatar-default.jpg",
                'providerId' => "jwt",
                'uid' => auth()->user()->id,
                'address' =>  auth()->user()->address,
                'birthday' => auth()->user()->birthday ? date('d/m/Y', strtotime(auth()->user()->birthday )) : null,
                'note' => auth()->user()->note,
                'gender' => auth()->user()->gender,
                'roleName' => auth()->user()->role_name,
                'permissions' => u::getPermissions(auth()->user()->id)
            ]
        ]);
    }
   

    private function sendActiveAccount($email){
        $user_info = u::getObject(array('email'=>$email),'users');
        if($user_info){
            $data = [
                'image_url_logo' => config('app.url').'/static/logo.png',
                'domain' => config('app.name'),
                'url_active' => config('app.url').'/api/account/activate/'.$user_info->email_verified_code,
                'to_mail' =>$user_info->email,
                'url_website'=> config('app.url'),
            ];
            $result = Mail::send('mail.activeAccount', array('data'=>$data), function($message) use ($user_info){
                $subject = "[Công nghệ giáo dục] Hoàn tất đăng ký";
                $message->to($user_info->email, $user_info->name)->subject($subject);
            });
        }
        
    }
    public function viewMail(){
        $data = [
            'image_url_logo' => 'http://local.congnghegiaoduc.com/static/logo.png',
            'domain' =>'congnghegiaoduc.com',
            'url_active' => 'https://onboarding-api.brevo.com/account/activate/fa08ee5b71d4785903d9acd3697253c8',
            'to_mail' =>'thanhcong1710@gmail.com',
            'url_website'=> 'http://local.congnghegiaoduc.com',
        ];
        return view('mail.activeAccount', ['data'=>$data]);
    }

    public function testMail(){
        $this->sendActiveAccount('thanhcong1710@gmail.com');
    }

    public function activeAccount(Request $request){
        $key = $request->key;
        $user_info = u::getObject(array('email_verified_code'=>$key), 'users');
        if($user_info){
            u::updateSimpleRow(array(
                'status' => 1,
                'email_verified_at' => date('Y-m-d H:i:s')
            ), array('id'=>$user_info->id),'users');
            $link = config('app.url').'/pages/active-account?status=1&email='.$user_info->email;
        }else{
            $link = config('app.url').'/pages/active-account?status=0';
        }
        return redirect($link);
    }

    public function forgotPassword(Request $request){
        $user_info = u::getObject(array('email'=>$request->email), 'users');
        if($user_info){
            $data = [
                'image_url_logo' => config('app.url').'/static/logo.png',
                'domain' => config('app.name'),
                'url_reset_pass' => config('app.url').'/pages/reset-password?email='.$user_info->email.'&key_reset='.md5($user_info->email."CNGD@1234"),
                'to_mail' =>$user_info->email,
                'url_website'=> config('app.url'),
            ];
            $result = Mail::send('mail.resetPassword', array('data'=>$data), function($message) use ($user_info){
                $subject = "[Công nghệ giáo dục] Đặt lại mật khẩu";
                $message->to($user_info->email, $user_info->name)->subject($subject);
            });
            return response()->json([
                'status' => 1,
                'message' => 'Một email đã được gửi đến hộp thư của bạn.'
            ], 200);
        }else{
            return response()->json([
                'status' => 0,
                'message' => 'Email không hợp lệ, vui lòng liên hệ với quản trị viên để được hỗ trợ.'
            ], 200);
        }
    }

    public function resetPassword(Request $request){
        $user_info = u::getObject(array('email'=>$request->email), 'users');
        if($user_info){
            if($request->key_reset == md5($user_info->email."CNGD@1234")){
                u::updateSimpleRow(array(
                    'password' => Hash::make($request->password)
                ), array('id' => $user_info->id), 'users');
                return response()->json([
                    'status' => 1,
                    'message' => 'Đổi mật khẩu thành công.'
                ], 200);
            }else{
                return response()->json([
                    'status' => 0,
                    'message' => 'Mã cập nhật không hợp lệ, vui lòng liên hệ với quản trị viên để được hỗ trợ.'
                ], 200); 
            }
        }else{
            return response()->json([
                'status' => 0,
                'message' => 'Email không hợp lệ, vui lòng liên hệ với quản trị viên để được hỗ trợ.'
            ], 200);
        }
    }

    public function revokeToken(Request $request){
        if($request->user_id){
            $user = u::first("SELECT api_token AS token FROM users WHERE status = 1 AND id= $request->user_id");
            if($user && $user->token){
                JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token( $user->token)); 
            }
            $result = array(
                'status' => 1,
                'message' => 'Xóa token đăng nhập thành công'
            );
        }else{
            $users = u::query("SELECT api_token AS token FROM users WHERE status = 1");
            foreach($users AS $user){
                if($user->token){
                    JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token( $user->token)); 
                }
            }
            $result = array(
                'status' => 1,
                'message' => 'Xóa tất cả token đăng nhập thành công'
            );
        }
        
        return response()->json($result);
    }
}