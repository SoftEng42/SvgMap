<?php

namespace App\Http\Controllers\Api;

use Imagick;
use ImagickDraw;
use ImagickPixel;
use DOMDocument;
use App\Models\SvgFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FileController extends Controller
{
    private $m_preId;
    private $m_ImagickDraw;
    //
    /**
     * upload file
     *
     * @return mixed
     */
    public function upload(Request $request)
    {
        $path = $request->file('file')->store('svg');
        $svg_path = Storage::path($path);
        $im = new Imagick($svg_path);
        $svg_path = pathinfo($path);
        $host = $request->getHttpHost();
        $avata_path = './storage/' . $svg_path['filename'] . ".png";
        $im->writeImage($avata_path);
        $height = $im->getImageheight();
        $width = $im->getImageWidth();
        $file = SvgFile::create([
            'path' => $path,
            'avata_path' => $avata_path,
            'name' => $request->name,
            'width' => $width,
            'height' => $height
        ]);
        $im->clear();
        $im->destroy();
        return response([
            "id" => $file->id,
            "avata_path"=> $avata_path,
            "name" => $request->name,
            "width" => $width,
            "height" => $height
        ], 200);
    }

    public function list(Request $request) {
        $svgLists = SvgFile::all();
        return response(['lists' => $svgLists], 200);
    }

    public function delete(Request $request) {
        $id = $request->id;
        $item = SvgFile::where('id', $id);
        if ($item)
            $item->delete();
        return response(['deleted' => $item], 200);
    }

    public function image(Request $request) {
        $id = $request->id;
        $clientRect = json_decode($request->clientRect);
        $scale = $request->scale;
        $item = SvgFile::where('id', $id)->first();
        $path = Storage::path($item->path);

        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->load($path);
        $svg = $dom->documentElement;

        $view_box = implode(' ', [$clientRect->x / 3.78, $clientRect->y / 3.78, $clientRect->width / 3.78, $clientRect->height / 3.78]);
        $svg->setAttribute('viewBox', $view_box);

        $svg->setAttribute('width', $clientRect->width * $scale);
        $svg->setAttribute('height', $clientRect->height * $scale);
        $svg_path = './storage/new.svg';
        $dom->save($svg_path);

        $im = new Imagick($svg_path);
        $png_path = './storage/'.time().'_new.png';
        // $im->scaleImage($clientRect->width * $scale, $clientRect->height * $scale);
        $im->writeImage($png_path);
        return response(['url' => $png_path], 200);
    }
}
