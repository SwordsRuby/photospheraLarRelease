<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Handle user login attempt.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = [
            'login' => $request->loginAuth,
            'password' => $request->passwordAuth
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return redirect()->back()
                ->withErrors(['loginAuth' => 'Неверный логин или пароль'])
                ->withInput($request->only('loginAuth'))
                ->with('showAuthModal', true);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            Auth::logout();

            $code = $user->generateVerificationCode();

            try {
                Mail::to($user->email)->send(new VerificationCodeMail($code, $user->login));
            } catch (\Exception $e) {
                Log::error('Mail sending failed: ' . $e->getMessage());
            }

            return redirect()->route('verification.show', $user->id)
                ->with('warning', 'Подтвердите регистрацию перед входом. Код отправлен на вашу почту.');
        }

        if ($user->is_banned) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->back()
                ->withErrors(['loginAuth' => 'Ваш аккаунт забанен, обратитесь в поддержку'])
                ->withInput($request->only('loginAuth'))
                ->with('showAuthModal', true);
        }

        if ($user->is_moderator) {
            return redirect()->route('admin.images');
        }

        return redirect()->route('home');
    }

    /**
     * Handle new user registration.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'login' => $request->loginRegister,
                'email' => $request->emailRegister,
                'password' => bcrypt($request->passwordRegister),
                'img' => '/img/users/default-user-icon.png',
                'is_moderator' => false,
                'is_banned' => false,
            ]);

            $code = $user->generateVerificationCode();

            Mail::to($user->email)->send(new VerificationCodeMail($code, $user->login));

            return redirect()->route('verification.show', $user->id)
                ->with('success', 'Код подтверждения отправлен на вашу почту');

        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());

            if (isset($user) && $user->exists) {
                $user->delete();
            }

            return redirect()->back()
                ->withErrors(['emailRegister' => 'Не удалось отправить письмо. Проверьте почту.'])
                ->withInput()
                ->with('showRegModal', true);
        }
    }

    /**
     * Log out the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Display the email verification form.
     *
     * @param int $userId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showVerificationForm($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->hasVerifiedEmail()) {
            Auth::login($user);

            if ($user->is_moderator) {
                return redirect()->route('admin.images');
            }
            return redirect()->route('home');
        }

        return view('auth.verify', compact('userId', 'user'));
    }

    /**
     * Verify the user's email with the provided code.
     *
     * @param Request $request
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyCode(Request $request, $userId)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($userId);

        if ($user->verifyCode($request->verification_code)) {
            Auth::login($user);

            if ($user->is_moderator) {
                return redirect()->route('admin.images')->with('success', 'Почта успешно подтвержден!');
            }

            return redirect()->route('home')->with('success', 'Почта успешно подтверждена!');
        }

        return back()->with('error', 'Неверный или просроченный код подтверждения');
    }

    /**
     * Resend the verification code to the user's email.
     *
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendCode($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->hasVerifiedEmail()) {
            Auth::login($user);
            return redirect()->route('home')->with('success', 'Почта уже подтверждена');
        }

        try {
            $code = $user->generateVerificationCode();
            Mail::to($user->email)->send(new VerificationCodeMail($code, $user->login));
            return back()->with('success', 'Новый код подтверждения отправлен на вашу почту');
        } catch (\Exception $e) {
            Log::error('Mail resend failed: ' . $e->getMessage());
            return back()->with('error', 'Не удалось отправить письмо. Попробуйте позже.');
        }
    }
}