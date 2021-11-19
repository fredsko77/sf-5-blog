<?php
namespace App\Services;

use App\Repository\UserRepository;
use App\Services\UserServicesInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Knp\Component\Pager\Pagination\PaginationInterface;

class UserServices implements UserServicesInterface
{

    /**
     * @var Security $security
     */
    private $security;

    /**
     * @var EntityManagerInterface $manager
     */
    private $manager;

    /**
     * @var PaginatorInterface $paginator
     */
    private $paginator;

    /**
     * @var UserRepository $repository
     */
    private $repository;

    public function __construct(Security $security, EntityManagerInterface $manager, PaginatorInterface $paginator, UserRepository $repository)
    {
        $this->security = $security;
        $this->manager = $manager;
        $this->paginator = $paginator;
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     *
     * @return array $data
     */
    public function adminIndex(Request $request): PaginationInterface
    {
        $search = $request->query->get('search', null);
        $page = $request->query->getInt('page', 1);
        $items = $request->query->get('items', 15);
        $sort = $request->query->get('sortBy', 'id');

        return $this->paginator->paginate(
            $this->repository->adminUser($search, $sort),
            $page,
            $items
        );
    }

}
