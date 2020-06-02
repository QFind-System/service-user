<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Service\Pagination\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * @var PaginationService
     */
    private $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        parent::__construct();
        $this->paginationService = $paginationService;
    }

    public function index(Request $request): View
    {
        $email = ($request->get('email')) ?? $request->get('email');
        $status = ($request->get('status')) ?? $request->get('status');
        $role = ($request->get('role')) ?? $request->get('role');
        $page = ($request->get('page')) ? $request->get('page') : 1;

        $response = Http::get($this->apiHost . '/users' . '?email=' . $email . '&status=' . $status . '&role=' . $role . '&page=' . $page);
        $paginationArray = $this->paginationService->build($response->json()['totalUsers']);

        return view('admin.user.index',
            [
                'users' => json_decode($response->json()['users']),
                'statusList' => $response->json()['statusList'],
                'rolesList' => $response->json()['rolesList'],
                'paginationArray' => $paginationArray
            ]
        );
    }
}
