<?php

namespace App\Http\Controllers\Backend;

use App\Models\AdminUser;
use Jenssegers\Agent\Agent;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use Carbon\Carbon;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.admin_user.index');
    }

    /**
     * for datatable
     */
    public function ssd() {
        $data = AdminUser::query();
        return DataTables::of($data)
        ->editColumn('created_at', function($each) {
            return   Carbon::parse($each->created_at)->format("Y m d H:i:s");
        })
        ->editColumn('updated_at', function($each) {
            return   Carbon::parse($each->created_at)->format("Y m d H:i:s");
        })
        ->editColumn('user_agent', function($each) {
            if($each->user_agent) {
                $agent = new Agent();
                $agent->setUserAgent($each->user_agent);
                $device = $agent->device();
                $platform = $agent->platform();
                $browser = $agent->browser();

                return  $platform ." platform <br>" . $browser . " browser";
            }
        })
        ->addColumn("actions", function($each) {
            $edit_btn = "<a class=' fs-4 text-warning' href='".route('admin.admin-user.edit', $each->id)."'><i class='pe-7s-note'></i></a>";
            $delete_btn = "<a class=' fs-4 text-danger delete_btn' href='#' data-id='".$each->id."'><i class='pe-7s-trash'></i></a>";

            return "<div>" . $edit_btn . $delete_btn . "</div>";
        })
        ->rawColumns(['actions','user_agent'])
        ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin_user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminUserRequest $request)
    {
        $admin = new AdminUser();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        $admin->password = Hash::make($request->password);
        $admin->save();

        return redirect()->route('admin.admin-user.index')->with('status', "Successfully admin user created");
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
        $admin = AdminUser::findOrFail($id);
        return view('backend.admin_user.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminUserRequest $request, string $id)
    {
        $admin = AdminUser::findOrFail($id);
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        $admin->password = $request->password ? Hash::make($request->password) : $admin->password;
        $admin->update();

        return redirect()->route('admin.admin-user.index')->with('status', "Successfully admin user updated");

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = AdminUser::findOrFail($id);
        $admin->delete();
        return "success";
    }
}
