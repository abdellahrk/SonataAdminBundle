<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminBundle\Route;

use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class RoutesCacheWarmUp implements CacheWarmerInterface
{
    public function __construct(
        private RoutesCache $cache,
        private Pool $pool
    ) {
    }

    public function isOptional(): bool
    {
        return true;
    }

    /**
     * NEXT_MAJOR: Add the string param typehint when Symfony 4 support is dropped.
     *
     * @param string $cacheDir
     *
     * @return string[]
     */
    public function warmUp($cacheDir): array
    {
        foreach ($this->pool->getAdminServiceCodes() as $code) {
            $this->cache->load($this->pool->getInstance($code));
        }

        return [];
    }
}
