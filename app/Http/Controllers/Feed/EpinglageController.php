<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;

class EpinglageController extends Controller
{
    use AuthorizesRequests;

    public function store(Publication $publication): RedirectResponse
    {
        $this->authorize('epingler', $publication);

        $publication->update(['epingle_le' => now()]);

        return back()->with('succes', 'Publication épinglée en haut du fil.');
    }

    public function destroy(Publication $publication): RedirectResponse
    {
        $this->authorize('epingler', $publication);

        $publication->update(['epingle_le' => null]);

        return back()->with('succes', 'Publication désépinglée.');
    }
}
