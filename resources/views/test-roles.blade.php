<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Users and Roles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .role-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            background-color: #e9ecef;
            margin-right: 5px;
            font-size: 12px;
        }
        .admin { background-color: #dc3545; color: white; }
        .finance { background-color: #28a745; color: white; }
        .audit { background-color: #17a2b8; color: white; }
        .validation { background-color: #ffc107; }
    </style>
</head>
<body>
    <h1>Test Users and Roles</h1>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Test Login</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="role-badge {{ $role->name }}">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('test.login-as', $user->id) }}">Login as this user</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No test users found. Run the TestUsersAndRolesSeeder first.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div>
        <h2>How to create these users:</h2>
        <pre>
            php artisan db:seed --class=TestUsersAndRolesSeeder
        </pre>
    </div>

    <div>
        <h2>Current Authentication Status:</h2>
        @if(Auth::check())
            <p>Logged in as: {{ Auth::user()->name }} ({{ Auth::user()->email }})</p>
            <p>Roles: 
                @foreach(Auth::user()->roles as $role)
                    <span class="role-badge {{ $role->name }}">{{ $role->name }}</span>
                @endforeach
            </p>
            <p>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </p>
        @else
            <p>Not logged in</p>
        @endif
    </div>
</body>
</html> 