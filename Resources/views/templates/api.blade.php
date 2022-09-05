@php echo "<?php";
@endphp

@php
function getName($string){
    $expload = explode('_', $string);
    $tableName = "";
    $count = 0;
    foreach ($expload as $item) {
        if($count === count($expload)){
            $item = Str::ucfirst($item);
            $tableName .= $item;
        }
        else if(count($expload)) {
            $item = Str::ucfirst($item) . " ";
            $tableName .= $item;
        }
        else {
            $item = Str::ucfirst($item);
            $tableName .= $item;
        }

        $count++;
    }

    return Str::ucfirst($tableName);
}
@endphp

namespace Modules\{{ $module }}\Http\Controllers\Api;
use Modules\{{ $module }}\Entities\{{ $model }};
use Modules\Auth\Services\AuthService;
use Modules\Auth\Entities\AuthModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Auth\Services\AuthLogic;
use Modules\Auth\Entities\AuthPlugin;
use Auth;

class {{ $model }}Auth
{
    public $model = {{ $model }}::class;


    /**
    * Register.
    *
    * @return \Illuminate\Http\JsonResponse
    */

    public function register(Request $request)
    {
        $rules = [
            @foreach($cols as $col)
                @php
                    if($col['required']){
                        $required = "required";
                    }
                    else {
                        $required = "";
                    }
                    if($col['unique']){
                        $unique = "unique:auth_plugins,".$col['name'];
                    }
                    else {
                        $unique = "";
                    }
                    if($col['maxLength']){
                        $max = "max:" . $col['maxLength'];
                    }
                    else {
                        $max = "";
                    }
                @endphp
                @if($col['name'] === 'password')
                    "password" => "{{ $required ? $required . "|" : "" }}{{ $max ? $max . "|" : "" }}string|min:8|confirmed",
                @elseif($col['name'] === 'email')
                    "email" => "{{ $required ? $required . "|" : "" }}{{ $max ? $max . "|" : "" }}{{ $unique ? $unique . "|" : "" }}email|string",
                @elseif($col['type'] === 'relation')
                    "{{ $col['name'] }}" => "{{ $required ? $required . "|" : "" }}{{ $max ? $max . "|" : "" }}{{ $unique ? $unique . "|" : "" }}array",
                @elseif($col['type'] === 'boolean')
                    "{{ $col['name'] }}" => "{{ $required ? $required . "|" : "" }}{{ $max ? $max . "|" : "" }}{{ $unique ? $unique . "|" : "" }}bool",
                @elseif($col['name'] === 'phone')
                    "{{ $col['name'] }}" => "{{ $required ? $required . "|" : "" }}{{ $max ? $max . "|" : "" }}{{ $unique ? $unique . "|" : "" }}string",
                @elseif($col['name'] === 'id')
                @else
                    "{{ $col['name'] }}" => "{{ $required ? $required . "|" : "" }}{{ $max ? $max . "|" : "" }}{{ $unique ? $unique . "|" : "" }}string",
                @endif
            @endforeach
            ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
        return response()->json([
        'success' => false,
        'message' =>$validator->errors()->first()
        ],401);
        }

        $type='Modules\{{ $module }}\Entities\{{ $model }}';

        $item = $this->model::create($request->except(['password','email']));

        $authNewRecord = new  AuthLogic();
        $authNewRecord->register($request->all(),$item,$type);
        return response()->json([
        'status' => 200,
        'message' =>'done ',
        'token' =>  $item->createToken("API TOKEN")->plainTextToken
        ]);

    }

    public function login(Request $request)
    {
            $rules = [
                'phone' => 'required',
                'password' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
            return response()->json([
            'success' => false,
            'message' =>$validator->errors()->first()
            ],401);
            }

            $type='Modules\{{ $module }}\Entities\{{ $model }}';


            $authPerson = AuthPlugin::where('phone', $request->phone)
                                ->where('model_type','=',$type)->first();
            if (!$authPerson) {
                return response()->json([
                'success' => false,
                'message' =>'this user not found'
                ],401);
            }

            if(!Auth::guard('custom')->attempt($request->only(['phone', 'password']))){
                return response()->json([
                'status' => false,
                'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            return response()->json([
                'status' => 200,
                'message' =>'done ',
                'token' =>  $authPerson->createToken("API TOKEN")->plainTextToken
            ]);


    }

    public function verifyAccount(Request $request)
    {

        $model = $request->user();

        if($model){
            $model->update([
            'activated' => 1
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' =>'done '
        ]);

    }

    public function profile(Request $request)
    {
        $data = [];
        $model = $request->user();

        if($model){
            $item = $this->model::find($model->model_id);
            $data =array_merge($item->toArray(), $model->toArray());
            unset($data['model_id']);
            unset($data['model_type']);
            unset($data['password']);
        }
        return response()->json([
            'status' => 200,
            'message' =>'done',
            "data"=>  $data
        ]);
    }

    public function forgetPassword(Request $request)
    {
        $rules = [
            'phone' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
            'success' => false,
            'message' =>$validator->errors()->first()
            ],401);
        }

        $type='Modules\{{ $module }}\Entities\{{ $model }}';


        $model = AuthPlugin::where('phone', $request->phone)
        ->where('model_type','=',$type)->first();
        $otp =random_int(100000, 999999);
        if($model){
            $model->update([
                 'otp' => $otp
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' =>'done ',
            'otp' => $model->otp

            ]);
    }
    public function resetPassword(Request $request){
        $rules = [
            'otp' => 'required',
            'password' => 'required',
            'phone' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' =>$validator->errors()->first()
            ],401);
        }

        $type='Modules\{{ $module }}\Entities\{{ $model }}';


        $model = AuthPlugin::where('phone', $request->phone)
            ->where('otp','=',$request->otp)
             ->where('model_type','=',$type)->first();

        if($model){
            $model->update([
            'password'=> bcrypt( $request->password)
            ]);
            return response()->json([
            'status' => 200,
            'message' =>'done '
            ]);
        }else{
            return response()->json([
            'status' => false,
            'message' => 'Otp Or Phone Not Correct',
            ], 401);

        }


    }

    public function updateProfile(Request $request)
    {
         $model = $request->user();
            $rules = [
            @foreach($cols as $col)
                @php
                    if($col['required']){
                        $required = "sometimes|nullable";
                    }
                    else {
                        $required = "";
                    }
                    if($col['unique']){
                        $unique = "unique:auth_plugins,".$col['name'];
                    }
                    else {
                        $unique = "";
                    }
                    if($col['maxLength']){
                        $max = "max:" . $col['maxLength'];
                    }
                    else {
                        $max = "";
                    }
                @endphp
                @if($col['name'] === 'password')
                    "password" => "{{ $required ? $required . "|" : "" }}{{ $max ? $max . "|" : "" }}string|min:8|confirmed",
                @elseif($col['name'] === 'email')
                    "email" => "{{ $required ? $required . "|" : "" }}{{ $max ? $max . "|" : "" }}{{ $unique ? $unique : "" }},".$model->id.",id",
                @elseif($col['name'] === 'phone')
                    "phone" => "{{ $required ? $required . "|" : "" }}{{ $max ? $max . "|" : "" }}{{ $unique ? $unique  : "" }},".$model->id.",id",
                @elseif($col['type'] === 'relation')
                    "{{ $col['name'] }}" => "{{ $required ? $required . "|" : "" }}{{ $max ? $max . "|" : "" }}{{ $unique ? $unique . "|" : "" }}array",
                @elseif($col['type'] === 'boolean')
                    "{{ $col['name'] }}" => "{{ $required ? $required . "|" : "" }}{{ $max ? $max . "|" : "" }}{{ $unique ? $unique . "|" : "" }}bool",
                @elseif($col['name'] === 'id')
                @else
                    "{{ $col['name'] }}" => "{{ $required ? $required . "|" : "" }}{{ $max ? $max . "|" : "" }}{{ $unique ? $unique . "|" : "" }}string",
                @endif
            @endforeach
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                'success' => false,
                'message' =>$validator->errors()->first()
                ],401);
            }
                $data =$request->except(['email','phone']);
                $data['id'] =  $model->model_id;
                $profile =  $this->model::find($model->model_id);
                $profile->update($data);
                $authNewRecord = new  AuthLogic();
                $authNewRecord->updateProfile($request->all(),$model);

                return response()->json([
                'status' => 200,
                'message' =>'done '
                ]);

    }

    public function changePassword(Request $request)
    {
        $model = $request->user();

        $rules = [
            'new_password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
            'success' => false,
            'message' =>$validator->errors()->first()
            ],401);
        }


        if($model){
            $model->update([
                 'password'=> bcrypt( $request->new_password)
            ]);
            $model->currentAccessToken()->delete();
            return response()->json([
            'status' => 200,
            'message' =>'done '
            ]);
        }else{
            return response()->json([
            'status' => false,
            'message' => 'This User Not Found',
            ], 401);

        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => 200,
                'message' =>'Logout .'
            ]);
    }

}
