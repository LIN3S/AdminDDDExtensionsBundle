<?php

/*
 * This file is part of the Admin DDD Extensions Bundle.
 *
 * Copyright (c) 2017-present LIN3S <info@lin3s.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends \Symfony\Component\HttpKernel\Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new LIN3S\AdminBundle\Lin3sAdminBundle(),
            new \LIN3S\AdminDDDExtensionsBundle\Lin3sAdminDDDExtensionsBundle(),
            new \LIN3S\CMSKernel\Infrastructure\Symfony\Bundle\Lin3sCmsKernelBundle(),
            new \SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle(),
            new Tests\Application\ExampleBundle\ApplicationExampleBundle(),
            new Symfony\Bundle\WebServerBundle\WebServerBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config.yml');
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__) . '/var/logs';
    }
}
