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

use LIN3S\AdminBundle\Configuration\Model\Entity;
use LIN3S\AdminBundle\Configuration\Type\ActionType;
use LIN3S\AdminBundle\Form\FormHandler;
use LIN3S\SharedKernel\Application\CommandBus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class HandleCommandActionType implements ActionType
{
    use EntityId;
    use OptionResolver;
    use Redirect;

    private $flashBag;
    private $twig;
    private $formHandler;
    private $commandBus;
    private $urlGenerator;

    public function __construct(
        FormHandler $formHandler,
        CommandBus $commandBus,
        \Twig_Environment $twig,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->formHandler = $formHandler;
        $this->commandBus = $commandBus;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    public function execute($entity, Entity $config, Request $request, $options = [])
    {
        $this->checkRequired($options, 'form');

        $form = $this->formHandler->createForm($options['form'], $entity); //, ['resource_id' => $entity->id()]);
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $form->handleRequest($request);
            if ($form->isValid() && $form->isSubmitted()) {
                $this->commandBus->handle(
                    $form->getData()
                );
                $this->flashBag->add(
                    'lin3s_admin_success',
                    sprintf(
                        'The %s is successfully saved',
                        $config->name()
                    )
                );

                return $this->redirect($this->urlGenerator, $options, $config->name(), $form->getData());
            } else {
                $this->flashBag->add(
                    'lin3s_admin_error',
                    sprintf(
                        'Errors while saving %s. Please check all fields and try again',
                        $config->name()
                    )
                );
            }
        }

        return new Response(
            $this->twig->render('Lin3sAdminBundle:Admin:new.html.twig', [
                'entity'       => $entity,
                'entityConfig' => $config,
                'form'         => $form->createView(),
            ])
        );
    }
}
