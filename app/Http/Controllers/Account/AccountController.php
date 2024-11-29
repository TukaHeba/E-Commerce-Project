<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Models\Account\Account;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\StoreAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;

class AccountController extends Controller
{
  
    protected AccountService $AccountService;

    public function __construct(AccountService $AccountService)
    {
        $this->AccountService = $AccountService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $accounts = $this->AccountService->getAccounts($request);
        return self::paginated($accounts, 'Accounts retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreAccountRequest $request): JsonResponse
    {
        $account = $this->AccountService->storeAccount($request->validated());
        return self::success($account, 'Account created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account): JsonResponse
    {
        return self::success($account, 'Account retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateAccountRequest $request, Account $account): JsonResponse
    {
        $updatedAccount = $this->AccountService->updateAccount($account, $request->validated());
        return self::success($updatedAccount, 'Account updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account): JsonResponse
    {
        $account->delete();
        return self::success(null, 'Account deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $accounts = Account::onlyTrashed()->get();
        return self::success($accounts, 'Accounts retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $account = Account::onlyTrashed()->findOrFail($id);
        $account->restore();
        return self::success($account, 'Account restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $account = Account::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'Account force deleted successfully');
    }
}
