<?php

namespace App\Http\Controllers\Cashback;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CashbackWalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CashbackAuthController extends Controller
{
    protected CashbackWalletService $walletService;

    public function __construct(CashbackWalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Register a new user account on Cashback Portal.
     */
    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên của bạn.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Địa chỉ email này đã được đăng ký tài khoản.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu tối thiểu phải từ 6 ký tự.',
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
        ]);

        $guestSubId = ($request->hasSession() ? $request->session()->get('cashback_sub_id') : null)
            ?? $request->cookie('cashback_sub_id')
            ?? ($request->cookies ? $request->cookies->get('cashback_sub_id') : null)
            ?? $request->input('guest_sub_id')
            ?? $request->header('X-Cashback-Sub-Id');

        Auth::login($user, true);

        // Get or create wallet and automatically merge any existing guest cookie wallet
        $wallet = $this->walletService->getOrCreateWallet($request, $guestSubId);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký tài khoản thành công! Số dư và lịch sử đã được lưu trữ an toàn.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'wallet' => [
                    'id' => $wallet->id,
                    'sub_id' => $wallet->sub_id,
                    'pending_balance' => (float) $wallet->pending_balance,
                    'available_balance' => (float) $wallet->available_balance,
                    'withdrawn_balance' => (float) $wallet->withdrawn_balance,
                ],
            ]);
        }

        return back()->with('success', 'Đăng ký tài khoản thành công!');
    }

    /**
     * Authenticate user with Email & Password.
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập email đăng nhập.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember', true))) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không chính xác.',
                ], 422);
            }

            return back()->withErrors([
                'email' => 'Email hoặc mật khẩu không chính xác.',
            ]);
        }

        $guestSubId = ($request->hasSession() ? $request->session()->get('cashback_sub_id') : null)
            ?? $request->cookie('cashback_sub_id')
            ?? ($request->cookies ? $request->cookies->get('cashback_sub_id') : null)
            ?? $request->input('guest_sub_id')
            ?? $request->header('X-Cashback-Sub-Id');

        $request->session()->regenerate();
        $user = Auth::user();

        // Get user wallet and merge guest history
        $wallet = $this->walletService->getOrCreateWallet($request, $guestSubId);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công! Chào mừng bạn quay trở lại.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'wallet' => [
                    'id' => $wallet->id,
                    'sub_id' => $wallet->sub_id,
                    'pending_balance' => (float) $wallet->pending_balance,
                    'available_balance' => (float) $wallet->available_balance,
                    'withdrawn_balance' => (float) $wallet->withdrawn_balance,
                    'default_bank_name' => $wallet->default_bank_name,
                    'default_bank_account_number' => $wallet->default_bank_account_number,
                    'default_bank_account_name' => $wallet->default_bank_account_name,
                ],
            ]);
        }

        return back()->with('success', 'Đăng nhập thành công!');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã đăng xuất thành công.',
            ]);
        }

        return back()->with('success', 'Đã đăng xuất thành công.');
    }

    /**
     * Save or update user default bank profile for faster withdrawals.
     */
    public function updateBankProfile(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_name' => ['required', 'string', 'max:100'],
        ], [
            'bank_name.required' => 'Vui lòng chọn ngân hàng.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản ngân hàng.',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản.',
        ]);

        $wallet = $this->walletService->getOrCreateWallet($request);
        $this->walletService->saveDefaultBank(
            $wallet,
            $validated['bank_name'],
            $validated['bank_account_number'],
            $validated['bank_account_name']
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật thông tin tài khoản ngân hàng mặc định.',
                'wallet' => [
                    'default_bank_name' => $wallet->default_bank_name,
                    'default_bank_account_number' => $wallet->default_bank_account_number,
                    'default_bank_account_name' => $wallet->default_bank_account_name,
                ],
            ]);
        }

        return back()->with('success', 'Đã cập nhật tài khoản ngân hàng mặc định.');
    }
}
