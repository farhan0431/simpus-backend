<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use Validator;
use Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = User::orderBy('created_at', 'DESC')->when(request()->q, function($query) {
            $query->where('name', 'LIKE', '%' . request()->q . '%');
        });
        return response()->json([
            'status' => 'success', 
            'search' => request()->q,
            'data' => request()->type == 'all' ? $user->get():$user->paginate(10)
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => app('hash')->make($request->password)
        ]);
        return response()->json(['status' => 'success']);
    }

    public function edit($id)
    {
        $user = User::find($id);
        return response()->json(['status' => 'success', 'data' => $user]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6'
        ]);

        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password != '' ? app('hash')->make($request->password):$user->password,
        ]);
        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        // logActivity('Menghapus Pengguna');
        return response()->json(['status' => 'success']);
    }

    public function updateProfile(Request $request)
    {
        $user = request()->user();
        $validate = Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $data = $request->only('nik', 'name', 'username', 'email', 'telp');
        if ($request->password != '') {
            $data['password'] = app('hash')->make($request->password);
        }
        $user->update($data);
        return response()->json(['status' => 'success']);
    }

    public function updateProfileAvatar(Request $request)
    {
        $user = $request->user();
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $user->username . '.png';
            move_uploaded_file($file, base_path('public/user/avatar/' . $filename));

            $user->update(['thumb_avatar' => $filename]);
        }
        return response()->json(['status' => 'success']);
    }

    public function updatePasswordUser(Request $request)
    {
        $this->validate($request, [
            'old' => 'required|string',
            'new' => 'required|string|confirmed'
        ]);

        $user = $request->user();
        if (Hash::check($request->old, $user->password)) {
            $user->update([
                'password' => bcrypt($request->new)
            ]);
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error']);
    }

    // public function getUserLogin()
    // {
    //     // $user = request()->user()->load(['role', 'role.role_permission.permission']);
    //     $user = request()->user();
    //     $setting = Setting::first();
    //     $user['setting'] = $setting;
    //     return response()->json(['status' => 'success', 'data' => $user]);
    // }

    // public function getCaptchaImage()
    // {
    //     $captcha = \Captcha::create('flat', true);
    //     return response()->json(['status' => 'success', 'data' => $captcha]);
    // }
}
