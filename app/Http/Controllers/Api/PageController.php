<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ProfileResource;
use App\Notifications\GeneralNotification;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\NotificationResource;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\TransactionDetailResource;
use App\Http\Requests\TransferFormValidateRequest;
use App\Http\Resources\NotificationDetailResource;

class PageController extends Controller
{
    public function profile() {
        $user = Auth::user();
        $data = new ProfileResource($user);

        return success("success", $data);
    }

    public function transaction(Request $request) {
        $user = Auth::user();
        $transactions = Transaction::when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->when($request->date, function ($query) use ($request) {
            $query->whereDate('created_at', $request->date);
        })
            ->with('user', 'source')->orderBy('created_at', "DESC")->where('user_id', $user->id)->paginate(5);
        $data = TransactionResource::collection($transactions)->additional(["result" => 1, "message" => "success"]);
        return $data;
    }

    public function transactionDetail($trx_id) {
        $user = Auth::user();
        $transaction = Transaction::with('user', 'source')->where('user_id', $user->id)->where("trx_id", $trx_id)->first();
        $data = new TransactionDetailResource($transaction);

        return success("success", $data);
    }

    public function notification() {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(5);

        return NotificationResource::collection($notifications)->additional(["result" => 1, "message" => "success"]);
    }

    public function notificationDetail($id) {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();
        $data = new NotificationDetailResource($notification);
        return success("success", $data);
    }

    public function checkPhone(Request $request) {
        if($request->to_phone) {
            $authUser = Auth::user();
            $to_phone = $request->to_phone;
            $user = User::where("phone", $to_phone)->first();
            if ($authUser->phone != $to_phone) {
                if ($user) {
                    return success("success", ["name" => $user->name]);
                }
            }
        }

        return fail("Invalid phone number", null);

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
            return fail("Something wrong!", null);
        }

        $user = Auth::user();
        $to_user = User::where("phone", $to_phone)->first();

        if ($amount < 1000) {
            return fail("Amount must be greater than 1000 MMK", null);
        }

        if (!$user->wallet || !$to_user->wallet) {
            return fail("Something wrong!", null);
        }

        if ($user->wallet->amount < $amount) {
            return fail("Your money is not enough to transfer", null);
        }

        return success("success", [
            "from_name" => $user->name,
            "from_phone" => $user->phone,

            "to_name" => $to_user->name,
            "to_phone" => $to_user->phone,

            "amount" => $amount,
            "description" => $description,
            "hash_value" => $hash_value
        ]);
    }

    public function completeTransfer(TransferFormValidateRequest $request)
    {
        if (!$request->password) {
            return fail( "Please fill your password", null);
        }

        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            return fail("Invalid password", null);
        }

        $hash_value = $request->hash_value;
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;

        $str = $to_phone . $amount . $description;
        $hash_value2 = hash_hmac("sha256", $str, "magicpay12345#$@%00");

        if ($hash_value !== $hash_value2) {
            return fail("Something wrong!", null);
        }

        $user = Auth::user();
        $to_user = User::where("phone", $to_phone)->first();

        if ($amount < 1000) {
            return fail("Amount must be greater than 1000 MMK", null);
        }

        if (!$user->wallet || !$to_user->wallet) {
            return fail("Something wrong!", null);
        }

        if ($user->wallet->amount < $amount) {
            return fail("Your money is not enough to transfer", null);
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
            return success("Successfully transfered", ["trx_id" => $from_transaction->trx_id]);
        } catch (Exception $e) {
            DB::rollback();
            return fail("Something wrong!" . $e->getMessage(), null);

        }
    }

    public function scanAndPayForm(Request $request)
    {
        $to_phone = $request->to_phone;
        $user = Auth::user();
        $to_user = User::where("phone", $to_phone)->first();
        if (!$to_phone) {
            return fail("Invalid QR Code", null);
        }

        return success("success", [
            "from_name" => $user->name,
            "from_phone" => $user->phone,

            "to_name" => $to_user->name,
            "to_phone" => $to_user->phone
        ]);

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
            return fail("Something wrong!", null);
        }

        $user = Auth::user();
        $to_user = User::where("phone", $to_phone)->first();

        if ($amount < 1000) {
            return fail("Amount must be greater than 1000 MMK", null);
        }

        if (!$user->wallet || !$to_user->wallet) {
            return fail("Something wrong!", null);
        }

        if ($user->wallet->amount < $amount) {
            return fail("Your money is not enough to transfer", null);
        }

        return success("success", [
            "from_name" => $user->name,
            "from_phone" => $user->phone,

            "to_name" => $to_user->name,
            "to_phone" => $to_user->phone,

            "amount" => $amount,
            "description" => $description,
            "hash_value" => $hash_value
        ]);
    }

    public function scanAndPayComplete(TransferFormValidateRequest $request)
    {
        if (!$request->password) {
            return fail( "Please fill your password", null);
        }

        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            return fail("Invalid password", null);
        }

        $hash_value = $request->hash_value;
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;

        $str = $to_phone . $amount . $description;
        $hash_value2 = hash_hmac("sha256", $str, "magicpay12345#$@%00");

        if ($hash_value !== $hash_value2) {
            return fail("Something wrong!", null);
        }

        $user = Auth::user();
        $to_user = User::where("phone", $to_phone)->first();

        if ($amount < 1000) {
            return fail("Amount must be greater than 1000 MMK", null);
        }

        if (!$user->wallet || !$to_user->wallet) {
            return fail("Something wrong!", null);
        }

        if ($user->wallet->amount < $amount) {
            return fail("Your money is not enough to transfer", null);
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
            return success("Successfully transfered", ["trx_id" => $from_transaction->trx_id]);
        } catch (Exception $e) {
            DB::rollback();
            return fail("Something wrong!" . $e->getMessage(), null);

        }
    }
}
