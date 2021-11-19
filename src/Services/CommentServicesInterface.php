<?php
namespace App\Services;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;

interface CommentServicesInterface
{

    /**
     * @param Request $request
     *
     * @return PaginationInterface
     */
    public function adminComment(Request $request): PaginationInterface;

}
