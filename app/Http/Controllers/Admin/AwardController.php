<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\Player;
use App\Services\Season;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    public function index(Request $request)
    {
        $year = Season::resolve($request->query('year'));

        return view('admin.awards', [
            'year' => $year,
            'awards' => $this->list($year),
            'players' => $this->players($year),
            'types' => collect(Award::TYPES)->map(fn ($label, $key) => ['value' => $key, 'label' => $label])->values()->all(),
            'months' => collect(Season::MONTHS)->map(fn ($name, $m) => ['value' => $m, 'label' => $name])->values()->all(),
            'month' => (int) date('n'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'type' => ['required', 'in:'.implode(',', array_keys(Award::TYPES))],
            'file' => ['required', 'image', 'max:8192'],
        ]);

        $year = (int) $data['year'];

        $exists = Award::forYear($year)
            ->where('month', $data['month'])
            ->where('type', $data['type'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'An award of this type already exists for that month.',
            ], 422);
        }

        $file = $request->file('file');

        Award::create([
            'year' => $year,
            'month' => (int) $data['month'],
            'type' => $data['type'],
            'file' => file_get_contents($file->getRealPath()),
            'filename' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Award '.$file->getClientOriginalName().' uploaded.',
            'awards' => $this->list($year),
        ]);
    }

    /** Replace the image of an existing award. */
    public function update(Request $request, Award $award)
    {
        $request->validate(['file' => ['required', 'image', 'max:8192']]);

        $file = $request->file('file');
        $award->update([
            'file' => file_get_contents($file->getRealPath()),
            'filename' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Award image replaced.',
            'awards' => $this->list($award->year),
        ]);
    }

    /** Assign the award to a set of players; players not listed lose it. */
    public function assign(Request $request, Award $award)
    {
        $data = $request->validate([
            'players' => ['present', 'array'],
            'players.*' => ['integer', 'exists:players,id'],
        ]);

        // Only the admin award may be held by more than one player; every other
        // award belongs to exactly one finishing place.
        if ($award->type !== 'admin' && count($data['players']) > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Only the admin award can be assigned to several players.',
            ], 422);
        }

        // Never touch players of a different season.
        $ids = Player::forYear($award->year)->whereIn('id', $data['players'])->pluck('id')->all();
        $award->players()->sync($ids);

        return response()->json([
            'success' => true,
            'message' => 'Award '.$award->label.' assigned to '.count($ids).' player(s).',
            'awards' => $this->list($award->year),
        ]);
    }

    public function destroy(Award $award)
    {
        $year = $award->year;
        $label = $award->label;
        $award->players()->detach();
        $award->delete();

        return response()->json([
            'success' => true,
            'message' => "Award $label deleted.",
            'awards' => $this->list($year),
        ]);
    }

    private function list(int $year): array
    {
        return Award::forYear($year)
            ->with('players:id,playername')
            ->orderByDesc('month')
            ->orderBy('type')
            ->get()
            ->map(fn (Award $a) => [
                'id' => $a->id,
                'year' => $a->year,
                'month' => $a->month,
                'month_name' => $a->month ? Season::monthName($a->month) : '',
                'type' => $a->type,
                'label' => $a->label,
                'filename' => $a->filename,
                'url' => $a->url,
                'players' => $a->players->map(fn ($p) => ['id' => $p->id, 'playername' => $p->playername])->all(),
            ])->all();
    }

    private function players(int $year): array
    {
        return Player::forYear($year)
            ->orderBy('playername')
            ->get(['id', 'playername'])
            ->map(fn ($p) => ['id' => $p->id, 'playername' => $p->playername])
            ->all();
    }
}
