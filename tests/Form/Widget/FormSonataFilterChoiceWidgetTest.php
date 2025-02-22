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

namespace Sonata\AdminBundle\Tests\Form\Widget;

use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Sonata\AdminBundle\Form\Type\Operator\ContainsOperatorType;
use Sonata\AdminBundle\Tests\Fixtures\TestExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType as SymfonyChoiceType;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\FormTypeInterface;

final class FormSonataFilterChoiceWidgetTest extends BaseWidgetTest
{
    protected $type = 'filter';

    public function testDefaultValueRendering(): void
    {
        $choice = $this->factory->create(
            $this->getChoiceClass(),
            null,
            $this->getDefaultOption()
        );

        $html = $this->cleanHtmlWhitespace($this->renderWidget($choice->createView()));
        $html = $this->cleanHtmlAttributeWhitespace($html);

        static::assertStringContainsString(
            '<option value="1">[trans]label_type_contains[/trans]</option>',
            $html
        );

        static::assertStringContainsString(
            '<option value="2">[trans]label_type_not_contains[/trans]</option>',
            $html
        );

        static::assertStringContainsString(
            '<option value="3">[trans]label_type_equals[/trans]</option></select>',
            $html
        );
    }

    /**
     * @return class-string<FormTypeInterface>
     */
    protected function getChoiceClass(): string
    {
        return ChoiceType::class;
    }

    /**
     * @return array<FormExtensionInterface>
     */
    protected function getExtensions(): array
    {
        $extensions = parent::getExtensions();
        $guesser = $this->createMock(FormTypeGuesserInterface::class);
        $extension = new TestExtension($guesser);
        $type = new ChoiceType();
        $extension->addType($type);

        if (!$extension->hasType($this->getChoiceClass())) {
            $reflection = new \ReflectionClass($extension);
            $property = $reflection->getProperty('types');
            $property->setAccessible(true);
            $types = $property->getValue($extension);
            \assert(\is_array($types));
            $property->setValue($extension, [$type::class => current($types)]);
        }

        $extensions[] = $extension;

        return $extensions;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultOption(): array
    {
        return ['field_type' => SymfonyChoiceType::class,
             'field_options' => [],
             'operator_type' => ContainsOperatorType::class,
             'operator_options' => [],
        ];
    }
}
