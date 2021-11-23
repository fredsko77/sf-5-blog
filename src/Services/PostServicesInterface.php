<?php
namespace App\Services;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

interface PostServicesInterface
{

    /**
     * @param Request $request
     *
     * @return PaginationInterface
     */
    public function adminPost(Request $request): PaginationInterface;

    /**
     * @param FormInterface $form
     * @param UploadedFile|null $image
     * 
     * @return [type]
     */
    public function save(FormInterface $form, ?UploadedFile $image = null);

}
