<?php

/*
 * This file is part of the Admin DDD Extensions Bundle.
 *
 * Copyright (c) 2017-present LIN3S <info@lin3s.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LIN3S\AdminDDDExtensionsBundle;

use LIN3S\AdminDDDExtensionsBundle\DependencyInjection\Lin3sAdminDDDExtensionsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Gorka Laucirica <gorka.lauzirka@gmail.com>
 */
final class Lin3sAdminDDDExtensionsBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new Lin3sAdminDDDExtensionsExtension();
    }
}
