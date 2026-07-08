<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Cookie\Middleware\EncryptCookies;

class EncryptPreferenceCookies extends EncryptCookies
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $plainPreferenceValues = [
        'pitstop_theme' => ['light', 'dark'],
        'pitstop_font_size' => ['normal', 'large'],
    ];

    /**
     * Decrypt Laravel cookies while still accepting plain client-side preference cookies.
     *
     * @param  string  $name
     * @param  string|array  $cookie
     * @return string|array
     */
    protected function decryptCookie($name, $cookie)
    {
        if (
            is_string($cookie)
            && isset($this->plainPreferenceValues[$name])
            && in_array($cookie, $this->plainPreferenceValues[$name], true)
        ) {
            return CookieValuePrefix::create($name, $this->encrypter->getKey()).$cookie;
        }

        return parent::decryptCookie($name, $cookie);
    }
}
