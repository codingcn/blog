<?php
/**
 * User: Alan
 * Date: 2017/9/18
 * Time: 10:02
 */
if (!function_exists('responseApi')) {
    /**
     * json响应数据
     * @param int $code 0为成功，其它为错误
     * @param string $message
     * @param array $data
     * @return array|string
     */
    function responseApi($code = 0, $message = '', $data = [])
    {
        if (!is_numeric($code)) {
            return '';
        }
        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        );
        return response()->json($result);
    }
}
if (!function_exists('getSetting')) {
    /**
     * @param string $key
     * @return mixed
     */
    function getSetting($key = '')
    {
        if (\Cache::has('settings')) {
            $settings = \Cache::get('settings');
        } else {
            $settings = \App\Models\Setting::get(['key', 'value'])->toArray();
            \Cache::forever('settings', $settings);
        }
        $value = '';
        foreach ($settings as $setting) {
            if ($setting['key'] == $key) {
                $value = $setting['value'];
            }
        }
        return $value;
    }
}