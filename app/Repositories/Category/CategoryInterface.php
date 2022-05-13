<?php


namespace App\Repositories\Category;


interface CategoryInterface
{
    public function create(array $data);

    public function getById(int $id);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function all(array $columns = [], bool $fetchPublishedOnly = false);

    public function paginate($perPage);

}
