<?php

namespace Padam87\AccountBundle\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * 2 precision money type, using decimal as an SQL type, and integer as PHP type.
 */
class MoneyType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'money';
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $fieldDeclaration['scale'] = 2; // must be 2 digits...

        return $platform->getDecimalTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function canRequireSQLConversion()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return $sqlExpr . ' / 100';
    }

    /**
     * {@inheritdoc}
     *
     * No AbstractPlatform hint here...
     */
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        return $sqlExpr . ' * 100';
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return (null === $value) ? null : (int) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType()
    {
        return \PDO::PARAM_INT;
    }
}
