<?php
namespace KFI\FrameworkBundle\Service;

use \Locale;
use \DateTime;
use \IntlDateFormatter;

class FrameworkTwigExtension extends \Twig_Extension
{
    protected $settedLocale;

    public function getFilters()
    {
        return array(
            'intldate'   => new \Twig_Filter_Method($this, 'filterIntlDate')
        );
    }

    public function getName()
    {
        return 'kfi_fw_extension';
    }

    public function filterIntlDate($date, $format)
    {
        if (!isset($this->settedLocale)) {
            $this->settedLocale = new IntlDateFormatter(Locale::getDefault(
            ), IntlDateFormatter::NONE, IntlDateFormatter::NONE);
            $this->settedLocale->setPattern($format);
        }
        if (!($date instanceof \DateTime)) {
            $date = new Datetime($date);
        }

        return $this->settedLocale->format($date);
    }
}