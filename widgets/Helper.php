<?php

namespace app\widgets;

use app\backend\BackendModule;
use app\components\BaseModule;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use kartik\icons\Icon;

class Helper
{
    private static $returnUrl;
    public static $returnUrlWithoutHistory = false;

    /**
     * @return string
     */
    public static function getReturnUrl()
    {
        if (is_null(self::$returnUrl)) {
            $url = parse_url(Yii::$app->request->url);
            $returnUrlParams = [];
            if (isset($url['query'])) {
                $parts = explode('&', $url['query']);
                foreach ($parts as $part) {
                    $pieces = explode('=', $part);
                    if (static::$returnUrlWithoutHistory && count($pieces) == 2 && $pieces[0] === 'returnUrl') {
                        continue;
                    }
                    if (count($pieces) == 2 && strlen($pieces[1]) > 0) {
                        $returnUrlParams[] = $part;
                    }
                }
            }
            if (count($returnUrlParams) > 0) {
                self::$returnUrl = $url['path'] . '?' . implode('&', $returnUrlParams);
            } else {
                self::$returnUrl = $url['path'];
            }
        }
        return self::$returnUrl;
    }

}
