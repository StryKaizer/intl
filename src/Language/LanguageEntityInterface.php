<?php

namespace CommerceGuys\Intl\Language;

interface LanguageEntityInterface extends LanguageInterface
{
    /**
     * Sets the two-letter language code.
     *
     * @param string $languageCode The two-letter language code.
     */
    public function setLanguageCode($languageCode);

    /**
     * Sets the language name.
     *
     * @param string $name The language name.
     */
    public function setName($name);
}
