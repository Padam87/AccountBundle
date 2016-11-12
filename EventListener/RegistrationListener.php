<?php

namespace Padam87\AccountBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Money\Currency;
use Padam87\AccountBundle\Entity\Account;
use Padam87\AccountBundle\Entity\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegistrationListener implements EventSubscriberInterface
{
    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var array
     */
    private $config;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_COMPLETED => 'addAccounts'
        ];
    }

    /**
     * @param Registry $doctrine
     * @param array    $config
     */
    public function __construct(Registry $doctrine, array $config)
    {
        $this->doctrine = $doctrine;
        $this->config = $config;
    }

    /**
     * @param FilterUserResponseEvent $event
     */
    public function addAccounts(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            throw new \LogicException(
                'Your User class must implement the UserInterface provided by the bundle to use the RegistrationListener'
            );
        }

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $accountClass = $this->config['classes']['account'];
        foreach ($this->config['currencies'] as $currency) {
            $account = new $accountClass($user, new Currency($currency));
            $user->addAccount($account);

            $em->persist($account);
        }

        $em->flush();
    }
}
