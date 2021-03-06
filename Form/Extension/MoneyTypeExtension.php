<?php

namespace Padam87\AccountBundle\Form\Extension;

use Money\Currency;
use Money\Money;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoneyTypeExtension extends MoneyType implements FormTypeExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['use_money_type']) {
            $builder
                ->addModelTransformer(
                    new CallbackTransformer(
                        function (Money $model = null) {
                            return (int) ($model ? $model->getAmount() : 0);
                        },
                        function ($form) use ($options) {
                            if (!$options['reverse_transform']) {
                                return $form;
                            }

                            return new Money($form, new Currency($options['currency']));
                        }
                    )
                )
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['use_money_type'] && $form->getData() && $form->getData() instanceof Money) {
            $view->vars['money_pattern'] = self::getPattern($form->getData()->getCurrency()->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'divisor' => 100,
                    'use_money_type' => true,
                    'reverse_transform' => false,
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return MoneyType::class;
    }
}
