<?php

/*
 * This file is part of the Moss micro-framework
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\loader;

/**
 * Moss APC auto load handlers
 *
 * @package Moss Autoloader
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class APCLoader extends Loader
{

    private $prefix;

    /**
     * Constructor.
     *
     * @param string $prefix apc prefix
     *
     * @throws \RuntimeException
     */
    public function __construct($prefix)
    {
        if (!extension_loaded('apc')) {
            throw new \RuntimeException('Unable to use APCLoader. APC is not enabled.');
        }

        $this->prefix = $prefix;
    }

    /**
     * Finds file in defined namespaces and prefixes
     *
     * @param string $className
     *
     * @return string
     */
    public function findFile($className)
    {
        if (false === $file = apc_fetch($this->prefix . $className)) {
            apc_store($this->prefix . $className, $file = parent::findFile($className));
        }

        return $file;
    }
}
