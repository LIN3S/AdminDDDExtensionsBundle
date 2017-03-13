<?php

/*
 * This file is part of the Admin DDD Extensions Bundle.
 *
 * Copyright (c) 2017-present LIN3S <info@lin3s.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LIN3S\AdminDDDExtensionsBundle\Action;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Redirect trait.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
trait Redirect
{
    private function redirect($urlGenerator, $options, $entity, $command)
    {
        if (!isset($options['redirectAction'])) {
            return new RedirectResponse(
                $urlGenerator->generate('lin3s_admin_list', [
                    'entity' => $entity,
                ])
            );
        }

        return new RedirectResponse(
            $urlGenerator->generate('lin3s_admin_custom', [
                'action' => $options['redirectAction'],
                'entity' => $entity,
                'id'     => $command->id(),
            ])
        );
    }
}
