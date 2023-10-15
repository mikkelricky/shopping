<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(private readonly RequestStack $requestStack, private readonly RouterInterface $router)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('current_path', $this->currentPath(...), ['is_safe' => ['all']]),
            new TwigFunction('icon', $this->icon(...), ['is_safe' => ['all']]),
            new TwigFunction('path_with_referer', $this->getPathWithReferer(...)),
            new TwigFunction('path_from_referer', $this->getPathFromReferer(...)),
        ];
    }

    public function currentPath(array $params = []): string
    {
        $request = $this->requestStack->getCurrentRequest();

        return $this->router->generate(
            $request->attributes->get('_route'),
            array_merge($request->attributes->get('_route_params'), $request->query->all(), $params)
        );
    }

    public function icon($name = 'stroopwafel'): string
    {
        return '<span class="fas fa-'.$name.'"></span>';
    }

    public function getPathWithReferer(string $route, array $parameters = []): string
    {
        if (!isset($parameters['referer'])) {
            $request = $this->requestStack->getCurrentRequest();
            $parameters['referer'] = $this->router->generate(
                $request->get('_route'),
                array_merge($request->get('_route_params'), $request->query->all())
            );
        }

        return $this->router->generate($route, $parameters);
    }

    /**
     * @return scalar|null
     */
    public function getPathFromReferer(string $defaultPath)
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request->query->get('referer', $defaultPath);
    }
}
