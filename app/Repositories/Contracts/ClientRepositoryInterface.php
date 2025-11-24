<?php

namespace App\Repositories\Contracts;

use App\Models\Client;
use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Summary of ClientRepositoryInterface
 * ambil semua fungsi yang ada di baserepositoryinterface
 */
interface ClientRepositoryInterface extends BaseRepositoryInterface{
    // ambil semua data client + fitur search
    public function getAllClient(?string $search = null) :mixed;
    
}