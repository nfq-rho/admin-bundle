<?php declare(strict_types=1);

namespace Nfq\AdminBundle\ImageUpload\Naming;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\ConfigurableInterface;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Naming\Polyfill;
use Vich\UploaderBundle\Util\Transliterator;

/**
 * Class TimeLocaleNamer
 * @package Nfq\AdminBundle\ImageUpload\Naming
 */
class TimeLocaleNamer implements NamerInterface, ConfigurableInterface
{
    use Polyfill\FileExtensionTrait;

    /** @var bool */
    private $transliterate = false;

    /**
     * @param array $options Options for this namer. The following options are accepted:
     *                       - transliterate: whether the filename should be transliterated or not
     *
     * @throws \InvalidArgumentException
     */
    public function configure(array $options): void
    {
        $this->transliterate = isset($options['transliterate']) ? (bool) $options['transliterate'] : $this->transliterate;
    }

    public function name($object, PropertyMapping $mapping): string
    {
        if (!method_exists($object, 'getLocale')) {
            throw new \InvalidArgumentException('Can not use LocaleNamer on non-translatable objects.');
        }

        /* @var $file UploadedFile */
        $file = $mapping->getFile($object);
        $name = $file->getClientOriginalName();

        if ($this->transliterate) {
            $name = Transliterator::transliterate($name);
        }

        return sprintf(
            '%s-%s-%s',
            time(),
            $object->getLocale(),
            $name
        );
    }
}
