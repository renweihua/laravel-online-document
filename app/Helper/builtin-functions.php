<?php
/**
 *
 *
 * 针对于laravel的方法函数
 *
 *
 */

//快速修改.env文件
function modifyEnv(array $data)
{
    $envPath      = base_path() . DIRECTORY_SEPARATOR . '.env';
    $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));
    $contentArray->transform(function ($item) use ($data)
    {
        foreach ($data as $key => $value) {
            if (str_contains($item, $key)) {
                return $key . '=' . $value;
            }
        }
        return $item;
    });
    $content = implode($contentArray->toArray(), "\n");
    \Illuminate\Support\Facades\File::put($envPath, $content);
}

// 获取数据表的前缀
function get_db_prefix()
{
    return config('database.connections.' . config('database.default') . '.prefix');
}
