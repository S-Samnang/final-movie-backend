<a href="{{ url('admin/movie/sync-actors/' . $entry->getKey()) }}"
   class="btn btn-sm btn-outline-primary"
   onclick="return confirm('Sync actors from TMDB?')"
>
    <i class="la la-users"></i> Sync Actors
</a>
