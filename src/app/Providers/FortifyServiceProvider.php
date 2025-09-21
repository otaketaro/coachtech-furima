<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Fortify\ValidateLogin;                 // ★ 追加：FormRequest 検証用
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;     // ★ 追加：Fortify 標準処理
use Laravel\Fortify\Contracts\RegisterResponse; // ★ 追加：セッション確立
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ===== Fortify のレスポンスを差し替え（リダイレクト先の指定） =====

        // 登録成功時：/mypage/profile へ
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse
            {
                public function toResponse($request)
                {
                    return redirect('/mypage/profile');
                }
            };
        });

        // ログイン成功時：/ へ
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse
            {
                public function toResponse($request)
                {
                    return redirect('/');
                }
            };
        });

        // ログアウト後：/ へ
        $this->app->singleton(LogoutResponse::class, function () {
            return new class implements LogoutResponse
            {
                public function toResponse($request)
                {
                    return redirect('/');
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * ===== ビュー割り当て =====
         */
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));
        // 必要に応じて以下を有効化
        // Fortify::verifyEmailView(fn () => view('auth.verify-email'));
        // Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));
        // Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', ['request' => $request]));

        /**
         * ===== アクション差し替え =====
         */
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        /**
         * ===== レート制限 =====
         */
        RateLimiter::for('login', function (Request $request) {
            $key = Str::lower($request->input(Fortify::username())).'|'.$request->ip();

            return Limit::perMinute(5)->by($key);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by((string) $request->session()->get('login.id'));
        });

        /**
         * ===== ログインパイプラインを上書き =====
         * 最初に ValidateLogin（FormRequest ルール）を必ず通す
         */
        Fortify::authenticateThrough(function (Request $request) {
            return [
                ValidateLogin::class,           // ← まずバリデーション
                AttemptToAuthenticate::class,   // Fortify標準の認証試行
                PrepareAuthenticatedSession::class, // セッション確立
            ];
        });
    }
}
