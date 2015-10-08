<?php
namespace bookin\helpers\yii2;

use yii\helpers\ArrayHelper;
use Yii;

/**
 * Class Extensions
 *
 * add to config file:
 * <code>
 * 'extensions'=>Extensions::add([
 *      'phpoffice/phpword'=>[
 *          'name'=>'phpoffice/phpword',
 *          'alias'=>
 *              '&#64;phpoffice/phpword'=>'&#64;extensions/phpoffice/phpword',
 *          ]
 *      ];
 * ]);
 * </code>
 */
class Extensions{

    /**
     * @param array $extensions
     * <code>
     * 'phpoffice/phpword'=>[
     *      'name'=>'phpoffice/phpword',
     *      'alias'=>
     *          '&#64;phpoffice/phpword'=>'&#64;extensions/phpoffice/phpword',
     *       ]
     * ];
     * </code>
     * @param null $extensionsDir Path to extensions dir Default - @app/extensions
     * @param null $vendorExtension Path to vendor extensions list Default - dirname(__DIR__). "/vendor/yiisoft/extensions.php"
     * @return array
     */
    public static function add($extensions=[], $extensionsDir=null, $vendorExtension=null){
        if(empty($extensionsDir))
            $extensionsDir =  dirname(__DIR__). DIRECTORY_SEPARATOR . 'extensions';

        if(empty($vendorExtension))
            $vendorExtension = dirname(__DIR__). "/vendor/yiisoft/extensions.php";

        $vendorExtension = require_once $vendorExtension;

        Yii::setAlias('@extensions', $extensionsDir);

        foreach ($extensions as $extension) {
            if (isset($extension['alias'])) {
                foreach ($extension['alias'] as $alias => $path) {
                    Yii::setAlias($alias, $path);
                }
            }
        }

        $merge = ArrayHelper::merge(
            $extensions,
            $vendorExtension
        );

        return $merge;
    }

    protected function __construct(){

    }

    private function __clone()
    {
    }


    private function __wakeup()
    {
    }
}
