<?php
namespace App\Services;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;

interface UserServicesInterface
{

    /**
     * @param Request $request
     *
     * @return array $data
     */
    public function adminIndex(Request $request): PaginationInterface;

}
