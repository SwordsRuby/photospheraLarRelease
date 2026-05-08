<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Services\YooKassaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use YooKassa\Model\Payment\PaymentStatus;

class SubscriptionController extends Controller
{
    protected $yooKassaService;

    /**
     * SubscriptionController constructor.
     *
     * @param YooKassaService $yooKassaService
     */
    public function __construct(YooKassaService $yooKassaService)
    {
        $this->yooKassaService = $yooKassaService;
    }

    /**
     * Display subscription plans page.
     *
     * @return \Illuminate\View\View
     */
    public function plans()
    {
        $currentSubscription = Auth::user()->active_subscription;
        $plans = Subscription::PLANS;

        return view('subscription.plans', compact('plans', 'currentSubscription'));
    }

    /**
     * Display user's current subscription and payment history.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $subscription = Auth::user()->active_subscription;
        $payments = Auth::user()->payments()
            ->latest()
            ->paginate(10);

        return view('subscription.index', compact('subscription', 'payments'));
    }

    /**
     * Show checkout page for selected plan.
     *
     * @param string $plan
     * @return \Illuminate\View\View
     */
    public function checkout($plan)
    {
        $plans = Subscription::PLANS;

        if (!isset($plans[$plan])) {
            abort(404);
        }

        $selectedPlan = $plans[$plan];

        return view('subscription.checkout', compact('selectedPlan', 'plan'));
    }

    /**
     * Process payment creation with YooKassa.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,pro,premium',
            'duration_months' => 'required|integer|min:1|max:12',
        ]);

        $plan = $request->plan;
        $durationMonths = (int) $request->duration_months;
        $plans = Subscription::PLANS;

        if ($plan === 'basic') {
            return redirect()->route('subscription.index')
                ->with('error', 'Базовый тариф бесплатный, оформление подписки не требуется');
        }

        $amount = $plans[$plan]['price'] * $durationMonths;
        $description = "Подписка {$plans[$plan]['name']} на {$durationMonths} мес.";
        $paymentId = 'payment_' . time() . '_' . uniqid();
        $returnUrl = route('subscription.success', ['payment_id' => $paymentId]);

        try {
            $payment = $this->yooKassaService->createPayment(
                $amount,
                $description,
                $paymentId,
                Auth::user()->email,
                $returnUrl
            );

            $dbPayment = Payment::create([
                'user_id' => Auth::id(),
                'amount' => $amount,
                'currency' => 'RUB',
                'payment_method' => 'yookassa',
                'transaction_id' => $paymentId,
                'yookassa_payment_id' => $payment->getId(),
                'payment_url' => $payment->getConfirmation()->getConfirmationUrl(),
                'status' => Payment::STATUS_PENDING,
                'plan' => $plan,
                'duration_months' => $durationMonths,
            ]);

            session(['last_yookassa_payment_id' => $payment->getId()]);
            session(['last_payment_transaction_id' => $paymentId]);

            return redirect()->away($payment->getConfirmation()->getConfirmationUrl());

        } catch (\Exception $e) {
            Log::error('YooKassa payment creation failed: ' . $e->getMessage());

            return redirect()->route('subscription.plans')
                ->with('error', 'Ошибка при создании платежа: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment success callback from YooKassa.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paymentSuccess(Request $request)
    {
        $transactionId = $request->input('payment_id');

        Log::info('Payment success callback', [
            'all_params' => $request->all(),
            'transaction_id' => $transactionId
        ]);

        $dbPayment = null;

        if ($transactionId) {
            $dbPayment = Payment::where('transaction_id', $transactionId)->first();
        }

        if (!$dbPayment && session('last_yookassa_payment_id')) {
            $dbPayment = Payment::where('yookassa_payment_id', session('last_yookassa_payment_id'))->first();
        }

        if (!$dbPayment) {
            $dbPayment = Payment::where('user_id', Auth::id())
                ->where('status', Payment::STATUS_PENDING)
                ->latest()
                ->first();
        }

        if (!$dbPayment) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Не удалось найти информацию о платеже. Обратитесь в поддержку.');
        }

        try {
            $payment = $this->yooKassaService->getPayment($dbPayment->yookassa_payment_id);

            Log::info('YooKassa payment status', [
                'status' => $payment->getStatus(),
                'yookassa_payment_id' => $dbPayment->yookassa_payment_id
            ]);

            // Use string comparison instead of constant
            if ($payment->getStatus() === 'succeeded') {
                if ($dbPayment->status !== Payment::STATUS_SUCCEEDED) {
                    $dbPayment->update(['status' => Payment::STATUS_SUCCEEDED]);

                    $this->activateSubscription(
                        $dbPayment->plan,
                        $dbPayment->duration_months,
                        $dbPayment->id
                    );

                    session()->forget(['last_yookassa_payment_id', 'last_payment_transaction_id']);

                    return redirect()->route('subscription.index')
                        ->with('success', 'Оплата прошла успешно! Подписка активирована.');
                }
            } elseif ($payment->getStatus() === 'pending') {
                return redirect()->route('subscription.plans')
                    ->with('warning', 'Платеж в обработке. Если средства списались, подписка активируется автоматически в течение нескольких минут.');
            } else {
                return redirect()->route('subscription.plans')
                    ->with('error', 'Платеж не был завершен. Статус: ' . $payment->getStatus());
            }

            return redirect()->route('subscription.plans')
                ->with('error', 'Платеж не был завершен. Попробуйте снова.');

        } catch (\Exception $e) {
            Log::error('Payment success handling failed: ' . $e->getMessage());

            if (config('yookassa.test_mode') && $dbPayment->status === Payment::STATUS_PENDING) {
                $dbPayment->update(['status' => Payment::STATUS_SUCCEEDED]);

                $this->activateSubscription(
                    $dbPayment->plan,
                    $dbPayment->duration_months,
                    $dbPayment->id
                );

                session()->forget(['last_yookassa_payment_id', 'last_payment_transaction_id']);

                return redirect()->route('subscription.index')
                    ->with('success', 'Тестовый платеж прошел успешно! Подписка активирована.');
            }

            return redirect()->route('subscription.plans')
                ->with('error', 'Ошибка при обработке платежа: ' . $e->getMessage());
        }
    }

    /**
     * Manually check payment status.
     *
     * @param string $paymentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkPaymentStatus($paymentId)
    {
        $payment = Payment::where('id', $paymentId)
            ->orWhere('transaction_id', $paymentId)
            ->orWhere('yookassa_payment_id', $paymentId)
            ->first();

        if (!$payment) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Платеж не найден');
        }

        try {
            $yookassaPayment = $this->yooKassaService->getPayment($payment->yookassa_payment_id);
            $status = $yookassaPayment->getStatus();

            if ($status === 'succeeded' && $payment->status !== Payment::STATUS_SUCCEEDED) {
                $payment->update(['status' => Payment::STATUS_SUCCEEDED]);

                $this->activateSubscription(
                    $payment->plan,
                    $payment->duration_months,
                    $payment->id
                );

                return redirect()->route('subscription.index')
                    ->with('success', 'Платеж подтвержден! Подписка активирована.');
            }

            return redirect()->route('subscription.plans')
                ->with('info', 'Статус платежа: ' . $status);

        } catch (\Exception $e) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Ошибка при проверке платежа: ' . $e->getMessage());
        }
    }

    /**
     * Activate subscription for user.
     *
     * @param string $plan
     * @param int $durationMonths
     * @param int $paymentId
     * @return void
     */
    private function activateSubscription($plan, $durationMonths, $paymentId)
    {
        Subscription::where('user_id', Auth::id())->update(['is_active' => false]);

        $plans = Subscription::PLANS;
        $storageBytes = $plans[$plan]['storage_gb'] * 1073741824;

        $expiresAt = $durationMonths > 0 ? Carbon::now()->addMonths($durationMonths) : null;

        $subscription = Subscription::create([
            'user_id' => Auth::id(),
            'plan' => $plan,
            'storage_limit' => $storageBytes,
            'storage_used' => 0,
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);

        Payment::where('id', $paymentId)->update(['subscription_id' => $subscription->id]);

        Log::info('Subscription activated', [
            'user_id' => Auth::id(),
            'plan' => $plan,
            'expires_at' => $expiresAt
        ]);
    }

    /**
     * Cancel user's current subscription.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel()
    {
        $subscription = Auth::user()->active_subscription;

        if ($subscription && $subscription->plan !== 'basic') {
            Auth::user()->createBasicSubscription();

            return redirect()->route('subscription.index')
                ->with('success', 'Подписка отменена. Вы переведены на базовый тариф.');
        }

        return redirect()->route('subscription.index')
            ->with('error', 'Невозможно отменить подписку');
    }
}