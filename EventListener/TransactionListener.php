<?php

namespace Padam87\AccountBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Padam87\AccountBundle\Entity\Transaction;

class TransactionListener implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'onFlush' => 'onFlush'
        ];
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Transaction) {
                $this->updateBalance($entity, $args);
            }
        }
    }

    private function updateBalance(Transaction $transaction, OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        if (null === $account = $transaction->getAccount()) {
            throw new \LogicException();
        }

        $money = $account->getBalance()->add($transaction->getAmount());
        $account->setBalance($money);

        $uow->persist($account);
        $uow->computeChangeSet($em->getClassMetadata(get_class($account)), $account);
    }
}
