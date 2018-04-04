<?php
/**
 * This file is part of cakephp-thumber.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/cakephp-thumber
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 * @see         https://github.com/mirko-pagliai/cakephp-thumber/wiki/How-to-use-the-helper
 */
namespace Thumber\View\Helper;

use Cake\View\Helper;
use InvalidArgumentException;
use RuntimeException;
use Thumber\ThumbTrait;
use Thumber\Utility\ThumbCreator;

/**
 * Thumb Helper.
 *
 * This helper allows you to generate thumbnails.
 * @method string crop()
 * @method string cropUrl()
 * @method string fit()
 * @method string fitUrl()
 * @method string resize()
 * @method string resizeUrl()
 * @method string resizeCanvas()
 * @method string resizeCanvasUrl()
 */
class ThumbHelper extends Helper
{
    use ThumbTrait;

    /**
     * Helpers
     * @var array
     */
    public $helpers = ['Html'];

    /**
     * Magic method.
     *
     * It dynamically calls all methods: `crop()`, `cropUrl()`, `fit()`,
     *  `fitUrl()`, `resize()`, `resizeUrl()`, `resizeCanvas()` and
     *  `resizeCanvasUrl()`.
     *
     * Each method takes these arguments:
     *  - $path Path of the image from which to create the thumbnail. It can be
     *      a relative path (to APP/webroot/img), a full path or a remote url;
     *  - $params Parameters for creating the thumbnail;
     *  - $options Array of HTML attributes for the `img` element.
     * @param string $name Method to invoke
     * @param array $params Array of params for the method
     * @return string
     * @see https://github.com/mirko-pagliai/cakephp-thumber/wiki/How-to-use-the-helper
     * @since 1.4.0
     * @throws InvalidArgumentException
     * @uses isUrlMethod()
     * @uses runUrlMethod()
     */
    public function __call($name, $params)
    {
        list($path, $params, $options) = $params + [null, [], []];

        if (!$path) {
            throw new InvalidArgumentException(__d('thumber', 'Thumbnail path is missing'));
        }

        $url = $this->runUrlMethod($name, $path, $params, $options);

        return $this->isUrlMethod($name) ? $url : $this->Html->image($url, $options);
    }

    /**
     * Checks is a method name is an "Url" method.
     *
     * This means that the last characters of the method name are "Url".
     *
     * Example: `cropUrl` is an "Url" method. `crop` is not.
     * @param string $name Method name
     * @return bool
     * @since 1.4.0
     */
    protected function isUrlMethod($name)
    {
        return substr($name, -3) === 'Url';
    }

    /**
     * Runs an "Url" method and returns the url generated by the method
     * @param string $name Method name
     * @param string $path Path of the image from which to create the thumbnail.
     *  It can be a relative path (to APP/webroot/img), a full path or a remote
     *  url
     * @param array $params Parameters for creating the thumbnail
     * @param array $options Array of HTML attributes for the `img` element
     * @return string Thumbnail url
     * @since 1.4.0
     * @throws RuntimeException
     * @uses isUrlMethod()
     */
    protected function runUrlMethod($name, $path, array $params = [], array $options = [])
    {
        $name = $this->isUrlMethod($name) ? substr($name, 0, -3) : $name;

        //Sets default parameters and options
        $params += ['format' => 'jpg', 'height' => null, 'width' => null];
        $options += ['fullBase' => true];

        //Creates the thumbnail
        $thumb = new ThumbCreator($path);

        if (!method_exists($thumb, $name)) {
            throw new RuntimeException(__d('thumber', 'Method {0}::{1} does not exist', get_class($this), $name));
        }

        $thumb = $thumb->$name($params['width'], $params['height'])->save($params);

        return $this->getUrl($thumb, $options['fullBase']);
    }
}
