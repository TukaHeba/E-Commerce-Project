<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Models\Transaction\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
  
    protected TransactionService $TransactionService;

    public function __construct(TransactionService $TransactionService)
    {
        $this->TransactionService = $TransactionService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $transactions = $this->TransactionService->getTransactions($request);
        return self::paginated($transactions, 'Transactions retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->TransactionService->storeTransaction($request->validated());
        return self::success($transaction, 'Transaction created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction): JsonResponse
    {
        return self::success($transaction, 'Transaction retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $updatedTransaction = $this->TransactionService->updateTransaction($transaction, $request->validated());
        return self::success($updatedTransaction, 'Transaction updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();
        return self::success(null, 'Transaction deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $transactions = Transaction::onlyTrashed()->get();
        return self::success($transactions, 'Transactions retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $transaction = Transaction::onlyTrashed()->findOrFail($id);
        $transaction->restore();
        return self::success($transaction, 'Transaction restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $transaction = Transaction::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'Transaction force deleted successfully');
    }
}
