<?php

namespace App\Http;

use Closure;
use Detection\MobileDetect;

class Agent extends MobileDetect
{
    /**
     * List of additional operating systems.
     *
     * @var array<string, string>
     */
    protected static array $additionalOperatingSystems = [
        'Windows' => 'Windows',
        'Windows NT' => 'Windows NT',
        'OS X' => 'Mac OS X',
        'Debian' => 'Debian',
        'Ubuntu' => 'Ubuntu',
        'Macintosh' => 'PPC',
        'OpenBSD' => 'OpenBSD',
        'Linux' => 'Linux',
        'ChromeOS' => 'CrOS',
    ];

    /**
     * List of additional browsers.
     *
     * @var array<string, string>
     */
    protected static array $additionalBrowsers = [
        'Opera Mini' => 'Opera Mini',
        'Opera' => 'Opera|OPR',
        'Edge' => 'Edge|Edg',
        'Coc Coc' => 'coc_coc_browser',
        'UCBrowser' => 'UCBrowser',
        'Vivaldi' => 'Vivaldi',
        'Chrome' => 'Chrome',
        'Firefox' => 'Firefox',
        'Safari' => 'Safari',
        'IE' => 'MSIE|IEMobile|MSIEMobile|Trident/[.0-9]+',
        'Netscape' => 'Netscape',
        'Mozilla' => 'Mozilla',
        'WeChat' => 'MicroMessenger',
    ];

    /**
     * Key value store for resolved strings.
     *
     * @var array<string, mixed>
     */
    protected array $store = [];

    /**
     * Get the platform name from the User Agent.
     *
     * @return string|null
     */
    public function platform(): ?string
    {
        return $this->retrieveUsingCacheOrResolve('agent.platform', function () {
            return $this->findDetectionRulesAgainstUserAgent(
                $this->mergeRules(MobileDetect::getOperatingSystems(), static::$additionalOperatingSystems)
            );
        });
    }

    /**
     * Get the browser name from the User Agent.
     *
     * @return string|null
     */
    public function browser(): ?string
    {
        return $this->retrieveUsingCacheOrResolve('agent.browser', function () {
            return $this->findDetectionRulesAgainstUserAgent(
                $this->mergeRules(static::$additionalBrowsers, MobileDetect::getBrowsers())
            );
        });
    }

    /**
     * Determine if the device is a desktop computer.
     *
     * @return bool
     */
    public function isDesktop(): bool
    {
        return $this->retrieveUsingCacheOrResolve('agent.desktop', function () {
            // Check specifically for cloudfront headers if the useragent ===
            // 'Amazon CloudFront'
            if (
                $this->getUserAgent() === static::$cloudFrontUA
                && $this->getHttpHeader('HTTP_CLOUDFRONT_IS_DESKTOP_VIEWER') === 'true'
            ) {
                return true;
            }

            return !$this->isMobile() && !$this->isTablet();
        });
    }

    /**
     * Match a detection rule and return the matched key.
     *
     * @param array $rules
     * @return string|null
     */
    protected function findDetectionRulesAgainstUserAgent(array $rules): ?string
    {
        $userAgent = $this->getUserAgent();

        foreach ($rules as $key => $regex) {
            if (empty($regex)) {
                continue;
            }

            if ($this->match($regex, $userAgent)) {
                return $key ?: reset($this->matchesArray);
            }
        }

        return null;
    }

    /**
     * Retrieve from the given key from the cache or resolve the value.
     *
     * @param string $key
     * @param Closure():mixed $callback
     * @return mixed
     */
    protected function retrieveUsingCacheOrResolve(string $key, Closure $callback): mixed
    {
        $cacheKey = $this->createCacheKey($key);

        if (!is_null($cacheItem = $this->store[$cacheKey] ?? null)) {
            return $cacheItem;
        }

        return tap(call_user_func($callback), function ($result) use ($cacheKey) {
            $this->store[$cacheKey] = $result;
        });
    }

    /**
     * Merge multiple rules into one array.
     *
     * @param array $all
     * @return array<string, string>
     */
    protected function mergeRules(...$all): array
    {
        $merged = [];

        foreach ($all as $rules) {
            foreach ($rules as $key => $value) {
                if (empty($merged[$key])) {
                    $merged[$key] = $value;
                } elseif (is_array($merged[$key])) {
                    $merged[$key][] = $value;
                } else {
                    $merged[$key] .= '|' . $value;
                }
            }
        }

        return $merged;
    }
}
