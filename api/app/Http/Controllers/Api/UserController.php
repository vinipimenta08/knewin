<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\Users;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Users::all();

        return response()->json(LibraryController::responseApi($users, 'ok'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), $this->roles($request));
            if ($validator->fails()) {
                return response()->json(LibraryController::responseApi([], $validator->getMessageBag(), 100));
            }

            $user = new Users();
            $user->fill($request->all());
            $user->password = bcrypt($user->password);
            $user->save();

            return response()->json(LibraryController::responseApi($user, 'ok'));
        } catch (Exception $e) {
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = Users::find($id);
            if(!$user){
                return response()->json(LibraryController::responseApi([],'Not Found', 400));
            }
            return response()->json(LibraryController::responseApi(['user' => $user]));
        } catch (Exception $e) {
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
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
        try {
            $user = Users::findOrFail($id);
            $user->fill($request->all());

            if (!$request->has('password') || $request->password == "") {
                unset($request->all()['password']);
                unset($user->password);
            }else {
                $user->password = bcrypt($request->password);
            }
            $validator = Validator::make($request->all(), $this->roles($request, $user));
            if ($validator->fails()) {
                return response()->json(LibraryController::responseApi([], $validator->getMessageBag(), 100));
            }

            $user->update();
            return response()->json(LibraryController::responseApi($user, 'ok'));

        }catch (Exception $e) {
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = Users::findOrFail($id);
            $user->delete();
            return response()->json(LibraryController::responseApi($user, 'ok'));
        } catch (Exception $e) {
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }

    public function roles($request, $user = null)
    {
        switch ($request->method()) {
            case 'POST':
                    $rules['email'] = 'required|email|unique:users';
                    $rules['password'] = 'required';
                break;
            case 'PUT':
                    $rules['email'] = [
                                        'required',
                                        'email',
                                        Rule::unique('users')->ignore($user->id),
                                    ];
                break;
            default:
                break;
        }
        return $rules;
    }
}
