<?php
namespace App\Services;

use App\Repository\CommentRepository;
use App\Services\CommentServicesInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class CommentServices implements CommentServicesInterface
{

    /**
     * @var CommentRepository $repository
     */
    private $repository;

    /**
     * @var EntityManagerInterface $manager
     */
    private $manager;

    /**
     * @var Security $security
     */
    private $security;

    /**
     * @var PaginatorInterface $paginator
     */
    private $paginator;

    public function __construct(CommentRepository $repository, EntityManagerInterface $manager, Security $security, PaginatorInterface $paginator)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->security = $security;
        $this->paginator = $paginator;
    }

    /**
     * @param Request $request
     *
     * @return PaginationInterface
     */
    public function adminComment(Request $request): PaginationInterface
    {
        $page = $request->query->getInt('page', 1);
        $items = $request->query->get('items', 15);

        return $this->paginator->paginate(
            $this->repository->adminComment(),
            $page,
            $items
        );
    }

}
