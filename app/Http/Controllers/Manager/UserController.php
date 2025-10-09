<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::paginate(10);
        return view('managers.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('managers.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:manager,karyawan,client',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' =>Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('managers.users.index')->with('success', 'User Created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
        return view('managers.users.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, User $user)
    // {
    //     //
    //     $data = $request -> validate([
    //         'name' => 'required|string|max:255',
    //         'email' => [
    //             'required',
    //             'email',
    //             Rule::unique('users')->ignore($user->id),
    //         ],
    //         'password' => 'nullable|min:6|confirmed',
    //         'role' => 'required|in:manager,karyawan,client',
    //     ]);

    //     $user->name = $data['name'];
    //     $user->email = $data['email'];
    //     if(!empty($data['password'])){
    //         $user->password = Hash::make($data['password']);
    //     }
    //     $user->save();
    //     $user->syncRoles([$data['role']]);
    //     return redirect()->route('managers.users.index')->with('success', 'Update Success');
    // }

    public function update(Request $request, User $user)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => [
            'required',
            'email',
            Rule::unique('users')->ignore($user->id),
        ],
        'password' => 'nullable|min:6|confirmed',
        'role' => 'required|in:manager,karyawan,client',
    ]);

    $user->name = $data['name'];
    $user->email = $data['email'];

    if (!empty($data['password'])) {
        $user->password = Hash::make($data['password']);
    }

    $user->save();
    $user->syncRoles([$data['role']]);

    return redirect()->route('managers.users.index')->with('success', 'Update Success');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user $user)
    {
        //
        if(auth()->id() === $user->id){
            return redirect()->back()->with('error','You cannot delete yourself.');
        }
        $user->delete();
        return redirect()->route('managers.users.index')->with('success', 'Delete Success');
    }
}
