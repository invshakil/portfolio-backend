<?php


namespace App\Repositories\Page;


use Illuminate\Http\Request;

interface PageInterface
{
    public function save(Request $request);

    public function update(Request $request, int $id);

    public function delete(int $id);

}
