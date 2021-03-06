<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Repository;

use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface as BaseMenuItemRepositoryInterface;

interface MenuItemRepositoryInterface extends BaseMenuItemRepositoryInterface
{
    /**
     * @param int $routeId
     *
     * @return array
     */
    public function findByRoute(int $routeId): array;
}
