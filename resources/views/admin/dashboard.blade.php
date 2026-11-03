<h1>Dashboard Admin</h1>
<p>Selamat datang, Admin!</p>

<form action="/logout" method="POST">
    @csrf
    <button type="submit">Logout</button>
</form>