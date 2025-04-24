<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
	public function show(Request $request) {
		$data['user'] = Auth::user();
		return view('profile',$data);
	}
	public function edit(Request $request) {
		//$user = $request->get('user');
		//$data['user_info'] = User::where('email',$user['email'])->first();
		return view('profile');
	}
	public function update(Request $request) {
		$userId = Auth::id();
		$update = [];
		$success = $error = [];

		// https://laravel.com/docs/5.5/validation#available-validation-rules
		$validatedData = $request->validate([
			//'email' => 'required|email|unique:users,email',
			'password' => 'required|min:4',
			'name' => 'required|string|min:3|max:255',
			
			
			
		]);

		//if(!empty($request->name)) $update['name'] = $request->name;
		//if(!empty($request->logo) && filter_var($request->logo, FILTER_VALIDATE_URL)) $update['logo'] = $request->logo;
		
		//$update['gender'] = ($request->gender=='male') ? true : false;
		if($validatedData['password']) {
			$validatedData['password'] = Hash::make($validatedData['password']);
			$success[] = 'Password changed success.';
		}

		User::where('id', $userId)->update($validatedData);
		$data['user'] = User::where('id', $userId)->first();
		$data['success'] = true;
		$success[] = 'Profile updated!';
		//$request->session()->flash('status', ['success' => true, 'message' => 'Update profile success!']);
		return redirect()->route('profile')->with('success', $success); // Redirecting With Flashed Session Data // @if (session('status'))
		//return redirect()->route('dashboard.profile', ['name' => $update['name']]);

		return view('profile', $data);
	}
	public function postFile(Request $req) {
		if($req->hasFile('myFile')) {
			$file = $req->file('myFile');
			$filename = $file->getClientOriginalName();
			echo 'Has file: '.$filename.'<br>';
			$file->move('img',$filename);
		} else {
			echo 'No file to upload.';
		}
	}
}
