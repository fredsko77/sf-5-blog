<?php
namespace App\Services;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;

interface PostServicesInterface
{

    /**
     * @param Request $request
     *
     * @return PaginationInterface
     */
    public function adminPost(Request $request): PaginationInterface;

}
