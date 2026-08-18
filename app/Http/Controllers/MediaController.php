<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Player;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves the image blobs that live in the database (awards and player avatars).
 */
class MediaController extends Controller
{
    public function award(Request $request, Award $award): Response
    {
        abort_if($award->file === null || $award->file === '', 404);

        return $this->image($request, $award->file, $award->mime ?: 'image/png');
    }

    public function avatar(Request $request, Player $player): Response
    {
        abort_unless($player->hasAvatar() && $player->avatar, 404);

        return $this->image($request, $player->avatar, $this->normaliseMime($player->avatar_mime));
    }

    private function image(Request $request, string $blob, string $mime): Response
    {
        $etag = '"'.md5($blob).'"';

        if (trim($request->headers->get('If-None-Match', ''), 'W/') === $etag) {
            return response('', 304);
        }

        return response($blob, 200, [
            'Content-Type' => $mime,
            'Content-Length' => strlen($blob),
            'Cache-Control' => 'public, max-age=86400',
            'ETag' => $etag,
        ]);
    }

    /** The legacy data stores bare extensions such as "jpg" instead of a mime type. */
    private function normaliseMime(?string $mime): string
    {
        $mime = strtolower(trim((string) $mime));

        return match ($mime) {
            '', 'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => str_contains($mime, '/') ? $mime : 'image/'.$mime,
        };
    }
}
