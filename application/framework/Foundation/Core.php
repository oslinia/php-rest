<?php

namespace Framework\Foundation;

function template_buffer(): string
{
    extract(Core::$context);

    ob_start();

    require Core::$template;

    return ob_get_clean();
}

class Core extends Routing\Mapper
{
    public static null|string $encoding = null;
    public static array|null $context = null;
    public static string $url = '/static/';
    public static string $lang = 'en';
    public static string $resource;
    public static string $src;
    public static array $urls;
    public static string $template;

    public static function url(array $args): null|string
    {
        $name = array_shift($args);

        if (isset(self::$urls[$name])) {
            $link = self::$urls[$name];

            $size = count($args);

            if (isset($link[$size])) {
                [$path, $pattern] = $link[$size];

                foreach ($args as $mask => $value)
                    $path = str_replace('{' . $mask . '}', $value, $path);

                if (preg_match($pattern, $path, $matches))
                    return $matches[0];
            }
        }

        return null;
    }

    public static function media(string $name): null|string
    {
        $filename = Core::$resource . 'media' . DIRECTORY_SEPARATOR . $name;

        if (is_file($filename)) {
            parent::$flag = true;

            return $filename;
        }

        return null;
    }

    public static function template(string $name, int|null $code, null|string $mimetype): array|null
    {
        self::$template = Core::$src . 'template' . DIRECTORY_SEPARATOR . $name;

        if (is_file(self::$template)) {
            $context = ['lang' => self::$lang];

            self::$context = is_null(self::$context) ? $context : array_merge($context, self::$context);

            return [template_buffer(), $code, $mimetype];
        }

        return null;
    }
}