<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cors
{
    /** @var CI_Controller */
    protected $ci;

    /** @var string[]  */
    private array $allowedOrigins = [];
    /** @var string[] */
    private array $allowedOriginsPatterns = [];
    /** @var string[] */
    private array $allowedMethods = [];
    /** @var string[] */
    private array $allowedHeaders = [];
    /** @var string[] */
    private array $exposedHeaders = [];
    private bool $supportsCredentials = false;
    private ?int $maxAge = 0;

    private bool $allowAllOrigins = false;
    private bool $allowAllMethods = false;
    private bool $allowAllHeaders = false;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->config('cors', true);

        $this->setOptions($this->ci->config->item('cors'));
    }

    public function handle()
    {
        // For Preflight, return the Preflight response
        if ($this->isPreflightRequest($this->ci->input)) {
            $this->handlePreflightRequest($this->ci->input);
            $this->varyHeader($this->ci->output, 'Access-Control-Request-Method');
        }

        if ($this->ci->input->method(true) === 'OPTIONS') {
            $this->varyHeader($this->ci->output, 'Access-Control-Request-Method');
        }

        if (!$this->ci->output->get_header('Access-Control-Allow-Origin')) {
            // Add the CORS headers to the Response
            $this->addActualRequestHeaders($this->ci->output, $this->ci->input);
        }
    }

    public function setOptions(array $options): void
    {
        $this->allowedOrigins = $options['allowed_origins'] ?? $this->allowedOrigins;
        $this->allowedOriginsPatterns = $options['allowed_origins_patterns'] ?? $this->allowedOriginsPatterns;
        $this->allowedMethods = $options['allowed_methods'] ?? $this->allowedMethods;
        $this->allowedHeaders = $options['allowed_headers'] ?? $this->allowedHeaders;
        $this->supportsCredentials = $options['supports_credentials'] ?? $this->supportsCredentials;

        $maxAge = $this->maxAge;
        if (array_key_exists('maxAge', $options)) {
            $maxAge = $options['maxAge'];
        } elseif (array_key_exists('max_age', $options)) {
            $maxAge = $options['max_age'];
        }
        $this->maxAge = $maxAge === null ? null : (int)$maxAge;

        $exposedHeaders =  $options['exposed_headers'] ?? $this->exposedHeaders;
        $this->exposedHeaders = $exposedHeaders === false ? [] : $exposedHeaders;

        $this->normalizeOptions();
    }

    private function normalizeOptions(): void
    {
        // Normalize case
        $this->allowedHeaders = array_map('strtolower', $this->allowedHeaders);
        $this->allowedMethods = array_map('strtoupper', $this->allowedMethods);

        // Normalize ['*'] to true
        $this->allowAllOrigins = in_array('*', $this->allowedOrigins);
        $this->allowAllHeaders = in_array('*', $this->allowedHeaders);
        $this->allowAllMethods = in_array('*', $this->allowedMethods);

        // Transform wildcard pattern
        if (!$this->allowAllOrigins) {
            foreach ($this->allowedOrigins as $origin) {
                if (strpos($origin, '*') !== false) {
                    $this->allowedOriginsPatterns[] = $this->convertWildcardToPattern($origin);
                }
            }
        }
    }

    /**
     * Create a pattern for a wildcard, based on Str::is() from Laravel
     *
     * @see https://github.com/laravel/framework/blob/5.5/src/Illuminate/Support/Str.php
     * @param string $pattern
     * @return string
     */
    private function convertWildcardToPattern($pattern)
    {
        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "*.example.com", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern);

        return '#^' . $pattern . '\z#u';
    }

    public function isCorsRequest(CI_Input $request): bool
    {
        return $request->get_request_header('Origin') !== null;
    }

    public function isPreflightRequest(CI_Input $request): bool
    {
        return $request->method(true) === 'OPTIONS' && $request->get_request_header('Access-Control-Request-Method');
    }

    public function handlePreflightRequest(CI_Input $request): CI_Output
    {
        /** @var CI_Output $response */
        $response = $this->ci->output;

        $response->set_status_header(204);

        return $this->addPreflightRequestHeaders($response, $request);
    }

    public function addPreflightRequestHeaders(CI_Output $response, CI_Input $request): CI_Output
    {
        $this->configureAllowedOrigin($response, $request);

        if ($response->get_header('Access-Control-Allow-Origin')) {
            $this->configureAllowCredentials($response);
            $this->configureAllowedMethods($response, $request);
            $this->configureAllowedHeaders($response, $request);
            $this->configureMaxAge($response);
        }

        return $response;
    }

    public function isOriginAllowed(CI_Input $request): bool
    {
        if ($this->allowAllOrigins === true) {
            return true;
        }

        $origin = $request->get_request_header('Origin');

        if (in_array($origin, $this->allowedOrigins)) {
            return true;
        }

        foreach ($this->allowedOriginsPatterns as $pattern) {
            if (preg_match($pattern, $origin)) {
                return true;
            }
        }

        return false;
    }

    public function addActualRequestHeaders(CI_Output $response, CI_Input $request): CI_Output
    {
        $this->configureAllowedOrigin($response, $request);

        if ($response->get_header('Access-Control-Allow-Origin')) {
            $this->configureAllowCredentials($response);
            $this->configureExposedHeaders($response);
        }

        return $response;
    }

    private function configureAllowedOrigin(CI_Output $response, CI_Input $request): void
    {
        if ($this->allowAllOrigins === true && !$this->supportsCredentials) {
            // Safe+cacheable, allow everything
            $response->set_header('Access-Control-Allow-Origin: *');
        } elseif ($this->isSingleOriginAllowed()) {
            // Single origins can be safely set
            $response->set_header(sprintf('Access-Control-Allow-Origin: %s', array_values($this->allowedOrigins)[0]));
        } else {
            // For dynamic headers, set the requested Origin header when set and allowed
            if ($this->isCorsRequest($request) && $this->isOriginAllowed($request)) {
                $response->set_header("Access-Control-Allow-Origin: {$request->get_request_header('origin')}");
            }

            $this->varyHeader($response, 'Origin');
        }
    }

    private function isSingleOriginAllowed(): bool
    {
        if ($this->allowAllOrigins === true || count($this->allowedOriginsPatterns) > 0) {
            return false;
        }

        return count($this->allowedOrigins) === 1;
    }

    private function configureAllowedMethods(CI_Output $response, CI_Input $request): void
    {
        if ($this->allowAllMethods === true) {
            $allowMethods = strtoupper($request->get_request_header('Access-Control-Request-Method'));
            $this->varyHeader($response, 'Access-Control-Request-Method');
        } else {
            $allowMethods = implode(', ', $this->allowedMethods);
        }

        $response->set_header("Access-Control-Allow-Methods: {$allowMethods}");
    }

    private function configureAllowedHeaders(CI_Output $response, CI_Input $request): void
    {
        if ($this->allowAllHeaders === true) {
            $allowHeaders = $request->get_request_header('Access-Control-Request-Headers');
            $this->varyHeader($response, 'Access-Control-Request-Headers');
        } else {
            $allowHeaders = implode(', ', $this->allowedHeaders);
        }
        $response->set_header("Access-Control-Allow-Headers: {$allowHeaders}");
    }

    private function configureAllowCredentials(CI_Output $response): void
    {
        if ($this->supportsCredentials) {
            $response->set_header('Access-Control-Allow-Credentials: true');
        }
    }

    private function configureExposedHeaders(CI_Output $response): void
    {
        if ($this->exposedHeaders) {
            $response->set_header(sprintf('Access-Control-Expose-Headers: %s', implode(', ', $this->exposedHeaders)));
        }
    }

    private function configureMaxAge(CI_Output $response): void
    {
        if ($this->maxAge !== null) {
            $response->set_header("Access-Control-Max-Age: {$this->maxAge}");
        }
    }

    public function varyHeader(CI_Output $response, string $header): CI_Output
    {
        if (!$response->get_header('Vary')) {
            $response->set_header("Vary: {$header}");
        } elseif (!in_array($header, explode(', ', $response->get_header('Vary')))) {
            $response->set_header("Vary: {$response->get_header('Vary')}, {$header}");
        }

        return $response;
    }
}