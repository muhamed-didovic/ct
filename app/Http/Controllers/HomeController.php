<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    
    public function gradient()
    {
        $data = 'https://www.youtube.com/watch?v=DLzxrzFCyOs&t=42s';
    
        $options = new QROptions([
            'version'      => 7,
            'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'     => QRCode::ECC_L,
            'scale'        => 5,
            'addQuietzone' => true,
            'cssClass'     => 'my-css-class',
            'svgOpacity'   => 1.0,
            'svgDefs'      => '
		<linearGradient id="g1">
			<stop offset="0%" stop-color="#F3F" />
			<stop offset="100%" stop-color="#39F" />
		</linearGradient>
		<style>rect{shape-rendering:crispEdges}</style>',
            'moduleValues' => [
                // finder
                1536 => 'url(#g1)', // dark (true)
                6    => '#fff', // light (false)
                // alignment
                2560 => 'url(#g1)',
                10   => '#fff',
                // timing
                3072 => 'url(#g1)',
                12   => '#fff',
                // format
                3584 => 'url(#g1)',
                14   => '#fff',
                // version
                4096 => 'url(#g1)',
                16   => '#fff',
                // data
                1024 => 'url(#g1)',
                4    => '#fff',
                // darkmodule
                512  => 'url(#g1)',
                // separator
                8    => '#fff',
                // quietzone
                18   => '#fff',
            ],
        ]);
        $qrcode = new QRCode($options);
        
        return view('gradient', compact('qrcode'));
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
}
