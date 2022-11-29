<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class PaginationService{

    private $entityClass; // l'entité sur laquelle on doit faire la pagination
    private $limit = 10;
    private $currentPage = 1;
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function setEntityClass($entityClass): self
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setPage(int $page): self
    {
        $this->currentPage = $page;
        return $this;
    }

    public function getPage(): int
    {
        return $this->currentPage;
    }

    public function getData()
    {
        // calculer l'offset
        $offset =  $this->currentPage * $this->limit - $this->limit;
        // demander au repository de trouver les élements
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([],[],$this->limit,$offset);

        // renvoyer les données
        return $data;
    }

    public function getPages()
    {
        // connaître le total des enregistrement de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
        $pages = ceil($total / $this->limit);

        return $pages;
    }




}