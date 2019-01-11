<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use JWTAuth;

class TransactionController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $transaction = $this->user->transactions();
        if ($request->opt == 'in') {
            $transaction = $transaction->where('type', \App\TransactionType::where('name', 'in')->first()->id);
        } elseif ($request->opt == 'out') {
            $transaction = $transaction->where('type', \App\TransactionType::where('name', 'out')->first()->id);
        }

        if ($request->srt == 'new') {
            $transaction = $transaction->latest();
        } elseif ($request->srt == 'old') {
            $transaction = $transaction->oldest();
        }

        return response()->json($transaction->get(['user', 'type', 'amount','message','created_at','updated_at']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required',
            'amount' => 'required'
        ]);
        $transferee = \App\User::getPhoneId($request->phone);
        if ($transferee && $request->amout >= 1 && $request->amount <=1000000) {
            $out = new Transaction();
            $out->type = \App\TransactionType::where('name', 'out')->first()->id;
            $out->amount = $request->amount;
            $out->message = $request->message;
            if ($this->user->transactions()->save($out)) {
                $in = new Transaction();
                $in->user = $transferee->id;
                $in->type = \App\TransactionType::where('name', 'in')->first()->id;
                $in->amount = $request->amount;
                $in->message = $request->message;
                if ($transferee->transactions()->save($in)) {
                    return response()->json([
                        'success' => true,
                        'transaction' => $out
                    ]);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, transfer could not be added'
                ], 500);
            }
            return response()->json([
                'success' => false,
                'message' => 'Sorry, transfer could not be added'
            ], 500);
        }
        return response()->json([
            'success' => false,
            'message' => 'Sorry, transfer could not be added'
        ], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = $this->user->transactions()->find($id);
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, Transaction not available'
            ], 400);
        }
        return response()->json($transaction);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
