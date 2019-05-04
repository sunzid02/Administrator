<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validation
        $this->validate($request ,[
            'name' => 'required | string | max:50',
            'email' => 'required | email |string | max:60 | unique:users',
            'password' => 'required | min: 5',
            'type' => 'required',
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'type' => $request->type,
            'bio' => $request->bio,
            'photo' => $request->photo,
            'password' => Hash::make($request->password),
        ]);
    }


    public function profile()
    {
        return auth('api')->user();

    }

    public function updateProfile(Request $request)
    {

        $user =  auth('api')->user();

        //validation
        $this->validate($request ,[
            'name' => 'required | string | max:50',
            'email' => 'required | email |string | max:60 | unique:users,email,'.$user->id,//if this userid's user email is same then skip validation
            'password' => 'sometimes | required | min: 5',
        ]);

        $currentPhoto = $user->photo;
        
        if ($request->photo != $currentPhoto) 
        {
          $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
          
          \Image::make($request->photo)->save(public_path('img/profile/').$name);
          
          $request->merge([
              'photo' => $name,
          ]);
        }

        // make password hashing
        if (!empty($request->password)) 
        {
           $request->merge([
               'password' => Hash::make($request->password),
           ]);
        }

        $user->update($request->all());

        return ['message' => "success"];
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
        $user = User::findOrFail($id);
        
        //validation
        $this->validate($request ,[
            'name' => 'required | string | max:50',
            'email' => 'required | email |string | max:60 | unique:users,email,'.$user->id,//if this userid's user email is same then skip validation
            'password' => 'sometimes | min: 5',
            'type' => 'required',
        ]);

        $user->update($request->all());

        return ['message' => 'Updated user info'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        //delete the user
        $user->delete();

        return ['message' => 'User deleted'];
    }
}
