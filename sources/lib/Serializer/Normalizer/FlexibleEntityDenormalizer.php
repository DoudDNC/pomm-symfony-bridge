<?php
/*
 * This file is part of Pomm's SymfonyBidge package.
 *
 * (c) 2017 Grégoire HUBERT <hubert.greg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PommProject\SymfonyBridge\Serializer\Normalizer;

use PommProject\Foundation\Pomm;
use PommProject\ModelManager\Model\FlexibleEntity\FlexibleEntityInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizer for flexible entities.
 *
 * @package PommSymfonyBridge
 * @copyright 2017 Grégoire HUBERT
 * @author Nicolas Joseph
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 */
class FlexibleEntityDenormalizer implements DenormalizerInterface
{
    private $pomm;

    public function __construct(Pomm $pomm)
    {
        $this->pomm = $pomm;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, string $class, string $format = null, array $context = array()): mixed
    {
        if (isset($context['session:name'])) {
            $session = $this->pomm->getSession($context['session:name']);
        } else {
            $session = $this->pomm->getDefaultSession();
        }

        if (isset($context['model:name'])) {
            $model_name = $context['model:name'];
        } else {
            $model_name = "${class}Model";
        }

        $model = $session->getModel($model_name);
        return $model->createEntity($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        if (!class_exists($type)) {
            return false;
        }

        $reflection = new \ReflectionClass($type);
        $interfaces = $reflection->getInterfaces();

        return isset($interfaces[FlexibleEntityInterface::class]);
    }
}
