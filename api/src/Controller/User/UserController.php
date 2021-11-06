<?php

namespace App\Controller\User;

use App\Exception\DbException;
use App\Service\User\PermissionService;
use App\Service\User\UserService;
use App\Validation\User\CreateUserValidation;
use App\Validation\User\UpdateUserValidation;
use App\Validation\User\ChangeUserPasswordValidation;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use OpenApi\Annotations as OA;

/**
 * @Route("/users")
 * @OA\Tag(name="User")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService, PermissionService $permissionService)
    {
        $this->userService = $userService;
        $this->permissionService = $permissionService;
    }

    /**
     * @Route("/",  methods={"GET"})
     * Get all users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        $email = $request->query->get('email');
        $status = $request->query->get('status');
        $role = $request->query->get('role');
        $page = $request->query->get('page');

        try {
            $users = $this->userService->all($email, $status, $role, $page);
            $totalUsers = $this->userService->totalUsers($email, $status, $role);
            return new JsonResponse(
                [
                    'users' => $users,
                    'totalUsers' => $totalUsers
                ],  Response::HTTP_OK
            );
        } catch (DbException $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}",  methods={"GET"})
     * Get single users
     *
     * @param int $id
     * @return JsonResponse
     */
    public function single($id): JsonResponse
    {
        try {
            $user = $this->userService->single($id);
            return new JsonResponse(['user' => $user], Response::HTTP_OK);
        } catch(NotFoundHttpException $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route("/",  methods={"POST"})
     * Create users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $violations = (new CreateUserValidation())->validate($data);
        if ($violations->count() > 0) {
            return new JsonResponse(["error" => (string)$violations], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->userService->create($data);
            return new JsonResponse(['message' => "Created successful"], Response::HTTP_CREATED);
        } catch(\InvalidArgumentException $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}",  methods={"PUT"})
     * Update users
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $violations = (new UpdateUserValidation())->validate($data);
        if ($violations->count() > 0) {
            return new JsonResponse(["error" => (string)$violations], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->userService->update($data, $id);
            return new JsonResponse(['message' => "Updated successful"], Response::HTTP_OK);
        } catch(NotFoundHttpException $e) {
                return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch(\InvalidArgumentException $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (DBALException $e) {
            return new JsonResponse(["error" => "User who has this email already exists."], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}",  methods={"DELETE"})
     * Delete user
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $this->userService->delete($id);
            return new JsonResponse(['message' => "User was deleted"], Response::HTTP_OK);
        } catch(NotFoundHttpException $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route("/{id}/change-password",  methods={"PUT"})
     * Update user password
     *
     * @return JsonResponse
     */
    public function updatePassword(Request $request, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $violations = (new ChangeUserPasswordValidation())->validate($data);
        if ($violations->count() > 0) {
            return new JsonResponse(["error" => (string)$violations], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->userService->changePassword($data, $id);
            return new JsonResponse(['message' => "Changed successful"], Response::HTTP_OK);
        } catch(NotFoundHttpException $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch(\InvalidArgumentException $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}/assign-permission/{permissionId}",  methods={"GET"})
     * Assign permission to user
     *
     * @param $id
     * @param $permissionId
     * @return JsonResponse
     */
    public function assignPermission($id, $permissionId): JsonResponse
    {
        try {
            $permission = $this->permissionService->getPermissionObj($permissionId);
            $this->userService->assignPermission($id, $permission);
            return new JsonResponse(['message' => "Permission was assigned successfull"], Response::HTTP_OK);
        } catch(NotFoundHttpException $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route("/{id}/remove-permission/{permissionId}",  methods={"GET"})
     * Remove permission to user
     *
     * @param $id
     * @param $permissionId
     * @return JsonResponse
     */
    public function removePermission($id, $permissionId): JsonResponse
    {
        try {
            $permission = $this->permissionService->getPermissionObj($permissionId);
            $this->userService->removePermission($id, $permission);
            return new JsonResponse(['message' => "Permission was removed successfull"], Response::HTTP_OK);
        } catch(NotFoundHttpException $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route("/{id}/activate",  methods={"GET"})
     * Activate user
     *
     * @param int $id
     * @return JsonResponse
     */
    public function activate(int $id): JsonResponse
    {
        try {
            $this->userService->activate($id);
            return new JsonResponse(['message' => "User was activated"], Response::HTTP_OK);
        } catch(NotFoundHttpException $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route("/{id}/block",  methods={"GET"})
     * Block user
     *
     * @param int $id
     * @return JsonResponse
     */
    public function block(int $id): JsonResponse
    {
        try {
            $this->userService->block($id);
            return new JsonResponse(['message' => "User was blocked"], Response::HTTP_OK);
        } catch(NotFoundHttpException $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
