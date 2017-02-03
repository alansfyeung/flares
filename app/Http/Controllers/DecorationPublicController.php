<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Decoration;
use App\Http\Controllers\Controller;
use App\Http\Custom\ResponseCodes;

class DecorationPublicController extends Controller
{
    public function __invoke(Request $request, $shortcode)
    {


        $decoration = Decoration::where('shortcode', $shortcode)
            ->select('*')
            ->addSelect(DB::raw('COALESCE(precedence, 0) as adjusted_precedence'))
            ->firstOrFail();
        $prevDecoration = Decoration::where('tier', $decoration->tier)
            ->where('precedence', '>', $decoration->adjusted_precedence)
            ->first();
        $nextDecoration = Decoration::where('tier', $decoration->tier)
            ->where('precedence', '<', $decoration->adjusted_precedence)
            ->first();

        return view('public.decoration', [
            'dec' => $decoration,
            'decBadgeUrl' => route('media::decoration-badge', ['decorationId' => $decoration->dec_id]),
            'prevDec' => $prevDecoration,
            'nextDec' => $nextDecoration,
        ]);
    }
}
