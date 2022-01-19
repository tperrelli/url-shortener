<?php

namespace App\Http\Controllers;

use App\Services\UrlService;
use App\Http\Requests\UrlRequest;
use App\Http\Resources\UrlResource;

class UrlController extends Controller
{   
    /** @var UrlService */
    protected $urlService;
    
    /**
     * class constructor
     * 
     * @param UrlService $urlService
     */
    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function index(UrlRequest $request)
    {
        if ($request->has('url')) {
            $url = $request->get('url');

            return new UrlResource($this->urlService->retrieveByShortUrl($url));
        }


        $urls = $this->urlService->getTop100();

        return UrlResource::collection($urls);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UrlRequest $request)
    {
        return new UrlResource($this->urlService->save($request->all()));
    }
}
