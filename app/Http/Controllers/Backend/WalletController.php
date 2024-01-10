<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class WalletController extends Controller
{
    public function index()
    {
        return view('backend.wallet.index');
    }

    public function ssd()
    {
        $data = Wallet::with('user');
        return DataTables::of($data)
            ->editColumn('created_at', function ($each) {
                return   Carbon::parse($each->created_at)->format("Y m d H:i:s");
            })
            ->editColumn('updated_at', function ($each) {
                return   Carbon::parse($each->created_at)->format("Y m d H:i:s");
            })
            ->addColumn("account_person", function ($each) {
                $user = $each->user;
                if ($user) {
                    return "<p class='mb-0'>Name - {$user->name}</p> <p class='mb-0'>Email - {$user->email}</p> <p class='mb-0'>Phone - {$user->phone}</p>";
                }
                return "-";
            })
            ->editColumn("amount", function ($each) {
                return number_format($each->amount, 2);
            })
            ->rawColumns(['account_person'])
            ->make(true);
    }

    public function addAmount()
    {
        $users = User::all();
        return view('backend.wallet.add_amount', compact('users'));
    }

    public function addAmountStore(Request $request)
    {
        $request->validate(
            [
                "user_id" => "required",
                "amount" => "required",
            ],
            [
                "user_id.required" => "The user is selected please."
            ]
        );

        if ($request->amount < 1000) {
            return redirect()->back()->withErrors(['amount' => "Amount must be greater than 1000 MMK"])->withInput();
        }

        try {
            DB::beginTransaction();

            $to_user = User::with("wallet")->where("id", $request->user_id)->firstOrFail();
            $to_wallet = $to_user->wallet;
            $to_wallet->increment('amount', $request->amount);
            $to_wallet->update();

            $refNo = UUIDGenerate::refNo();
            $to_transaction = new Transaction();
            $to_transaction->ref_no = $refNo;
            $to_transaction->trx_id = UUIDGenerate::trxId();
            $to_transaction->user_id = $to_user->id;
            $to_transaction->type = 1;
            $to_transaction->amount = $request->amount;
            $to_transaction->source_id = 0;
            $to_transaction->description = $request->description;
            $to_transaction->save();
            DB::commit();

            return redirect()->route("admin.wallet.index");
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['fail' => "Something wrong!"])->withInput();
        }
    }

    public function reduceAmount()
    {
        $users = User::all();
        return view('backend.wallet.reduce_amount', compact('users'));
    }

    public function reduceAmountStore(Request $request)
    {
        $request->validate(
            [
                "user_id" => "required",
                "amount" => "required",
            ],
            [
                "user_id.required" => "The user is selected please."
            ]
        );

        if ($request->amount < 50) {
            return redirect()->back()->withErrors(['amount' => "Amount must be greater than 50 MMK"])->withInput();
        }

        try {
            DB::beginTransaction();

            $to_user = User::with("wallet")->where("id", $request->user_id)->firstOrFail();
            $to_wallet = $to_user->wallet;
            if($request->amount > $to_wallet->amount) {
                throw new Exception("Amount is not enough to reduce!");
            }
            $to_wallet->decrement('amount', $request->amount);
            $to_wallet->update();

            $refNo = UUIDGenerate::refNo();
            $to_transaction = new Transaction();
            $to_transaction->ref_no = $refNo;
            $to_transaction->trx_id = UUIDGenerate::trxId();
            $to_transaction->user_id = $to_user->id;
            $to_transaction->type = 2;
            $to_transaction->amount = $request->amount;
            $to_transaction->source_id = 0;
            $to_transaction->description = $request->description;
            $to_transaction->save();
            DB::commit();

            return redirect()->route("admin.wallet.index");
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['fail' => "Something wrong! " . $e->getMessage()])->withInput();
        }
    }
}
