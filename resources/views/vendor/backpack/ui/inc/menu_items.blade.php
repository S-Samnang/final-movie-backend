{{-- Dashboard --}}
<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('dashboard') }}">
        <i class="la la-home nav-icon"></i> Dashboard
    </a>
</li>

{{-- Custom Entities --}}
<x-backpack::menu-item title="Movies" icon="la la-film" :link="backpack_url('movie')" />
<x-backpack::menu-item title="Actors" icon="la la-user" :link="backpack_url('actor')" />
<x-backpack::menu-item title="Users" icon="la la-users" :link="backpack_url('user')" />
<x-backpack::menu-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />

<x-backpack::menu-item title="Ratings" icon="la la-star" :link="backpack_url('rating')" />

<x-backpack::menu-item title="Favorites" icon="la la-heart" :link="backpack_url('favorite')" />
