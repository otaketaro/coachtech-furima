<?php

namespace App\Actions\Fortify;

use App\Http\Requests\LoginRequest;
use Closure;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Http\Request;

class ValidateLogin
{
    public function __construct(private ValidationFactory $validator) {}

    /**
     * Fortify の認証パイプライン先頭で実行。
     * LoginRequest の rules/messages/attributes で手動バリデーションしてから次へ。
     */
    public function __invoke(Request $request, Closure $next)
    {
        $form = app(LoginRequest::class);

        $this->validator
            ->make(
                $request->all(),
                $form->rules(),
                method_exists($form, 'messages') ? $form->messages() : [],
                method_exists($form, 'attributes') ? $form->attributes() : []
            )
            ->validate();

        return $next($request); // ← これが重要。次の処理へ渡す
    }
}
