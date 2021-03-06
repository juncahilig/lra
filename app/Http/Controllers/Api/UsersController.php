<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UsersController extends Controller
{
    /**
     * List all resource.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        return response()->json($this->paginatedQuery($request));
    }

    /**
     * Store a new resource.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function store(Request $request) : JsonResponse
    {
        $request->validate([
            'type' => 'required|in:superuser,user',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',

            'gender' => 'nullable|in:female,male',
            'birthdate' =>
                'nullable|date:Y-m-d|before:'.now()->subYear(10)->format('Y-m-d'),
            'address' => 'nullable|string|max:510',

            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'username' => 'nullable|unique:users'
        ]);

        $user = User::create([
            'type' => $request->input('type'),
            'firstname' => ($firstname = $request->input('firstname')),
            'middlename' => ($middlename = $request->input('middlename')),
            'lastname' => ($lastname = $request->input('lastname')),

            'gender' => $request->input('gender'),
            'birthdate' => $request->input('birthdate'),
            'address' => $request->input('address'),

            'name' => "{$firstname} {$middlename} {$lastname}",

            'email' => $request->input('email'),
            'username' => $request->input('username'),
        ]);

        return response()->json($user, 201);
    }

    /**
     * Show a resource.
     *
     * @param Illuminate\Http\Request $request
     * @param App\User $user
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function show(Request $request, User $user) : JsonResponse
    {
        return response()->json($user);
    }

    /**
     * Update a resource.
     *
     * @param Illuminate\Http\Request $request
     * @param App\User $user
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user) : JsonResponse
    {
        return response()->json($user);
    }

    /**
     * Destroy a resource.
     *
     * @param Illuminate\Http\Request $request
     * @param App\User $user
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, User $user) : JsonResponse
    {
        $user->delete();

        return response()->json($this->paginatedQuery($request));
    }

    /**
     * Restore a resource.
     *
     * @param Illuminate\Http\Request $request
     * @param string $id
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function restore(Request $request, $id)
    {
        $user = User::withTrashed()->where('id', $id)->first();
        $user->deleted_at = null;
        $user->update();

        return response()->json($this->paginatedQuery($request));
    }

    /**
     * Get the paginated resource query.
     *
     * @param Illuminate\Http\Request
     *
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    protected function paginatedQuery(Request $request) : LengthAwarePaginator
    {
        $users = User::orderBy(
            $request->input('sortBy') ?? 'firstname',
            $request->input('sortType') ?? 'ASC'
        );

        if ($type = $request->input('type')) {
            $this->filter($users, 'type', $type);
        }

        if ($name = $request->input('name')) {
            $this->filter($users, 'name', $name);
        }

        if ($email = $request->input('email')) {
            $this->filter($users, 'email', $email);
        }

        return $users->paginate($request->input('perPage') ?? 10);
    }

    /**
     * Filter a specific column property
     *
     * @param mixed $users
     * @param string $property
     * @param array $filters
     *
     * @return void
     */
    protected function filter($users, string $property, array $filters)
    {
        foreach ($filters as $keyword => $value) {
            // Needed since LIKE statements requires values to be wrapped by %
            if (in_array($keyword, ['like', 'nlike'])) {
                $users->where(
                    $property,
                    _to_sql_operator($keyword),
                    "%{$value}%"
                );

                return;
            }

            $users->where($property, _to_sql_operator($keyword), "{$value}");
        }
    }
}
