<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\Yaml\Yaml;

/**
 * Class AppExtension
 * 
 * @package App\Twig
 */
class AppExtension extends AbstractExtension {

    /**
     * @var string $translations All translation items parsed to PHP array.
     * */
    private $translations;

    /**
     * Class constructor.
     * 
     * @param string $pathTranslationFile Path to translation file.
     * 
     * @return void
     * */
    public function __construct(string $pathTranslationFile) {

        $this->translations = Yaml::parseFile($pathTranslationFile);
    }

    /**
     * Method for Twig filters.
     * */
    public function getFilters() {

        return [
            new TwigFilter('trans_items', [$this, 'translationItems']),
        ];
    }

    /**
     * Find a specific translation alias and convert it to a PHP array.
     * 
     * It works recursively.
     * 
     * @param string $alias Translation alias.
     * @param array $aliasItems Current converted translation items. Only for internal use.
     * @param array $resultItems Final php array with translations. Only for internal use.
     * 
     * @return array Translation items in PHP array.
     * */
    public function translationItems($alias, $aliasItems = [], $resultItems = []) {

        if (!empty($alias) && empty($aliasItems)) {

            // split alias
            $splitedAlias = explode('.', $alias);

            // swap parts of alias (values) as keys of array
            $aliasItems = array_flip($splitedAlias);
        }

        if (!empty($aliasItems)) {

            // set first alias item
            $firstAliasItem = array_key_first($aliasItems);

            // prepare result items
            $resultItems = (empty($resultItems) ? $this->translations[$firstAliasItem] : $resultItems[$firstAliasItem]);

            // remove first alias item
            array_shift($aliasItems);

            // if no more alias items, then set alias as null
            if (empty($aliasItems)) $alias = null;

            // set result items
            $resultItems = $this->translationItems($alias, $aliasItems, $resultItems);
        }

        return $resultItems;
    }
}
