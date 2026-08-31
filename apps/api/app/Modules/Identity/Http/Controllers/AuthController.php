<?php

namespace App\Modules\Identity\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Application\Data\LoginCredentials;
use App\Modules\Identity\Application\Data\RegisterUserData;
use App\Modules\Identity\Application\UseCases\GetCurrentUser;
use App\Modules\Identity\Application\UseCases\LoginUser;
use App\Modules\Identity\Application\UseCases\LogoutUser;
use App\Modules\Identity\Application\UseCases\RegisterUser;
use App\Modules\Identity\Http\Requests\LoginRequest;
use App\Modules\Identity\Http\Requests\RegisterRequest;
use App\Modules\Identity\Http\Resources\AuthUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUser $useCase): JsonResponse
    {
        $session = $useCase->handle(RegisterUserData::fromArray($request->validated()));

        return response()->json([
            'token' => $session->token,
            'user' => new AuthUserResource($session->user)
        ], 201);
    }

    public function login(LoginRequest $request, LoginUser $useCase): array
    {
        $session = $useCase->handle(LoginCredentials::fromArray($request->validated()));

        return ['token' => $session->token, 'user' => new AuthUserResource($session->user)];
    }

    public function me(Request $request, GetCurrentUser $useCase): array
    {
        return ['user' => new AuthUserResource($useCase->handle($request->user()->getAuthIdentifier()))];
    }

    public function logout(Request $request, LogoutUser $useCase): Response
    {
        $useCase->handle($request->user()->getAuthIdentifier(), $request->user()->currentAccessToken()?->id);

        return response()->noContent();
    }
}
