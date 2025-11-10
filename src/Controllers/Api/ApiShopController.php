<?php

namespace Azuriom\Plugin\ApiExtender\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\ApiExtender\Middleware\VerifyApiKey;
use Illuminate\Http\Request;
use Azuriom\Plugin\Shop\Models\Category;
use Azuriom\Plugin\Shop\Models\Payment;
use Azuriom\Plugin\Shop\Models\Giftcard;
use Illuminate\Support\Facades\Validator;

class ApiShopController extends Controller
{
    public function __construct()
    {
        $this->middleware(VerifyApiKey::class);
    }

    public function payments(Request $request)
    {
        $perPage = (int) $request->input('per_page', 50);
        $perPage = max(1, min($perPage, 100));

        $payments = Payment::query()
            ->with([
                'user:id,name',
                'items' => function ($q) {
                    $q->select([
                        'id', 'payment_id', 'name', 'price', 'quantity', 'buyable_type', 'buyable_id', 'expires_at', 'created_at',
                    ]);
                },
            ])
            ->latest('created_at')
            ->paginate($perPage, [
                'id', 'user_id', 'price', 'currency', 'status', 'gateway_type', 'transaction_id', 'created_at',
            ]);

        $payments->getCollection()->each(function ($payment) {
            $payment->items->each(function ($item) {
                $item->unsetRelation('payment');
            });
        });

        return response()->json($payments);
    }


    public function categories()
    {
        $categories = Category::query()
            ->enabled()
            ->parents()
            ->with([
                'categories' => fn ($q) => $q->enabled(),
                'packages' => fn ($q) => $q->enabled()->select([
                    'id', 'category_id', 'name', 'short_description', 'price', 'billing_type', 'is_enabled', 'position',
                ]),
            ])
            ->get([
                'id', 'name', 'slug', 'description', 'icon', 'position', 'parent_id', 'is_enabled',
            ]);

        return response()->json([
            'categories' => $categories,
        ]);
    }

    public function giftcard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'balance' => ['required', 'numeric', 'min:0.01'],
            'code' => ['nullable', 'string', 'max:255', 'unique:shop_giftcards,code'],
            'start_at' => ['nullable', 'date'],
            'expire_at' => ['nullable', 'date', 'after:start_at'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $code = $request->input('code') ?? Giftcard::randomCode();
        $balance = (float) $request->input('balance');
        $startAt = $request->input('start_at', now());
        $expireAt = $request->input('expire_at');

        $giftcard = Giftcard::create([
            'code' => $code,
            'balance' => $balance,
            'original_balance' => $balance,
            'start_at' => $startAt,
            'expire_at' => $expireAt,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Giftcard created successfully',
            'giftcard' => [
                'id' => $giftcard->id,
                'code' => $giftcard->code,
                'balance' => $giftcard->balance,
                'original_balance' => $giftcard->original_balance,
                'start_at' => $giftcard->start_at,
                'expire_at' => $giftcard->expire_at,
                'shareable_link' => $giftcard->shareableLink(),
                'created_at' => $giftcard->created_at,
            ],
        ], 201);
    }
}



