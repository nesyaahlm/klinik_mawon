<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Myth\Auth\Filters\BaseFilter;

class LoginFilter extends BaseFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        foreach ($this->reservedRoutes as $reservedRoute) {
            if (url_is(route_to($reservedRoute))) {
                return;
            }
        }
        if (! $this->authenticate->check()) {
            session()->set('redirect_url', current_url());
            return redirect($this->reservedRoutes['login']);
        }

    

        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
