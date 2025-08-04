<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <nav>
        {{ auth()->user()->name }}
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </nav>
</body>
</html>