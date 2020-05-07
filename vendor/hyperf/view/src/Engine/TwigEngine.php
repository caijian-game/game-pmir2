<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\View\Engine;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigEngine implements EngineInterface
{
    public function render($template, $data, $config): string
    {
        $loader = new FilesystemLoader($config['view_path']);
        $twig = new Environment($loader, ['cache' => $config['cache_path']]);

        return $twig->render($template, $data);
    }
}
