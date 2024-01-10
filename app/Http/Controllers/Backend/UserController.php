<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\UUIDGenerate;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Wallet;
use App\Models\AdminUser;
use Jenssegers\Agent\Agent;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.user.index');
    }

    /**
     * for datatable
     */
    public function ssd()
    {
        $data = User::query();
        return DataTables::of($data)
            ->editColumn('created_at', function ($each) {
                return   Carbon::parse($each->created_at)->format("Y m d H:i:s");
            })
            ->editColumn('updated_at', function ($each) {
                return   Carbon::parse($each->created_at)->format("Y m d H:i:s");
            })
            ->editColumn('user_agent', function ($each) {
                if ($each->user_agent) {
                    $agent = new Agent();
                    $agent->setUserAgent($each->user_agent);
                    $device = $agent->device();
                    $platform = $agent->platform();
                    $browser = $agent->browser();

                    return  $platform . " platform <br>" . $browser . " browser";
                }
            })
            ->addColumn("actions", function ($each) {
                $edit_btn = "<a class=' fs-4 text-warning' href='" . route('admin.user.edit', $each->id) . "'><i class='pe-7s-note'></i></a>";
                $delete_btn = "<a class=' fs-4 text-danger delete_btn' href='#' data-id='" . $each->id . "'><i class='pe-7s-trash'></i></a>";

                return "<div>" . $edit_btn . $delete_btn . "</div>";
            })
            ->rawColumns(['actions', 'user_agent'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            Wallet::firstOrCreate(
                [
                    'user_id' => $user->id
                ],
                [
                    'account_number' => UUIDGenerate::accountNumber(),
                    'amount' => 0
                ]
            );

            DB::commit();
            return redirect()->route('admin.user.index')->with('status', "Successfully user created");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => "Something wrong"])->withInput();
        }
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
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('backend.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = $request->password ? Hash::make($request->password) : $user->password;
            $user->update();

            Wallet::firstOrCreate(
                [
                    'user_id' => $user->id
                ],
                [
                    'account_number' => UUIDGenerate::accountNumber(),
                    'amount' => 0
                ]
            );

            DB::commit();
            return redirect()->route('admin.user.index')->with('status', "Successfully user updated");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => "Something wrong"])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return "success";
    }
}
