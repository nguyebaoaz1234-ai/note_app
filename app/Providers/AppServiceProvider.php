<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL; // Thêm dòng này

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Fix lỗi độ dài chuỗi trong MySQL cũ
        Schema::defaultStringLength(191);

        // BẮT BUỘC DÙNG HTTPS KHI ĐƯA LÊN MẠNG (TIÊU CHÍ 28)
        // Nếu không chạy ở môi trường local (máy tính), thì tự động bật HTTPS
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }

    public function register()
    {
        //
    }
}