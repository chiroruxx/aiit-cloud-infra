<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 公開鍵のバリデーション・認可を行うリクエスト
 */
class PublicKeyRequest extends FormRequest
{
    // 認証を作成してないので、認可は必ず通す
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // `key` は必須パラメータ
            'key' => ['required']
        ];
    }
}
