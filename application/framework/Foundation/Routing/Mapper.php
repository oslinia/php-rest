<?php

namespace Framework\Foundation\Routing;

use Framework\Http\Path;

class Mapper
{
    protected static array $controller = array();
    protected static bool $flag;
    protected static array $tmp;
    protected static array $urls;
    protected string $routing;

    protected function caching(string $routing): void
    {
        mkdir($routing);

        $patterns = $masks = $urls = array();

        foreach (self::$tmp as $path => $items) {
            [$name, $size] = $items;

            $pattern = '/^' . str_replace('/', '\/', $path) . '$/';

            $masks[$name][$size] = null;

            0 === $size || [$masks[$name][$size], $pattern] = [
                $items[2],
                str_replace(array_keys($items[3]), array_values($items[3]), $pattern),
            ];

            $patterns[$pattern] = $name;

            $urls[$name][$size] = [$path, $pattern];
        }

        foreach (['patterns' => $patterns, 'masks' => $masks, 'urls' => $urls] as $name => $value) {
            $f = fopen($this->routing . $name . '.php', 'w');
            fwrite($f, '<?php return ' . var_export($value, true) . ';');
            fclose($f);
        }
    }

    private function callback(string $name, array $matches): mixed
    {
        [$class, $method, $middleware] = self::$controller[$name];

        $patterns = array_slice($matches, 1);

        $masks = (require $this->routing . 'masks.php')[$name];

        if (0 < ($size = count($patterns)) and isset($masks[$size])) {
            $tokens = array();

            foreach ($patterns as $key => $pattern)
                $tokens[$masks[$size][$key]] = $pattern;

            array_unshift($middleware, new Path($tokens));
        }

        return new $class()->$method(...$middleware);
    }

    protected function response(string $path): mixed
    {
        self::$urls = require $this->routing . 'urls.php';

        foreach (require $this->routing . 'patterns.php' as $pattern => $name)
            if (preg_match($pattern, $path, $matches))
                if (isset(self::$controller[$name]))
                    return $this->callback($name, $matches);

        return ['Not Found', 404, null, 'ASCII'];
    }
}