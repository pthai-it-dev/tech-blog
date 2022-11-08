<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\UserDTO;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Http\Requests\Admin\Auth\LoginPostRequest;
use App\Http\Requests\Admin\Auth\LogoutPostRequest;
use function App\Helpers\failedResponse;
use function App\Helpers\successfulResponse;
use const App\Helpers\ROLES;
use const App\Helpers\HTTP_STATUS_CODE_UNAUTHENTICATED;

class AuthController extends Controller
{
    private UserDTO $userDTO;

    /**
     * @param UserDTO $userDTO
     */
    public function __construct (UserDTO $userDTO) { $this->userDTO = $userDTO; }

    public function login (LoginPostRequest $request) : Response|Application|ResponseFactory
    {
        if (auth()->attempt(array_merge($request->validated(),
                                        ['role' => Arr::only(ROLES,
                                                             ['admin', 'mode'])])))
        {
            $accessToken = auth()->user()->createToken('access_token')->plainTextToken;
            $data        = [
                'user'        => $this->userDTO->formatLogin(auth()->user()),
                'accessToken' => $accessToken,
            ];

            return successfulResponse($data);
        }

        return failedResponse([], '', HTTP_STATUS_CODE_UNAUTHENTICATED);
    }

    public function logout (LogoutPostRequest $request) : Response|Application|ResponseFactory
    {
        $user = auth()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return successfulResponse();
    }

    public function logoutAll (LogoutPostRequest $request) : Response|Application|ResponseFactory
    {
        auth()->user()->tokens()->delete();
        return successfulResponse();
    }
}
