<?php

/*
 * This file is part of the Moss micro-framework
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\Config;

use Moss\Bag\Bag;

/**
 * Configuration representation
 *
 * @package Moss Config
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class Config extends Bag implements ConfigInterface
{
    protected $mode;
    protected $storage = array(
        'framework' => array(
            'error' => array(
                'display' => true,
                'level' => -1,
                'detail' => true
            ),
            'session' => array(
                'name' => 'PHPSESSID',
                'cacheLimiter' => ''
            ),
            'cookie' => array(
                'domain' => null,
                'path' => '/',
                'http' => true,
                'ttl' => 2592000 // one month
            )
        ),
        'container' => array(),
        'dispatcher' => array(),
        'router' => array()
    );

    /**
     * Creates Config instance
     *
     * @param array  $arr
     * @param string $mode
     *
     * @throws ConfigException
     */
    public function __construct($arr = array(), $mode = null)
    {
        $this->mode($mode);
        $this->import($arr);
    }

    /**
     * Sets config mode
     *
     * @param null|string $mode
     *
     * @return string
     */
    public function mode($mode = null)
    {
        if ($mode !== null) {
            $this->mode = (string) $mode;
        }

        return $this->mode;
    }

    /**
     * Reads configuration properties from passed array
     *
     * @param array       $arr
     * @param null|string $prefix
     *
     * @return $this
     */
    public function import(array $arr, $prefix = null)
    {
        $importKeys = array();
        foreach ($arr as $key => $node) {
            if (strpos($key, 'import') === 0) {
                $mode = substr($key, 7);
                if ($mode == '' || $mode == $this->mode) {
                    $importKeys[] = $key;
                }

                continue;
            }

            switch ($key) {
                case 'container':
                    $node = $this->applyContainerDefaults($node);
                    break;
                case 'dispatcher':
                    $node = $this->applyDispatcherDefaults($node);
                    break;
                case 'router':
                    $node = $this->applyRouterDefaults($node);
                    break;
            }

            $this->storage[$key] = array_merge($this->storage[$key], $this->applyPrefix($node, $prefix));
        }

        foreach ($importKeys as $key) {
            foreach ($arr[$key] as $key => $value) {
                $this->import($value, $this->prefixKey($key, $prefix));
            }
        }

        return $this;
    }

    /**
     * Applies prefix to array keys
     *
     * @param array $array
     * @param null|string  $prefix
     *
     * @return array
     */
    private function applyPrefix(array $array, $prefix = null)
    {
        if (!$this->checkPrefix($prefix)) {
            return $array;
        }

        $result = array();
        foreach ($array as $key => $value) {
            $result[$this->prefixKey($key, $prefix)] = $value;
        }

        return $result;
    }

    /**
     * Prefixes key
     *
     * @param string $key
     * @param null|string $prefix
     *
     * @return string
     */
    private function prefixKey($key, $prefix = null)
    {
        if (!$this->checkPrefix($prefix)) {
            return $key;
        }

        return $prefix . ':' . $key;
    }

    /**
     * Checks if key needs to be prefixed
     * Only strings are prefixed
     *
     * @param string $prefix
     *
     * @return bool
     */
    private function checkPrefix($prefix)
    {
        return !empty($prefix) && !is_numeric($prefix);
    }

    /**
     * Applies default values or missing properties for containers component definition
     *
     * @param array $array
     * @param array $defaults
     *
     * @return array
     */
    private function applyContainerDefaults(array $array, $defaults = array('shared' => false))
    {
        foreach ($array as &$node) {
            if (!is_array($node) || !array_key_exists('component', $node) || !is_callable($node['component'])) {
                continue;
            }

            $node = array_merge($defaults, $node);
            unset($node);
        }

        return $array;
    }

    /**
     * Applies default values or missing properties for event listener definition
     *
     * @param array $array
     *
     * @return array
     * @throws ConfigException
     */
    private function applyDispatcherDefaults(array $array)
    {
        foreach ($array as $evt) {
            foreach ($evt as $node) {
                if (!is_callable($node)) {
                    throw new ConfigException('Event listener must be callable, got ' . gettype($node));
                }
            }
        }

        return $array;
    }

    /**
     * Applies default values or missing properties for route definition
     *
     * @param array $array
     * @param array $defaults
     *
     * @return array
     * @throws ConfigException
     */
    private function applyRouterDefaults(array $array, $defaults = array('arguments' => array(), 'methods' => array()))
    {
        foreach ($array as &$node) {
            if (!isset($node['pattern'])) {
                throw new ConfigException('Missing required "pattern" property in route definition');
            }

            if (!isset($node['controller'])) {
                throw new ConfigException('Missing required "controller" property in route definition');
            }

            $node = array_merge($defaults, $node);
            unset($node);
        }

        return $array;
    }

    /**
     * Returns current stored configuration as array
     *
     * @return array
     */
    public function export()
    {
        return $this->storage;
    }
}
