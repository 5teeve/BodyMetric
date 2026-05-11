<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Check common session keys used for admin identification.
        $isAdmin = false;

        if ($session->has('is_admin')) {
            $val = $session->get('is_admin');
            if ($val === true || $val === 1 || $val === '1') {
                $isAdmin = true;
            }
        }

        if (!$isAdmin && $session->has('role')) {
            if ((string) $session->get('role') === 'admin') {
                $isAdmin = true;
            }
        }

        if (!$isAdmin) {
            return redirect()->to('/');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
