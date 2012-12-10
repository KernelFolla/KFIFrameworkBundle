<?php

namespace KFI\FrameworkBundle\Loader;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Mapping\Loader\YamlFileLoader;

/**
 * This class inherits from YamlFileLoader because we need to call the
 * parseNodes() protected method.
 */
class YamlFormFieldsLoader
{
    /**
     * Constructor.
     *
     * @param string $file The mapping file to load
     *
     * @throws MappingException if the mapping file does not exist
     * @throws MappingException if the mapping file is not readable
     */
    public function __construct($file)
    {
        if (!is_file($file)) {
            throw new MappingException(sprintf('The mapping file %s does not exist', $file));
        }

        if (!is_readable($file)) {
            throw new MappingException(sprintf('The mapping file %s is not readable', $file));
        }

        $this->file = $file;
    }
}