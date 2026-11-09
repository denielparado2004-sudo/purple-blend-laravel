@extends('layouts.admin')
@section('title', 'Admin accounts')
@section('content')
<p class="eyebrow">STAFF ACCESS</p><h1>Administrator accounts</h1>
<details class="form-card product-create"><summary class="button">Add administrator</summary><form method="post" action="{{ route('admin.accounts.store') }}" class="form-stack">@csrf
    <div class="form-row"><label>Username<input name="username" required maxlength="50"></label><label>Role<select name="role"><option value="staff">Staff</option><option value="manager">Manager</option></select></label></div>
    <div class="form-row"><label>First name<input name="first_name" required maxlength="100"></label><label>Last name<input name="last_name" required maxlength="100"></label></div>
    <label>Email <span class="muted">(optional)</span><input type="email" name="email"></label><label>Temporary password<input type="password" name="password" minlength="8" required></label><label class="checkbox"><input type="checkbox" name="is_active" value="1" checked> Account active</label><button class="button">Create account</button>
</form></details>
<div class="table-wrap"><table><thead><tr><th>Administrator</th><th>Email</th><th>Role</th><th>Active</th><th>Update account</th></tr></thead><tbody>
@foreach($admins as $admin)<tr><td><strong>{{ $admin->username }}</strong><small class="block muted">{{ $admin->first_name }} {{ $admin->last_name }}</small></td><td>{{ $admin->email ?: '—' }}</td><td>{{ str($admin->role)->headline() }}</td><td>{{ $admin->is_active ? 'Yes' : 'No' }}</td><td><details><summary class="text-button">Edit</summary><form method="post" action="{{ route('admin.accounts.update', $admin) }}" class="mini-form">@csrf @method('PATCH')
    <label>Username<input name="username" value="{{ $admin->username }}" required></label><div class="form-row"><label>First name<input name="first_name" value="{{ $admin->first_name }}" required></label><label>Last name<input name="last_name" value="{{ $admin->last_name }}" required></label></div><label>Email<input type="email" name="email" value="{{ $admin->email }}"></label><label>Role<select name="role"><option value="staff" @selected($admin->role === 'staff')>Staff</option><option value="manager" @selected($admin->role === 'manager')>Manager</option></select></label><label>New password <span class="muted">(leave blank to keep current)</span><input type="password" name="password" minlength="8"></label><label class="checkbox"><input type="checkbox" name="is_active" value="1" @checked($admin->is_active)> Active</label><button class="button small">Save</button>
</form></details></td></tr>@endforeach
</tbody></table></div>
@endsection
