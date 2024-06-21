<?php

namespace Suarez\UtmParameter;

use Illuminate\Http\Request;

class UtmParameter
{
    /**
     * Bag containing all UTM-Parameters.
     *
     * @var array
     */
    public array|null $parameters;

    /**
     * Utm Parameter Session Key.
     *
     * @var string
     */
    public string $sessionKey;


    public function __construct(array $parameters = [])
    {
        $this->sessionKey = config('utm-parameter.session_key');
        $this->parameters = $parameters;
    }

    /**
     * Bootstrap UtmParameter.
     *
     * @param Request $request
     *
     * @return UtmParameter
     */
    public function boot(Request $request)
    {
        $this->parameters = $this->useRequestOrSession($request);
        return $this;
    }

    /**
     * Check which Parameters should be used.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function useRequestOrSession(Request $request)
    {
        $currentRequestParameter = $this->getParameter($request);
        $sessionParameter = session($this->sessionKey);

        if (!empty($currentRequestParameter) && empty($sessionParameter)) {
            session([$this->sessionKey => $currentRequestParameter]);
            return $currentRequestParameter;
        }

        if (!empty($currentRequestParameter) && !empty($sessionParameter) && config('utm-parameter.override_utm_parameters')) {
            $mergedParameters = array_merge($sessionParameter, $currentRequestParameter);
            session([$this->sessionKey => $mergedParameters]);
            return $mergedParameters;
        }

        return $sessionParameter;
    }

    /**
     * Retrieve all UTM-Parameter.
     *
     * @return array
     */
    public function all()
    {
        return $this->parameters ?? [];
    }

    /**
     * Retrieve a UTM-Parameter by key.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function get(string $key)
    {
        $parameters = $this->all();
        $key = $this->ensureUtmPrefix($key);

        if (!array_key_exists($key, $parameters)) {
            return null;
        }

        return $parameters[$key];
    }

    /**
     * Determine if a UTM-Parameter exists.
     *
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function has(string $key, $value = null)
    {
        $parameters = $this->all();
        $key = $this->ensureUtmPrefix($key);

        if (!array_key_exists($key, $parameters)) {
            return false;
        }

        if (array_key_exists($key, $parameters) && $value !== null) {
            return $this->get($key) === $value;
        }

        return true;
    }

    /**
     * Determine if a value contains inside the key.
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function contains(string $key, string $value)
    {
        $parameters = $this->all();
        $key = $this->ensureUtmPrefix($key);

        if (!array_key_exists($key, $parameters) || !is_string($value)) {
            return false;
        }

        return str_contains($this->get($key), $value);
    }

    /**
     * Clear and remove utm session.
     *
     * @return bool
     */
    public function clear()
    {
        session()->forget($this->sessionKey);
        $this->parameters = null;
        return true;
    }

    /**
     * Retrieve all UTM-Parameter from the URI.
     *
     * @return array
     */
    protected function getParameter(Request $request)
    {
        $allowedKeys = config('utm-parameter.allowed_utm_parameters', [
            'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'
        ]);

        return collect($request->all())
            ->filter(fn ($value, $key) => substr($key, 0, 4) === 'utm_')
            ->filter(fn ($value, $key) => in_array($key, $allowedKeys))
            ->mapWithKeys(fn ($value, $key) => [
                htmlspecialchars($key, ENT_QUOTES, 'UTF-8') => htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
            ])
            ->toArray();
    }

    /**
     * Ensure the key to start with 'utm_'.
     *
     * @param string $key
     * @return string
     */
    protected function ensureUtmPrefix(string $key): string
    {
        return str_starts_with($key, 'utm_') ? $key : 'utm_' . $key;
    }
}
