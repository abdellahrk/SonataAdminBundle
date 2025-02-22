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

namespace Sonata\AdminBundle\Tests\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Exception\AdminCodeNotFoundException;
use Sonata\AdminBundle\Templating\MutableTemplateRegistryInterface;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\AdminBundle\Twig\Extension\TemplateRegistryExtension;
use Sonata\AdminBundle\Twig\TemplateRegistryRuntime;
use Symfony\Component\DependencyInjection\Container;

/**
 * NEXT_MAJOR: Remove this test.
 *
 * @group legacy
 */
final class TemplateRegistryExtensionTest extends TestCase
{
    private TemplateRegistryExtension $extension;

    protected function setUp(): void
    {
        $templateRegistry = $this->createMock(TemplateRegistryInterface::class);
        $templateRegistry->method('getTemplate')->with('edit')->willReturn('@SonataAdmin/CRUD/edit.html.twig');

        $adminTemplateRegistry = $this->createMock(MutableTemplateRegistryInterface::class);
        $adminTemplateRegistry->method('getTemplate')->with('edit')->willReturn('@SonataAdmin/CRUD/edit.html.twig');

        $admin = $this->createStub(AdminInterface::class);
        $admin
            ->method('getTemplateRegistry')
            ->willReturn($adminTemplateRegistry);

        $container = new Container();
        $container->set('admin.post', $admin);
        $pool = new Pool($container, ['admin.post']);

        $this->extension = new TemplateRegistryExtension(new TemplateRegistryRuntime(
            $templateRegistry,
            $pool
        ));
    }

    public function testGetFunctions(): void
    {
        static::assertCount(2, $this->extension->getFunctions());
    }

    public function testGetAdminTemplate(): void
    {
        static::assertSame(
            '@SonataAdmin/CRUD/edit.html.twig',
            $this->extension->getAdminTemplate('edit', 'admin.post')
        );
    }

    public function testGetAdminTemplateFailure(): void
    {
        $this->expectException(AdminCodeNotFoundException::class);

        $this->expectExceptionMessage('Admin service "admin.non-existing" not found in admin pool. Did you mean "admin.post" or one of those: []?');

        static::assertSame(
            '@SonataAdmin/CRUD/edit.html.twig',
            $this->extension->getAdminTemplate('edit', 'admin.non-existing')
        );
    }

    public function testGetGlobalTemplate(): void
    {
        static::assertSame(
            '@SonataAdmin/CRUD/edit.html.twig',
            $this->extension->getGlobalTemplate('edit')
        );
    }
}
