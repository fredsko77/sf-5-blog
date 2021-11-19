<?php
namespace App\Services;

use App\Repository\PostRepository;
use App\Services\PostServicesInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class PostServices implements PostServicesInterface
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
     * @var PostRepository $repository
     */
    private $repository;

    /**
     * @var PaginatorInterface $paginator
     */
    private $paginator;

    public function __construct(Security $security, EntityManagerInterface $manager, PostRepository $repository, PaginatorInterface $paginator)
    {
        $this->security = $security;
        $this->manager = $manager;
        $this->repository = $repository;
        $this->paginator = $paginator;
    }

    /**
     * @param Request $request
     *
     * @return PaginationInterface
     */
    public function adminPost(Request $request): PaginationInterface
    {
        $search = $request->query->get('search', null);
        $state = $request->query->get('state', null);
        $page = $request->query->getInt('page', 1);
        $items = $request->query->get('items', 15);
        $sort = $request->query->get('sortBy', null);
        $state = $request->query->get('state', null);

        return $this->paginator->paginate(
            $this->repository->adminPost($search, $sort, $state),
            $page,
            $items
        );
    }

}
