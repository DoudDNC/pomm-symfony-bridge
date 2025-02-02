<?php
/*
 * This file is part of Pomm's SymfonyBidge package.
 *
 * (c) 2015 Grégoire HUBERT <hubert.greg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PommProject\SymfonyBridge\PropertyInfo\Extractor;

use PommProject\Foundation\Pomm;
use PommProject\Foundation\Session;
use Symfony\Component\PropertyInfo\PropertyListExtractorInterface;

/**
 * Extract properties list using pomm.
 *
 * @package PommSymfonyBridge
 * @copyright 2015 Grégoire HUBERT
 * @author Nicolas Joseph
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 */
class ListExtractor implements PropertyListExtractorInterface
{
    private $pomm;

    public function __construct(Pomm $pomm)
    {
        $this->pomm = $pomm;
    }

    /**
     * @see PropertyListExtractorInterface
     */
    public function getProperties(string $class, array $context = array())
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

        if (!class_exists($model_name)) {
            return;
        }

        return $this->getPropertiesList($session, $model_name);
    }

    private function getPropertiesList(Session $session, $model_name)
    {
        $model = $session->getModel($model_name);
        $structure = $model->getStructure();

        return $structure->getFieldNames();
    }
}
