<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\UUIDGenerate;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUpdatePasswordRequest;
use App\Http\Requests\TransferFormValidateRequest;
use App\Models\Transaction;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class PageController extends Controller

{
    public function home()
    {

        $user = Auth::user();
        return view('frontend.home', compact('user'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('frontend.profile', compact('user'));
    }

    public function updatePassword()
    {
        return view('frontend.update_password');
    }

    public function updatePasswordStore(StoreUpdatePasswordRequest $request)
    {

        $old_password = $request->old_password;
        $new_password = $request->new_password;
        $user = Auth::user();
        if (Hash::check($old_password, $user->password)) {
            $user->password = $new_password;
            $user->update();

            $title = "Changed Password!";
            $message = "Your account password is changed successfully.";
            $sourceable_id = $user->id;
            $sourceable_type = User::class;
            $web_link = route('profile');
            $deep_link = [
                "target" => "profile",
                "parameter" => null
            ];

            Notification::send($user, new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link, $deep_link));

            return redirect()->route('profile')->with('status', 'Successfully password updated');
        }

        return back()->withErrors(["fail" => "The old password is not correct"])->withInput();
    }

    public function wallet()
    {
        $user = Auth::user();
        return view('frontend.wallet', compact('user'));
    }

    public function transfer()
    {
        $user = Auth::user();
        return view('frontend.transfer', compact('user'));
    }
    public function confirmTransfer(TransferFormValidateRequest $request)
    {

        $hash_value = $request->hash_value;
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;

        $str = $to_phone . $amount . $description;
        $hash_value2 = hash_hmac("sha256", $str, "magicpay12345#$@%00");

        if ($hash_value !== $hash_value2) {
            return redirect()->back()->withErrors(['fail' => "Something wrong!"])->withInput();
        }

        $user = Auth::user();
        $to_user = User::where("phone", $to_phone)->first();

        if ($amount < 1000) {
            return redirect()->back()->withErrors(['amount' => "Amount must be greater than 1000 MMK"])->withInput();
        }

        if (!$user->wallet || !$to_user->wallet) {
            return redirect()->back()->withErrors(['fail' => "Something wrong!"])->withInput();
        }

        if ($user->wallet->amount < $amount) {
            return redirect()->back()->withErrors(['amount' => "Your money is not enough to transfer"])->withInput();
        }

        return view('frontend.confirm_transfer', compact('user', 'to_user', 'amount', 'description', 'hash_value'));
    }

    public function completeTransfer(TransferFormValidateRequest $request)
    {

        $hash_value = $request->hash_value;
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;

        $str = $to_phone . $amount . $description;
        $hash_value2 = hash_hmac("sha256", $str, "magicpay12345#$@%00");

        if ($hash_value !== $hash_value2) {
            return redirect()->back()->withErrors(['fail' => "Something wrong!"])->withInput();
        }

        $user = Auth::user();
        $to_user = User::where("phone", $to_phone)->first();

        if ($amount < 1000) {
            return redirect()->back()->withErrors(['fail' => "Amount must be greater than 1000 MMK"])->withInput();
        }

        if ($user->phone == $to_phone) {
            return redirect()->back()->withErrors(['fail' => "Invalid phone number"])->withInput();
        }

        if (!$user->wallet || !$to_user->wallet) {
            return redirect()->back()->withErrors(['fail' => "Something wrong!"])->withInput();
        }

        if ($user->wallet->amount < $amount) {
            return redirect()->back()->withErrors(['amount' => "Your money is not enough to transfer"])->withInput();
        }

        try {
            DB::beginTransaction();
            $from_wallet = $user->wallet;
            $from_wallet->decrement('amount', $amount);
            $from_wallet->update();

            $to_wallet = $to_user->wallet;
            $to_wallet->increment('amount', $amount);
            $to_wallet->update();

            $refNo = UUIDGenerate::refNo();
            $from_transaction = new Transaction();
            $from_transaction->ref_no = $refNo;
            $from_transaction->trx_id = UUIDGenerate::trxId();
            $from_transaction->user_id = $user->id;
            $from_transaction->type = 2;
            $from_transaction->amount = $amount;
            $from_transaction->source_id = $to_user->id;
            $from_transaction->description = $description;
            $from_transaction->save();

            $to_transaction = new Transaction();
            $to_transaction->ref_no = $refNo;
            $to_transaction->trx_id = UUIDGenerate::trxId();
            $to_transaction->user_id = $to_user->id;
            $to_transaction->type = 1;
            $to_transaction->amount = $amount;
            $to_transaction->source_id = $user->id;
            $to_transaction->description = $description;
            $to_transaction->save();

            // From Noti
            $title = "Transfered!";
            $message = "Your wallet transfered " . number_format($amount) . " MMK to " . $to_user->name . " (" . $to_user->phone . ")";
            $sourceable_id = $from_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url("/transaction-detail/{$from_transaction->trx_id}");
            $deep_link = [
                "target" => "transaction-detail",
                "parameter" => $from_transaction->trx_id
            ];

            Notification::send($user, new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link, $deep_link));

            // To Noti
            $title = "Received!";
            $message = "Your wallet received " . number_format($amount) . " MMK from " . $user->name . " (" . $user->phone . ")";
            $sourceable_id = $to_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url("/transaction-detail/{$to_transaction->trx_id}");
            $deep_link = [
                "target" => "transaction-detail",
                "parameter" => $to_transaction->trx_id
            ];

            Notification::send($to_user, new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link, $deep_link));


            DB::commit();
            return redirect("/transaction-detail/{$from_transaction->trx_id}");
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['fail' => "Something wrong!"])->withInput();
        }
    }

    public function checkPhone(Request $request)
    {
        $authUser = Auth::user();
        $to_phone = $request->to_phone;
        $user = User::where("phone", $to_phone)->first();
        if ($authUser->phone != $to_phone) {
            if ($user) {
                return response()->json([
                    "status" => "success",
                    "data" => $user,
                ]);
            }
        }

        return response()->json([
            "status" => "fail",
            "message" => "Invalid phone number",
        ]);
    }

    public function checkPassword(Request $request)
    {

        if (!$request->password) {
            return response()->json([
                "status" => "fail",
                "message" => "Please fill your password",
            ]);
        }

        $authUser = Auth::user();
        if (Hash::check($request->password, $authUser->password)) {
            return response()->json([
                "status" => "success",
                "message" => "Correct password",
            ]);
        }

        return response()->json([
            "status" => "fail",
            "message" => "Invalid password",
        ]);
    }

    public function transaction(Request $request)
    {
        $user = Auth::user();
        $transactions = Transaction::when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->when($request->date, function ($query) use ($request) {
            $query->whereDate('created_at', $request->date);
        })
            ->with('user', 'source')->orderBy('created_at', "DESC")->where('user_id', $user->id)->paginate(4);
        return view('frontend.transaction', compact('transactions'));
    }

    public function transactionDetail(Request $request)
    {
        $user = Auth::user();
        $transaction = Transaction::with('user', 'source')->where('user_id', $user->id)->where("trx_id", $request->trx_id)->first();
        return view('frontend.transaction_detail', compact('transaction'));
    }

    public function transferHash(Request $request)
    {
        $str = $request->to_phone . $request->amount . $request->description;
        $hash_value = hash_hmac("sha256", $str, "magicpay12345#$@%00");

        return response()->json([
            "status" => "success",
            "hash_value" => $hash_value,
        ]);
    }

    public function receiveQR()
    {
        $user = Auth::user();
        return view('frontend.receive_qr', compact('user'));
    }

    public function scanAndPay()
    {
        return view('frontend.scan_and_pay');
    }

    public function scanAndPayForm(Request $request)
    {
        $to_phone = $request->to_phone;
        $user = Auth::user();
        $to_user = User::where("phone", $to_phone)->first();
        if (!$to_phone) {
            return redirect()->back()->withErrors(['fail' => "Invalid QR Code"])->withInput();
        }

        return view('frontend.scan_and_pay_form', compact('user', 'to_user'));
    }

    public function scanAndPayConfrim(TransferFormValidateRequest $request)
    {

        $hash_value = $request->hash_value;
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;

        $str = $to_phone . $amount . $description;
        $hash_value2 = hash_hmac("sha256", $str, "magicpay12345#$@%00");

        if ($hash_value !== $hash_value2) {
            return redirect()->back()->withErrors(['fail' => "Something wrong!"])->withInput();
        }

        $user = Auth::user();
        $to_user = User::where("phone", $to_phone)->first();

        if ($amount < 1000) {
            return redirect()->back()->withErrors(['amount' => "Amount must be greater than 1000 MMK"])->withInput();
        }

        if (!$user->wallet || !$to_user->wallet) {
            return redirect()->back()->withErrors(['fail' => "Something wrong!"])->withInput();
        }

        if ($user->wallet->amount < $amount) {
            return redirect()->back()->withErrors(['amount' => "Your money is not enough to transfer"])->withInput();
        }

        return view('frontend.scan_and_pay_confirm', compact('user', 'to_user', 'amount', 'description', 'hash_value'));
    }

    public function scanAndPayComplete(TransferFormValidateRequest $request)
    {

        $hash_value = $request->hash_value;
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;

        $str = $to_phone . $amount . $description;
        $hash_value2 = hash_hmac("sha256", $str, "magicpay12345#$@%00");

        if ($hash_value !== $hash_value2) {
            return redirect()->back()->withErrors(['fail' => "Something wrong!"])->withInput();
        }

        $user = Auth::user();
        $to_user = User::where("phone", $to_phone)->first();

        if ($amount < 1000) {
            return redirect()->back()->withErrors(['fail' => "Amount must be greater than 1000 MMK"])->withInput();
        }

        if ($user->phone == $to_phone) {
            return redirect()->back()->withErrors(['fail' => "Invalid phone number"])->withInput();
        }

        if (!$user->wallet || !$to_user->wallet) {
            return redirect()->back()->withErrors(['fail' => "Something wrong!"])->withInput();
        }

        if ($user->wallet->amount < $amount) {
            return redirect()->back()->withErrors(['amount' => "Your money is not enough to transfer"])->withInput();
        }

        try {
            DB::beginTransaction();
            $from_wallet = $user->wallet;
            $from_wallet->decrement('amount', $amount);
            $from_wallet->update();

            $to_wallet = $to_user->wallet;
            $to_wallet->increment('amount', $amount);
            $to_wallet->update();

            $refNo = UUIDGenerate::refNo();
            $from_transaction = new Transaction();
            $from_transaction->ref_no = $refNo;
            $from_transaction->trx_id = UUIDGenerate::trxId();
            $from_transaction->user_id = $user->id;
            $from_transaction->type = 2;
            $from_transaction->amount = $amount;
            $from_transaction->source_id = $to_user->id;
            $from_transaction->description = $description;
            $from_transaction->save();

            $to_transaction = new Transaction();
            $to_transaction->ref_no = $refNo;
            $to_transaction->trx_id = UUIDGenerate::trxId();
            $to_transaction->user_id = $to_user->id;
            $to_transaction->type = 1;
            $to_transaction->amount = $amount;
            $to_transaction->source_id = $user->id;
            $to_transaction->description = $description;
            $to_transaction->save();

            // From Noti
            $title = "Transfered!";
            $message = "Your wallet transfered " . number_format($amount) . " MMK to " . $to_user->name . " (" . $to_user->phone . ")";
            $sourceable_id = $from_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url("/transaction-detail/{$from_transaction->trx_id}");
            $deep_link = [
                "target" => "transaction-detail",
                "parameter" => $from_transaction->trx_id
            ];

            Notification::send($user, new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link, $deep_link));

            // To Noti
            $title = "Received!";
            $message = "Your wallet received " . number_format($amount) . " MMK from " . $user->name . " (" . $user->phone . ")";
            $sourceable_id = $to_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url("/transaction-detail/{$to_transaction->trx_id}");
            $deep_link = [
                "target" => "transaction-detail",
                "parameter" => $to_transaction->trx_id
            ];
            Notification::send($to_user, new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link, $deep_link));


            DB::commit();
            return redirect("/transaction-detail/{$from_transaction->trx_id}");
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['fail' => "Something wrong!"])->withInput();
        }
    }
}
