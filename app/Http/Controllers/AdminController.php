<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\GoogleAccount;
use App\Http\Controllers\Controller;
use App\Http\Resources\GoogleAccountResource;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.admin');
    }

    public function dashboard()
    {
        return redirect('/admin/users');
    }

    public function users()
    {
        return view('admin-users', [
            'users' => User::all()
        ]);
    }

    public function deleteUser(Request $request, $userId)
    {
        $user = User::where('is_admin', 0)->findOrFail($userId);
        $user->delete();
        return redirect()->back();
    }

    public function googleAccounts()
    {
        return view('admin-accounts', [
            'accounts' => GoogleAccount::all()
        ]);
    }

    public function files()
    {
        return view('admin-files', [
            'files' => File::with('user')->get()
        ]);
    }

    public function deleteFile($fileId)
    {
        $file = File::findOrFail($fileId);
        $file->delete();
        return redirect()->back();
    }

    public function addGoogleAccount(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'image' => 'string|nullable',
            'refresh_token' => 'required|string',
        ]);

        if ($account = GoogleAccount::where('email', $data['email'])->exists()) {
            return response()->json([
                'error' => true,
                'message' => 'Tài khoản đã tồn tại'
            ]);
        }

        $data['is_active'] = true;
        $account = GoogleAccount::create($data);

        // return new GoogleAccountResource($account);
        return redirect()->back();
    }

    public function updateGoogleAccount(Request $request, $accountId)
    {
        // $data = $request->validate([
        //     'is_active' => 'required|boolean'
        // ]);

        $account = GoogleAccount::findOrFail($accountId);
        $account->update([
            'is_active' => !$account->is_active
        ]);

        // return new GoogleAccountResource($account);
        return redirect()->back();
    }

    public function deleteGoogleAccount($accountId)
    {
        $account = GoogleAccount::findOrFail($accountId);
        $account->delete();
        return redirect()->back();
    }
  
  	public function edituser($userId)
    {
        $user = User::find($userId);
        


        return view('admin-users-edit',compact('user'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateuser(Request $request, $id)
    {
        $this->validate($request, [
            //nullable
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:password',
            'is_admin' => 'required',
            
        ]);


        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = array_except($input,array('password'));    
        }


        $user = User::find($id);
        $user->update($input);
        
        

        


        //return redirect()->route('admin-users-edit')
        //                ->with('success','User updated successfully');
         return view('admin-users-edit', compact('success', 'user'));         
                        
    }
  
}
