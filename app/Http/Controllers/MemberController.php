<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class MemberController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return JsonResource::collection(Member::all());
    }

    public function show(string $id): JsonResource|Response
    {
        try{
            return new JsonResource(Member::findOrFail($id));
        } catch (Exception) {
            return response(null, Response::HTTP_NOT_FOUND);
        }
    }

    public function store(StoreMemberRequest $request): JsonResource
    {
        $member = Member::create($request->all());
        return new JsonResource($member);
    }
}
